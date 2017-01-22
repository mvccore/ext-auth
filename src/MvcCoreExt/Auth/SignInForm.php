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

class MvcCoreExt_Auth_SignInForm extends MvcCoreExt_Auth_Abstract_Form {

	/** @var string */
	public $CssClass = 'sign-in';

	/**
	 * Initialize all form fields, initialize hidden field with
	 * sourceUrl for cases when in request params is any source url param.
	 * To return there after form is submitted.
	 * @return MvcCoreExt_Auth_SignInForm
	 */
	public function Init () {
		parent::Init();
		
		$cfg = MvcCoreExt_Auth::GetInstance()->GetConfig();
		$this->addSuccessAndErrorUrlHiddens($cfg->signedInUrl, $cfg->errorUrl);

		$this->AddField(new SimpleForm_Text(array(
			'name'			=> 'username',
			'placeholder'	=> 'User',
		)));
		$this->AddField(new SimpleForm_Password(array(
			'name'			=> 'password',
			'placeholder'	=> 'Password',
		)));
		$this->AddField(new SimpleForm_SubmitButton(array(
			'name'			=> 'send',
			'value'			=> 'Sign In',
			'cssClasses'	=> array('button'),
		)));

		$params = MvcCore::GetInstance()->GetRequest()->Params;

		$sourceUrl = isset($params['sourceUrl']) ? $params['sourceUrl'] : '' ;
		$sourceUrl = filter_var($sourceUrl, FILTER_VALIDATE_URL);
		$this->AddField(new SimpleForm_Hidden(array(
			'name'			=> 'sourceUrl',
			'value'			=> $sourceUrl,
		)));

		return $this;
	}

	/**
	 * Sign in submit - if there is any user with the same password imprint
	 * store user in session for next requests, if there is not - wait for
	 * three seconds and then go to error page.
	 * @param array $rawParams
	 * @return array
	 */
	public function Submit ($rawParams = array()) {
		parent::Submit();
		$userClass = MvcCoreExt_Auth::GetInstance()->GetConfig()->userClass;
		if ($this->Result === SimpleForm::RESULT_SUCCESS) {
			// now sended values are safe strings, 
			// try to get use by username and compare password hashes:
			$user = $userClass::Authenticate(
				$this->Data['username'], $this->Data['password']
			);
			if (is_null($user)) {
				$this->AddError('User name or password is incorrect.');
			} else {
				$userClass::StoreInSession($user);
			}
		}
		$data = (object) $this->Data;
		$this->SuccessUrl = $data->sourceUrl ? urldecode($data->sourceUrl) : $data->successUrl;
		$this->ErrorUrl = $data->errorUrl;
		if ($this->Result !== SimpleForm::RESULT_SUCCESS) {
			sleep(3);
		}
		return array($this->Result, $this->Data, $this->Errors);
	}
}