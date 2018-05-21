<?php

namespace MvcCore\Ext\Auth\Basic\Traits\Auth;

trait Setters
{
	/***********************************************************************************
	 *                            `\MvcCore\Ext\Auth` - Setters                        *
	 ***********************************************************************************/

	/**
	 * Set authorization expiration seconds, 10 minutes by default.
	 * @param int $expirationSeconds
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetExpirationSeconds ($expirationSeconds = 600) {
		$this->expirationSeconds = $expirationSeconds;
		return $this;
	}


	/**
	 * Set user's passwords hash salt, put here any string, every request the same.
	 * @param string $passwordHashSalt
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetPasswordHashSalt ($passwordHashSalt = '') {
		$this->passwordHashSalt = $passwordHashSalt;
		return $this;
	}

	/**
	 * Set authorization service user class
	 * to get store username from session stored from previous
	 * requests for 10 minutes by default, by sign in action to compare
	 * sender credentials with any user from your custom place
	 * and by sign out action to remove username from session.
	 * It has to extend \MvcCore\Ext\Auth\Abstracts\User.
	 * @param string $userClass
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetUserClass ($userClass = '') {
		$this->userClass = $this->checkClassExistence($userClass);
		return $this;
	}

	/**
	 * Set authorization service controller class
	 * to handle signin and signout actions,
	 * it has to extend \MvcCore\Ext\Auth\Abstracts\Controller.
	 * @param string $controllerClass
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetControllerClass ($controllerClass = '') {
		$this->controllerClass = $this->checkClassExistence($controllerClass);
		return $this;
	}

	/**
	 * Set authorization service sign in form class,
	 * to create, render and submit sign in user.
	 * it has to implement \MvcCore\Ext\Auth\Abstracts\Form.
	 * @param string $signInFormClass
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetSignInFormClass ($signInFormClass = '') {
		$this->signInFormClass = $this->checkClassExistence($signInFormClass);
		return $this;
	}

	/**
	 * Set authorization service sign out form class,
	 * to create, render and submit sign out user.
	 * it has to implement \MvcCore\Ext\Auth\Abstracts\Form.
	 * @param string $signInFormClass
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetSignOutFormClass ($signOutFormClass = '') {
		$this->signOutFormClass = $this->checkClassExistence($signOutFormClass);
		return $this;
	}

	/**
	 * Set translator callable if you want to translate
	 * sign in and sign out forms labels, placeholders and error messages.
	 * @param callable $translator
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetTranslator (callable $translator = NULL) {
		$this->translator = $translator;
		return $this;
	}

	/**
	 * Set url to redirect user after sign in process was successfull.
	 * By default signed in url is the same as current request url,
	 * internaly configured by default authentication service pre request handler.
	 * @param string $signedInUrl
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetSignedInUrl ($signedInUrl ='') {
		$this->signedInUrl = $signedInUrl;
		return $this;
	}

	/**
	 * Set url to redirect user after sign out process was successfull.
	 * By default signed out url is the same as current request url,
	 * internaly configured by default authentication service pre request handler.
	 * @param string $signedOutUrl
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetSignedOutUrl ($signedOutUrl ='') {
		$this->signedOutUrl = $signedOutUrl;
		return $this;
	}

	/**
	 * Set url to redirect user after sign in or sign out process was
	 * not successfull. By default signed in/out error url is the same as
	 * current request url, internaly configured by default
	 * authentication service pre request handler.
	 * @param string $signErrorUrl
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetSignErrorUrl ($signErrorUrl = NULL) {
		$this->signErrorUrl = $signErrorUrl;
		return $this;
	}

	/**
	 * Set sign in route, where to navigate user browser after
	 * user clicks on submit button in sign in form and
	 * where to run authentication process.
	 * Route shoud be any pattern string without any groups,
	 * or route configuration array/stdClass or \MvcCore\Route
	 * instance. Sign in route is prepended before all routes
	 * in default service preroute handler.
	 * @param string|array|\MvcCore\Interfaces\IRoute $signInRoute
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetSignInRoute ($signInRoute = NULL) {
		$this->signInRoute = $signInRoute;
		$method = NULL;
		if (gettype($signInRoute) == 'array' && isset($signInRoute['method']))
			$method = strtoupper($signInRoute['method']);
		if ($signInRoute instanceof \MvcCore\Interfaces\IRoute)
			$method = $signInRoute->GetMethod();
		if ($method !== \MvcCore\Interfaces\IRequest::METHOD_POST)
			$this->addRoutesForAnyRequestMethod = TRUE;
		return $this;
	}

	/**
	 * Set sign out route, where to navigate user browser after
	 * user clicks on submit button in sign out form and
	 * where to run deauthentication process.
	 * Route shoud be any pattern string without any groups,
	 * or route configuration array/stdClass or \MvcCore\Route
	 * instance. Sign out route is prepended before all routes
	 * in default service preroute handler.
	 * @param string|array|\MvcCore\Interfaces\IRoute $signOutRoute
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetSignOutRoute ($signOutRoute = NULL) {
		$this->signOutRoute = $signOutRoute;
		$method = NULL;
		if (gettype($signOutRoute) == 'array' && isset($signOutRoute['method']))
			$method = strtoupper($signOutRoute['method']);
		if ($signOutRoute instanceof \MvcCore\Interfaces\IRoute)
			$method = $signOutRoute->GetMethod();
		if ($method !== \MvcCore\Interfaces\IRequest::METHOD_POST)
			$this->addRoutesForAnyRequestMethod = TRUE;
		return $this;
	}

	/**
	 * Set user instance by you custom external authorization service.
	 * @param \MvcCore\Ext\Auth\Traits\User|\MvcCore\Ext\Auth\Interfaces\IUser $user
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetUser (\MvcCore\Ext\Auth\Interfaces\IUser & $user) {
		$this->user = $user;
		$this->userInitialized = TRUE;
		return $this;
	}

	/**
	 * Set sign in/sign out form instance.
	 * Use this method only if you need sometimes to
	 * complete different form to render.
	 * @param \MvcCore\Ext\Auth\SignInForm|\MvcCore\Ext\Auth\SignOutForm|\MvcCore\Ext\Auth\Traits\SignForm|\MvcCore\Ext\Auth\Interfaces\ISignForm $form
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetForm (\MvcCore\Ext\Auth\Interfaces\ISignForm & $form) {
		$this->form = $form;
		return $this;
	}

	/**
	 * Set up authorization service configuration.
	 * Each array key must have key by default configuration item.
	 * If configuration item is class, it's checked if it exists.
	 * @param array $configuration
	 * @param bool $throwExceptionMissingKeys
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetConfiguration ($configuration = array(), $throwExceptionMissingKeys = FALSE) {
		foreach ($configuration as $key => & $value) {
			$setter = 'Set' . ucfirst($key);
			if (method_exists($this, $setter)) {
				$this->$setter($value);
			} else if ($throwExceptionMissingKeys) {
				throw new \InvalidArgumentException (
					'['.__CLASS__.'] Property `'.$key.'` has no setter method `'.$setter.'` in class `'.get_class($this).'`.'
				);
			}
		}
		return $this;
	}

	/**
	 * Alias for `\MvcCore\Ext\Auth\Users\Database::SetUsersTableStructure($tableName, $columnNames);`.
	 * @param string|NULL	$tableName
	 * @param string[]|NULL	$columnNames
	 */
	public function & SetTableStructureForDbUsers ($tableName = NULL, $columnNames = NULL) {
		$userClass = $this->userClass;
		$toolClass = static::$toolClass;
		if ($toolClass::CheckClassInterface($userClass, 'MvcCore\Ext\Auth\Interfaces\IDatabaseUser', TRUE, TRUE)) {
			$userClass::SetUsersTableStructure($tableName, $columnNames);
		};
		return $this;
	}
}
