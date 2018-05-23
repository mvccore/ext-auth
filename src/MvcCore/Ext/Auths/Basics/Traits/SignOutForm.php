<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Auths\Basics\Traits;

/**
 * Trait for class `\MvcCore\Ext\Auths\Basics\SignOutForm`. Trait contains:
 * - Protected property `$user` to display user full name in sign out form.
 * - `Init()` method to initialize all necessary sign in form fields.
 * - `Submit()` method to handle signin form submit request (`POST` by default).
 * - `Render()` method to render sign out form without template by default.
 */
trait SignOutForm
{
	/** @var \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser */
	protected $user = NULL;

	/**
	 * Initialize sign out button and user into
	 * template for any custom template rendering.
	 * @return \MvcCore\Ext\Auths\Basics\SignOutForm
	 */
	public function Init () {
		parent::Init();

		$this->initAuthFormPropsAndHiddenControls();

		$this->AddField(new \MvcCore\Ext\Form\SubmitButton(array(
			'name'			=> 'send',
			'value'			=> 'Log Out',
			'cssClasses'	=> array('button'),
		)));

		$this->user = $this->auth->GetUser();

		return $this;
	}

	/**
	 * Sign out submit - if everything is ok, unser user unique name from session
	 * for next requests to hanve not authenticated user anymore.
	 * @param array $rawParams
	 * @return array
	 */
	public function Submit ($rawParams = array()) {
		parent::Submit();
		if ($this->Result === \MvcCore\Ext\Form::RESULT_SUCCESS) {
			$userClass = $this->auth->GetUserClass();
			$userClass::LogOut();
		}
		$this->SetSuccessUrl($this->Data['successUrl']);
		$this->SetErrorUrl($this->Data['errorUrl']);
		return array($this->Result, $this->Data, $this->Errors);
	}

	/**
	 * Render form without custom template, without any errors, with
	 * user full name, sign out button and all necessary hidden fields.
	 * @return string
	 */
	public function Render () {
		$result = $this->RenderBegin();
		if ($this->user)
			$result .= '<span>'.$this->user->GetFullName().'</span>';
		foreach ($this->Fields as & $field)
			$result .= $field->Render();
		$result .= $this->RenderEnd();
		return $result;
	}
}
