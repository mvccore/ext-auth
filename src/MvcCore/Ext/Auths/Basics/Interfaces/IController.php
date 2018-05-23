<?php

namespace MvcCore\Ext\Auths\Basics\Interfaces;

/**
 * Responsibility - handle configured signin form and signout form submit requests.
 */
interface IController
{
	/**
	 * Authentication form submit action to sign in.
	 * Routed by configured route by:
	 * `\MvcCore\Ext\Auths\Basic::GetInstance()->SetSignInRoute(...);`
	 * @return void
	 */
	public function SignInAction ();

	/**
	 * Authentication form submit action to sign out.
	 * Routed by configured route by:
	 * `\MvcCore\Ext\Auths\Basic::GetInstance()->SetSignOutRoute(...);`
	 * @return void
	 */
	public function SignOutAction ();
}
