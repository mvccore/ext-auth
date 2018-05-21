<?php

namespace MvcCore\Ext\Auth\Basic\Interfaces;

interface IAuthController
{
	public function SignInAction ();
	public function SignOutAction ();
}
