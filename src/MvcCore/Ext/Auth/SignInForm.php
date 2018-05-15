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

class SignInForm extends \MvcCore\Ext\Form implements \MvcCore\Ext\Auth\Interfaces\ISignForm {

	use \MvcCore\Ext\Auth\Traits\SignForm;

	/** @var string */
	public $Id = 'authentication';

	/** @var string */
	public $CssClass = 'sign-in';

	/** @var string */
	public $Method = \MvcCore\Ext\Form::METHOD_POST;

	/**
	 * Initialize all form fields, initialize hidden field with
	 * sourceUrl for cases when in request params is any source url param.
	 * To return there after form is submitted.
	 * @return \MvcCore\Ext\Auth\SignInForm
	 */
	public function Init () {
		parent::Init();
		
		$this->addSuccessAndErrorUrlHiddenControls();
		
		$this->AddField(new Form\Text(array(
			'name'			=> 'username',
			'placeholder'	=> 'User',
		)));
		$this->AddField(new Form\Password(array(
			'name'			=> 'password',
			'placeholder'	=> 'Password',
		)));
		$this->AddField(new Form\SubmitButton(array(
			'name'			=> 'send',
			'value'			=> 'Sign In',
			'cssClasses'	=> array('button'),
		)));

		$params = \MvcCore\Application::GetInstance()->GetRequest()->GetParams();

		$sourceUrl = isset($params['sourceUrl'])
			? filter_var($params['sourceUrl'], FILTER_VALIDATE_URL)
			: '' ;
		$this->AddField(new Form\Hidden(array(
			'name'			=> 'sourceUrl',
			'value'			=> $sourceUrl ?: '',
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
		$userClass = Auth::GetInstance()->GetConfig()->userClass;
		if ($this->Result === Form::RESULT_SUCCESS) {
			// now sended values are safe strings,
			// try to get use by username and compare password hashes:
			$userClass = Auth::GetInstance()->GetConfig()->userClass;
			$user = $userClass::LogIn(
				$this->Data['username'], $this->Data['password']
			);
			if ($user === NULL)
				$this->AddError('User name or password is incorrect.');
		}
		$data = (object) $this->Data;
		$this->SuccessUrl = $data->sourceUrl
			? urldecode($data->sourceUrl)
			: $data->successUrl;
		$this->ErrorUrl = $data->errorUrl;
		if ($this->Result !== Form::RESULT_SUCCESS) sleep(3);
		return array(
			$this->Result,
			$this->Data,
			$this->Errors
		);
	}
}
