<?php

namespace MvcCore\Ext\Auth\Basic\Traits\Auth;

trait Setters
{
	/***********************************************************************************
	 *                            `\MvcCore\Ext\Auth` - Setters                        *
	 ***********************************************************************************/

	/**
	 * Set expiration time (in seconds) how long to remember the user in session.
	 * You can use zero (`0`) to browser close moment, but some browsers can
	 * restore previous session after next browser application start. Or any
	 * colleague in your project could use session for storing any information
	 * for some longer time in your application and session cookie could then
	 * exists much longer then browser close moment only.
	 * So better is not to use a zero value.
	 * Default value is 10 minutes (600 seconds).
	 * @param int $expirationSeconds
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetExpirationSeconds ($expirationSeconds = 600) {
		$this->expirationSeconds = $expirationSeconds;
		return $this;
	}

	/**
	 * Set full class name to use for user instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auth\Basic\Interfaces\IUser`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auth\Basic\User`.
	 * @param string $userClass User full class name implementing `\MvcCore\Ext\Auth\Basic\Interfaces\IUser`.
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetUserClass ($userClass = '') {
		$this->userClass = $this->checkClassImplementation(
			$userClass, \MvcCore\Ext\Auth\Basic\Interfaces\IUser::class, TRUE
		);
		return $this;
	}

	/**
	 * Set full class name to use for controller instance
	 * to submit auth form(s). Class name has to implement interfaces:
	 * - `\MvcCore\Ext\Auth\Basic\Interfaces\IController`
	 * - `\MvcCore\Interfaces\IController`
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auth\Basic\Controller`.
	 * @param string $controllerClass Controller full class name implementing `\MvcCore\Ext\Auth\Basic\Interfaces\IController`.
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetControllerClass ($controllerClass = '') {
		$controllerClass = $this->checkClassImplementation(
			$controllerClass, \MvcCore\Ext\Auth\Basic\Interfaces\IController::class, FALSE
		);
		$this->controllerClass = $this->checkClassImplementation(
			$controllerClass, \MvcCore\Interfaces\IController::class, TRUE
		);
		return $this;
	}

	/**
	 * Set full class name to use for sign in form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auth\Basic\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auth\Basic\SignInForm`.
	 * @param string $signInFormClass Form full class name implementing `\MvcCore\Ext\Auth\Basic\Interfaces\IForm`.
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetSignInFormClass ($signInFormClass = '') {
		$this->signInFormClass = $this->checkClassImplementation(
			$signInFormClass, \MvcCore\Ext\Auth\Basic\Interfaces\IForm::class, FALSE
		);
		return $this;
	}

	/**
	 * Set full class name to use for sign out form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auth\Basic\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auth\Basic\SignOutForm`.
	 * @param string $signInFormClass Form full class name implementing `\MvcCore\Ext\Auth\Basic\Interfaces\IForm`.
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetSignOutFormClass ($signOutFormClass = '') {
		$this->signOutFormClass = $this->checkClassImplementation(
			$signOutFormClass, \MvcCore\Ext\Auth\Basic\Interfaces\IForm::class, FALSE
		);
		return $this;
	}

	/**
	 * Set full url to redirect user, after sign in
	 * POST request was successful.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign in form rendered.
	 * @param string|NULL $signedInUrl
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetSignedInUrl ($signedInUrl = NULL) {
		$this->signedInUrl = $signedInUrl;
		return $this;
	}

	/**
	 * Set full url to redirect user, after sign out
	 * POST request was successful.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign out form rendered.
	 * @param string|NULL $signedOutUrl
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetSignedOutUrl ($signedOutUrl = NULL) {
		$this->signedOutUrl = $signedOutUrl;
		return $this;
	}

	/**
	 * Set full url to redirect user, after sign in POST
	 * request or sign out POST request was not successful,
	 * for example wrong credentials.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign in/out form rendered.
	 * @param string|NULL $signErrorUrl
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetSignErrorUrl ($signErrorUrl = NULL) {
		$this->signErrorUrl = $signErrorUrl;
		return $this;
	}

	/**
	 * Set route instance to submit sign in form into.
	 * Default configured route for sign in request is `/signin` by POST.
	 * @param string|array|\MvcCore\Route|\MvcCore\Interfaces\IRoute $signInRoute
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
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
	 * Set route to submit sign out form into.
	 * Default configured route for sign in request is `/signout` by POST.
	 * @param string|array|\MvcCore\Route|\MvcCore\Interfaces\IRoute $signOutRoute
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
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
	 * Set configured salt for `passord_hash();` to generate password by `PASSWORD_BCRYPT`.
	 * `NULL` by default. This option is the only one option required
	 * to configure authentication module to use it properly.
	 * @param string $passwordHashSalt
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetPasswordHashSalt ($passwordHashSalt = '') {
		$this->passwordHashSalt = $passwordHashSalt;
		return $this;
	}

	/**
	 * Set timeout to `sleep();` PHP script before sending response to user,
	 * when user submitted invalid username or password.
	 * Default value is `3` (3 seconds).
	 * @param int $seconds
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetInvalidCredentialsTimeout ($seconds = 3) {
		$this->invalidCredentialsTimeout = $seconds;
		return $this;
	}

	/**
	 * Set callable translator to set it into auth form
	 * to translate form labels, placeholders or buttons.
	 * Default value is `NULL` (forms without translations).
	 * @param callable $translator
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetTranslator (callable $translator = NULL) {
		$this->translator = $translator;
		return $this;
	}

	/**
	 * Set user instance manualy. If you use this method
	 * no authentication by `{$configuredUserClass}::SetUpUserBySession();`
	 * is used and authentication state is always positive.
	 * @param \MvcCore\Ext\Auth\Basic\User|\MvcCore\Ext\Auth\Basic\Interfaces\IUser|NULL $user
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetUser (\MvcCore\Ext\Auth\Basic\Interfaces\IUser & $user = NULL) {
		$this->user = $user;
		$this->userInitialized = TRUE;
		return $this;
	}

	/**
	 * Set sign in, sign out or any authentication form instance.
	 * Use this method only if you need sometimes to complete different form to render.
	 * @param \MvcCore\Ext\Auth\Basic\SignInForm|\MvcCore\Ext\Auth\Basic\SignOutForm|\MvcCore\Ext\Auth\Basic\Traits\Form|\MvcCore\Ext\Auth\Basic\Interfaces\IForm $form
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetForm (\MvcCore\Ext\Auth\Basic\Interfaces\IForm & $form) {
		$this->form = $form;
		return $this;
	}

	/**
	 * Set up authorization module configuration.
	 * Each array key has to be key by protected configuration property in this class.
	 * All properties are one by one configured by it's setter method.
	 * @param array $configuration Keys by protected properties names in camel case.
	 * @param bool $throwExceptionIfPropertyIsMissing
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetConfiguration ($configuration = array(), $throwExceptionIfPropertyIsMissing = TRUE) {
		foreach ($configuration as $key => & $value) {
			$setter = 'Set' . ucfirst($key);
			if (method_exists($this, $setter)) {
				$this->$setter($value);
			} else if ($throwExceptionIfPropertyIsMissing) {
				throw new \InvalidArgumentException (
					'['.__CLASS__.'] Property `'.$key.'` has no setter method `'.$setter.'` in class `'.get_class($this).'`.'
				);
			}
		}
		return $this;
	}

	/**
	 * Optional alias method if you have user class configured
	 * to database user: `\MvcCore\Ext\Auth\Basic\Users\Database`.
	 * Alias for `\MvcCore\Ext\Auth\Basic\Users\Database::SetUsersTableStructure($tableName, $columnNames);`.
	 * @param string|NULL	$tableName Database table name.
	 * @param string[]|NULL	$columnNames Keys are user class protected properties names in camel case, values are database columns names.
	 * @return \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basic\Interfaces\IAuth
	 */
	public function & SetTableStructureForDbUsers ($tableName = NULL, $columnNames = NULL) {
		$userClass = $this->userClass;
		$toolClass = static::$toolClass;
		if ($toolClass::CheckClassInterface($userClass, \MvcCore\Ext\Auth\Basic\Interfaces\IDatabaseUser::class, TRUE, TRUE)) {
			$userClass::SetUsersTableStructure($tableName, $columnNames);
		};
		return $this;
	}

	/**
	 * Check if given class name implements given interface
	 * and optionaly if test class implements static interface methods.
	 * If not, thrown an `\InvalidArgumentException` every time.
	 * @param string $testClassName Full test class name.
	 * @param string $interfaceName Full interface class name.
	 * @param bool $checkStaticMethods `FALSE` by default.
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	protected function checkClassImplementation ($testClassName, $interfaceName, $checkStaticMethods = FALSE) {
		$toolClass = static::$toolClass;
		if ($toolClass::CheckClassInterface($testClassName, $interfaceName, $checkStaticMethods, TRUE)) {
			return $testClassName;
		}
		return '';
	}
}
