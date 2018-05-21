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

namespace MvcCore\Ext\Auth\Basic\Traits;

trait SignOutForm
{
	/** @var \MvcCore\Ext\Auth\Basic\User|\MvcCore\Ext\Auth\Basic\Interfaces\IUser */
	protected $user = NULL;

	/**
	 * Initialize sign out button and user into
	 * template for any custom template rendering.
	 * @return \MvcCore\Ext\Auth\Basic\SignOutForm
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
}
