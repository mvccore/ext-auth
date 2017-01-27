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

namespace MvcCore\Ext\Auth;

use \MvcCore\Ext\Auth,
	\MvcCore\Ext\Form;

class SignOutForm extends Virtual\Form {

	/** @var string */
	public $CssClass = 'sign-out';

	/** @var \MvcCore\Ext\Auth\Virtual\User */
	public $User = NULL;

	/**
	 * Initialize sign out button and user into 
	 * template for any custom template rendering.
	 * @return \MvcCore\Ext\Auth\SignOutForm
	 */
	public function Init () {
		parent::Init();

		$cfg = Auth::GetInstance()->GetConfig();
		$this->addSuccessAndErrorUrlHiddens($cfg->signedInUrl, $cfg->errorUrl);

		$this->AddField(new Form\SubmitButton(array(
			'name'			=> 'send',
			'value'			=> 'Log Out',
			'cssClasses'	=> array('button'),
		)));

		$this->User = Auth::GetInstance()->GetUser();

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
		if ($this->Result === Form::RESULT_SUCCESS) {
			$userClass = Auth::GetInstance()->GetConfig()->userClass;
			$userClass::ClearFromSession();
		}
		$this->SuccessUrl = $this->Data['successUrl'];
		$this->ErrorUrl = $this->Data['errorUrl'];
		return array($this->Result, $this->Data, $this->Errors);
	}
}