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

trait SignInController
{
	/**
	 * Authentication form submit action to sign in.
	 * Routed by route configured by:
	 * MvcCore\Ext\Auth::GetInstance()->SetSignInRoute();
	 * @return void
	 */
	public function SignInAction () {
		/** @var $form \MvcCore\Ext\Auth\Basic\SignInForm */
		$form = \MvcCore\Ext\Auth\Basic::GetInstance()->GetForm();
		list ($result, $data, $errors) = $form->Submit();
		if ($result !== \MvcCore\Ext\Form::RESULT_SUCCESS) {
			// here you can count bad login requests
			// to ban danger user for some time or anything else...

		}
		$form->ClearSession(); // to remove all submited data from session
		$form->RedirectAfterSubmit();
	}
}
