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
 * Trait for class `\MvcCore\Ext\Auths\Basics\SignInForm`. Trait contains:
 * - `Init()` method to initialize all necessary sign in form fields.
 * - `Submit()` method to handle signin form submit request (`POST` by default).
 */
trait SignInForm
{
	/**
	 * Initialize all form fields, initialize hidden field with
	 * sourceUrl for cases when in request params is any source url param.
	 * To return there after form is submitted.
	 * @return \MvcCore\Ext\Auths\Basics\SignInForm
	 */
	public function Init () {
		parent::Init();

		$this->initAuthFormPropsAndHiddenControls();

		$this->AddField(new \MvcCore\Ext\Form\Text(array(
			'name'			=> 'username',
			'placeholder'	=> 'User',
			'validators'	=> array('SafeString'),
		)));
		$this->AddField(new \MvcCore\Ext\Form\Password(array(
			'name'			=> 'password',
			'placeholder'	=> 'Password',
			'validators'	=> array('SafeString'),
		)));
		$this->AddField(new \MvcCore\Ext\Form\SubmitButton(array(
			'name'			=> 'send',
			'value'			=> 'Sign In',
			'cssClasses'	=> array('button'),
		)));

		$sourceUrl = $this->application->GetRequest()
			->GetParam('sourceUrl', '.*', '', 'string');
		$sourceUrl = filter_var(rawurldecode($sourceUrl), FILTER_VALIDATE_URL);

		$this->AddField(new \MvcCore\Ext\Form\Hidden(array(
			'name'			=> 'sourceUrl',
			'value'			=> rawurlencode($sourceUrl) ?: '',
			'validators'	=> array('Url'),
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
		if ($this->Result === \MvcCore\Ext\Form::RESULT_SUCCESS) {
			// now sended values are safe strings,
			// try to get use by username and compare password hashes:
			$userClass = $this->auth->GetUserClass();
			$user = $userClass::LogIn(
				$this->Data['username'], $this->Data['password']
			);
			if ($user !== NULL) {
				$user->SetPasswordHash(NULL);
			} else {
				$this->AddError(
					'User name or password is incorrect.',
					array('username', 'password')
				);
			}
		}
		$data = (object) $this->Data;
		$this->SetSuccessUrl($data->sourceUrl ? $data->sourceUrl : $data->successUrl);
		$this->SetErrorUrl($data->errorUrl);
		if ($this->Result !== \MvcCore\Ext\Form::RESULT_SUCCESS)
			sleep($this->auth->GetInvalidCredentialsTimeout());
		return array(
			$this->Result,
			$this->Data,
			$this->Errors
		);
	}
}
