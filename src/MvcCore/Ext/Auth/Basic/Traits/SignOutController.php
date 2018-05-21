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

trait SignOutController
{
	/**
	 * Authentication form submit action to sign out.
	 * Routed by route configured by:
	 * MvcCore\Ext\Auth::GetInstance()->SetSignOutRoute();
	 * @return void
	 */
	public function SignOutAction () {
		/** @var $form \MvcCore\Ext\Auth\Basic\SignOutForm */
		$form = \MvcCore\Ext\Auth\Basic::GetInstance()->GetForm();
		/*list ($result, $data, $errors) = */$form->Submit();
		$form->ClearSession(); // to remove all submited data from session
		$form->RedirectAfterSubmit();
	}
}
