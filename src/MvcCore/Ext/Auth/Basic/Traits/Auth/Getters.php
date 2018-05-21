<?php

namespace MvcCore\Ext\Auth\Basic\Traits\Auth;

trait Getters
{
	/***********************************************************************************
	 *                            `\MvcCore\Ext\Auth` - Getters                        *
	 ***********************************************************************************/

	/**
	 * Get expiration time (in seconds) how long to remember the user in session.
	 * You can use zero (`0`) to browser close moment, but some browsers can
	 * restore previous session after next browser application start. Or any
	 * colleague in your project could use session for storing any information
	 * for some longer time in your application and session cookie could then
	 * exists much longer then browser close moment only.
	 * So better is not to use a zero value.
	 * Default value is 10 minutes (600 seconds).
	 * @return int
	 */
	public function GetExpirationSeconds () {
		return $this->expirationSeconds;
	}

	/**
	 * Get full class name to use for user instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auth\Basic\Interfaces\IUser`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auth\Basic\User`.
	 * @return string
	 */
	public function GetUserClass () {
		return $this->userClass;
	}

	/**
	 * Get full class name to use for controller instance
	 * to submit auth form(s). Class name has to implement interfaces:
	 * - `\MvcCore\Ext\Auth\Basic\Interfaces\IController`
	 * - `\MvcCore\Interfaces\IController`
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auth\Basic\Controller`.
	 * @return string
	 */
	public function GetControllerClass () {
		return $this->controllerClass;
	}

	/**
	 * Get full class name to use for sign in form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auth\Basic\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auth\Basic\SignInForm`.
	 * @return string
	 */
	public function GetSignInFormClass () {
		return $this->signInFormClass;
	}

	/**
	 * Full class name to use for sign out form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auth\Basic\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auth\Basic\SignOutForm`.
	 * @return string
	 */
	public function GetSignOutFormClass () {
		return $this->signOutFormClass;
	}

	/**
	 * Get full url to redirect user, after sign in
	 * POST request was successful.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign in form rendered.
	 * @return string|NULL
	 */
	public function GetSignedInUrl () {
		return $this->signedInUrl;
	}

	/**
	 * Get full url to redirect user, after sign out
	 * POST request was successful.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign out form rendered.
	 * @return string|NULL
	 */
	public function GetSignedOutUrl () {
		return $this->signedOutUrl;
	}

	/**
	 * Get full url to redirect user, after sign in POST
	 * request or sign out POST request was not successful,
	 * for example wrong credentials.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign in/out form rendered.
	 * @param string $signErrorUrl
	 * @return string|NULL
	 */
	public function GetSignErrorUrl () {
		return $this->signErrorUrl;
	}

	/**
	 * Get route instance to submit sign in form into.
	 * Default configured route for sign in request is `/signin` by POST.
	 * @return \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	public function & GetSignInRoute () {
		return $this->getInitializedRoute('SignIn');
	}

	/**
	 * Get route to submit sign out form into.
	 * Default configured route for sign in request is `/signout` by POST.
	 * @return \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	public function & GetSignOutRoute () {
		return $this->getInitializedRoute('SignOut');
	}

	/**
	 * Get configured salt for `passord_hash();` to generate password by `PASSWORD_BCRYPT`.
	 * `NULL` by default. This option is the only one option required
	 * to configure authentication module to use it properly.
	 * @return string|NULL
	 */
	public function GetPasswordHashSalt () {
		return $this->passwordHashSalt;
	}

	/**
	 * Get timeout to `sleep();` PHP script before sending response to user,
	 * when user submitted invalid username or password.
	 * Default value is `3` (3 seconds).
	 * @return int
	 */
	public function GetInvalidCredentialsTimeout () {
		return $this->invalidCredentialsTimeout;
	}

	/**
	 * Get configred callable translator to set it into auth form
	 * to translate form labels, placeholders, buttons or error messages.
	 * Default value is `NULL` (forms without translations).
	 * @return callable|NULL
	 */
	public function GetTranslator () {
		return $this->translator;
	}

	/**
	 * Get authenticated user model instance reference
	 * or `NULL` if user has no username record in session namespace.
	 * If user has not yet been initialized, load the user internaly by
	 * `{$configuredUserClass}::SetUpUserBySession();` to try to load
	 * user by username record in session namespace.
	 * @return \MvcCore\Ext\Auth\Basic\User|\MvcCore\Ext\Auth\Basic\Interfaces\IUser|NULL
	 */
	public function & GetUser () {
		if (!$this->userInitialized && $this->user === NULL) {
			$configuredUserClass = $this->userClass;
			$this->user = $configuredUserClass::SetUpUserBySession();
			$this->userInitialized = TRUE;
		}
		return $this->user;
	}

	/**
	 * Return `TRUE` if user is authenticated/signed in,
	 * `TRUE` if user has any username record in session namespace.
	 * If user has not yet been initialized, load the user internaly by
	 * `$auth->GetUser();` to try to load user by username record in session namespace.
	 * @return bool
	 */
	public function IsAuthenticated () {
		return $this->GetUser() !== NULL;
	}

	/**
	 * Return completed sign in or sign out form instance.
	 * Form instance completition is processed only once,
	 * any created form instance is stored in `$auth->form` property.
	 * This method is always called by you, your application
	 * to set form into you custom template to render it for user.
	 * If user is not authenticated, sign in form is returned and
	 * if user is authenticated, opposite sign out form is returned.
	 * This method is only alias to call two other methods:
	 * - `\MvcCore\Ext\Auth\Basic::GetInstance()->GetSignInForm();` for not authenticated users.
	 * - `\MvcCore\Ext\Auth\Basic::GetInstance()->GetSignOutForm();` for authenticated users.
	 * @var \MvcCore\Ext\Auth\Basic\Traits\Form|\MvcCore\Ext\Auth\Basic\Interfaces\IForm|\MvcCore\Ext\Auth\Basic\SignInForm|\MvcCore\Ext\Auth\Basic\SignOutForm
	 */
	public function & GetForm () {
		if ($this->form === NULL) {
			if ($this->IsAuthenticated()) {
				$this->form = $this->GetSignOutForm();
			} else {
				$this->form = $this->GetSignInForm();
			}
		}
		return $this->form;
	}

	/**
	 * Return completed sign in form instance.
	 * Form instance completition is processed only once,
	 * created form instance is stored in `$auth->form` property.
	 * @return \MvcCore\Ext\Auth\Basic\SignInForm|\MvcCore\Ext\Auth\Basic\Interfaces\IForm
	 */
	public function GetSignInForm () {
		$routerClass = $this->application->GetRouterClass();
		$route = $this->getInitializedRoute('SignIn');
		$method = $route->GetMethod();
		$this->form = new \MvcCore\Ext\Auth\Basic\SignInForm($this->application->GetController());
		return $this->form
			->SetId(\MvcCore\Ext\Auth\Basic\Interfaces\IForm::ID)
			->SetCssClass('sign-in')
			->SetMethod($method !== NULL ? $method : \MvcCore\Interfaces\IRequest::METHOD_POST)
			->SetAction($routerClass::GetInstance()->UrlByRoute($route))
			->SetSuccessUrl($this->signedInUrl)
			->SetErrorUrl($this->signErrorUrl)
			->SetTranslator($this->translator)
			->Init();
	}

	/**
	 * Return completed sign out form instance.
	 * Form instance completition is processed only once,
	 * created form instance is stored in `$auth->form` property.
	 * @return \MvcCore\Ext\Auth\Basic\SignOutForm|\MvcCore\Ext\Auth\Basic\Interfaces\IForm
	 */
	public function GetSignOutForm () {
		$routerClass = $this->application->GetRouterClass();
		$route = $this->getInitializedRoute('SignOut');
		$method = $route->GetMethod();
		$this->form = new \MvcCore\Ext\Auth\Basic\SignOutForm($this->application->GetController());
		return $this->form
			->SetId(\MvcCore\Ext\Auth\Basic\Interfaces\IForm::ID)
			->SetCssClass('sign-out')
			->SetMethod($method !== NULL ? $method : \MvcCore\Interfaces\IRequest::METHOD_POST)
			->SetAction($routerClass::GetInstance()->UrlByRoute($route))
			->SetSuccessUrl($this->signedOutUrl)
			->SetErrorUrl($this->signErrorUrl)
			->SetTranslator($this->translator)
			->Init();
	}

	/**
	 * Return `\stdClass` object with values with all protected configuration properties.
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
