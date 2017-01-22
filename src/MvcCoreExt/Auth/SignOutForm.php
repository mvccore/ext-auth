<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/3.0.0/LICENCE.md
 */

class MvcCoreExt_Auth_SignOutForm extends MvcCoreExt_Auth_Abstract_Form {

	/** @var string */
	public $CssClass = 'sign-out';

	/** @var MvcCoreExt_Auth_Abstract_User */
	public $User = NULL;

	/**
	 * Initialize sign out button and user into 
	 * template for any custom template rendering.
	 * @return MvcCoreExt_Auth_SignOutForm
	 */
	public function Init () {
		parent::Init();

		$cfg = MvcCoreExt_Auth::GetInstance()->GetConfig();
		$this->addSuccessAndErrorUrlHiddens($cfg->signedInUrl, $cfg->errorUrl);

		$this->AddField(new SimpleForm_SubmitButton(array(
			'name'			=> 'send',
			'value'			=> 'Log Out',
			'cssClasses'	=> array('button'),
		)));

		$this->User = MvcCoreExt_Auth::GetInstance()->GetUser();

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
		if ($this->Result === SimpleForm::RESULT_SUCCESS) {
			$userClass = MvcCoreExt_Auth::GetInstance()->GetConfig()->userClass;
			$userClass::ClearFromSession();
		}
		$this->SuccessUrl = $this->Data['successUrl'];
		$this->ErrorUrl = $this->Data['errorUrl'];
		return array($this->Result, $this->Data, $this->Errors);
	}
}