<?php

namespace MvcCore\Ext\Auth\Basic\Traits\Auth;

trait Getters
{
	/***********************************************************************************
	 *                            `\MvcCore\Ext\Auth` - Getters                        *
	 ***********************************************************************************/

	/**
	 * Return singleton instance. If instance exists, return existing instance,
	 * if not, create new Auth module instance, store it and return it.
	 * @param array $configuration Optional configuration passed into `__construct($configuration)` method.
	 * @return \MvcCore\Ext\Auth
	 */
	public static function GetInstance ($configuration = array()) {
		if (static::$instance === NULL)
			static::$instance = new static($configuration);
		return static::$instance;
	}

	/**
	 * Set authorization expiration seconds, 10 minutes by default.
	 * @return int
	 */
	public function GetExpirationSeconds () {
		return $this->expirationSeconds;
	}

	/**
	 * Get user's passwords hash salt, put here any string, every request the same.
	 * @return string|NULL
	 */
	public function GetPasswordHashSalt () {
		return $this->passwordHashSalt;
	}

	/**
	 * Get authorization service user class
	 * to get store username from session stored from previous
	 * requests for 10 minutes by default, by sign in action to compare
	 * sender credentials with any user from your custom place
	 * and by sign out action to remove username from session.
	 * It has to extend \MvcCore\Ext\Auth\Abstracts\User.
	 * @return string
	 */
	public function GetUserClass () {
		return $this->userClass;
	}

	/**
	 * Get authorization service controller class
	 * to handle signin and signout actions,
	 * it has to extend \MvcCore\Ext\Auth\Abstracts\Controller.
	 * @return \MvcCore\Ext\Auth
	 */
	public function GetControllerClass () {
		return $this->controllerClass;
	}

	/**
	 * Get authorization service sign in form class,
	 * to create, render and submit sign in user.
	 * it has to implement \MvcCore\Ext\Auth\Abstracts\Form.
	 * @return string
	 */
	public function GetSignInFormClass () {
		return $this->signInFormClass;
	}

	/**
	 * Get authorization service sign out form class,
	 * to create, render and submit sign out user.
	 * it has to implement \MvcCore\Ext\Auth\Abstracts\Form.
	 * @return string
	 */
	public function GetSignOutFormClass () {
		return $this->signOutFormClass;
	}

	/**
	 * Get translator callable if you want to translate
	 * sign in and sign out forms labels, placeholders and error messages.
	 * @return callable
	 */
	public function GetTranslator () {
		return $this->translator;
	}

	/**
	 * Get url to redirect user after sign in process was successfull.
	 * By default signed in url is the same as current request url,
	 * internaly configured by default authentication service pre request handler.
	 * @return string
	 */
	public function GetSignedInUrl () {
		return $this->signedInUrl;
	}

	/**
	 * Get url to redirect user after sign out process was successfull.
	 * By default signed out url is the same as current request url,
	 * internaly configured by default authentication service pre request handler.
	 * @return string
	 */
	public function GetSignedOutUrl () {
		return $this->signedOutUrl;
	}

	/**
	 * Get url to redirect user after sign in or sign out process was
	 * not successfull. By default signed in/out error url is the same as
	 * current request url, internaly configured by default
	 * authentication service pre request handler.
	 * @param string $signErrorUrl
	 * @return \MvcCore\Ext\Auth
	 */
	public function GetSignErrorUrl () {
		return $this->signErrorUrl;
	}

	/**
	 * @return \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	public function GetSignInRoute () {
		return $this->getInitializedRoute('SignIn');
	}

	/**
	 * @return \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	public function GetSignOutRoute () {
		return $this->getInitializedRoute('SignOut');
	}

	/**
	 * Get authenticated user instance reference or null if user is not authenticated.
	 * If user is not loaded yet, load the user internaly by $auth->GetUser();
	 * to start session and try to load user by session username record.
	 * @return \MvcCore\Ext\Auth\Traits\User|\MvcCore\Ext\Auth\Interfaces\IUser
	 */
	public function & GetUser () {
		if (!$this->userInitialized && $this->user === NULL) {
			$userClass = $this->userClass;
			$this->user = $userClass::SetUpUserBySession();
			$this->userInitialized = TRUE;
		}
		return $this->user;
	}

	/**
	 * Return TRUE if user is authenticated/signed in.
	 * If user is not loaded yet, load the user internaly by $auth->GetUser();
	 * to start session and try to load user by session username record.
	 * @return bool
	 */
	public function IsAuthenticated () {
		return $this->GetUser() !== NULL;
	}

	/**
	 * Return completed signin/signout form instance.
	 * Form instance completiion is processed only once,
	 * created instance is stored in $auth->form property.
	 * This method is always called by you, your application
	 * to set form into you custom template to render it for user.
	 * If user is not authenticated, sign in form is returned and
	 * if user is authenticated, opposite sign out form is returned.
	 * @return \MvcCore\Ext\Auth\Interfaces\ISignForm
	 */
	public function & GetForm () {
		if ($this->form === NULL) $this->initializeAuthForm();
		return $this->form;
	}


	/**
	 * Return configuration object.
	 * @return \stdClass
	 */
	public function & GetConfiguration () {
		$result = array();
		$type = new \ReflectionClass($this);
		/** @var $props \ReflectionProperty[] */
		$props = $type->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
		foreach ($props as $prop) {
			$name = $prop->getName();
			if (!in_array($name, static::$nonConfigurationProperties))
				$result[$name] = $prop->getValue();
		}
		return $result;
	}
}
