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

class MvcCoreExt_Auth_Controller extends MvcCoreExt_Auth_Abstract_Controller {
	/**
	 * Authentication form submit action to sign in.
	 * Routed by route configured by:
	 * MvcCoreExt_Auth::GetInstance()->SetSignInRoute();
	 * @return void
	 */
	public function SignInAction () {
		/** @var $form MvcCoreExt_Auth_SignInForm */ 
		$form = MvcCoreExt_Auth::GetInstance()->GetForm();
		list ($result, $data, $errors) = $form->Submit();
		if ($result !== SimpleForm::RESULT_SUCCESS) {
			// here you can count bad login requests 
			// to ban danger user for some time or anything else...

		}
		$form->ClearSession(); // to remove all submited data from session
		$form->RedirectAfterSubmit();
	}
	/**
	 * Authentication form submit action to sign out.
	 * Routed by route configured by: 
	 * MvcCoreExt_Auth::GetInstance()->SetSignOutRoute();
	 * @return void
	 */
	public function SignOutAction () {
		/** @var $form MvcCoreExt_Auth_SignOutForm */
		$form = MvcCoreExt_Auth::GetInstance()->GetForm();
		list ($result, $data, $errors) = $form->Submit();
		$form->ClearSession(); // to remove all submited data from session
		$form->RedirectAfterSubmit();
	}
}