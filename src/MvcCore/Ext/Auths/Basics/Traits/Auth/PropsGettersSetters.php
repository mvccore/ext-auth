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

namespace MvcCore\Ext\Auths\Basics\Traits\Auth;

/**
 * Trait for `\MvcCore\Ext\Auths\Basic` class. Trait contains:
 * - All static properties.
 * - All instance configurable properties except `protected $autoInit` property from `\MvcCore\Model`.
 * - All instance non-configurable properties for internal use.
 * - Getters for non-configurable and configurable instance properties.
 * - Setters for configurable properties with interface implementation checking for class name properties.
 * - Setters for non-configurable instance properties.
 */
trait PropsGettersSetters
{
	/***********************************************************************************
	 *                                Static Properties                                *
	 ***********************************************************************************/

	/**
	 * Singleton instance of authentication extension module.
	 * @var \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth|NULL
	 */
	protected static $instance = NULL;

	/**
	 * Shortcut for configured core tool class value
	 * from `\MvcCore\Application::GetInstance()->GetToolClass();`.
	 * @var string|NULL
	 */
	protected static $toolClass = NULL;

	/**
	 * Properties names which are internal properties
	 * or internal instances for authentication module,
	 * which are not configuration properties, instance properties only.
	 * This array is used only in `\MvcCore\Ext\Auth::GetConfiguration();`.
	 * @var array
	 */
	protected static $nonConfigurationProperties = array(
		'userInitialized', 'application', 'user', 'form',
	);


	/***********************************************************************************
	 *                            Configuration Properties                             *
	 ***********************************************************************************/

	/**
	 * Expiration time (in seconds) how long to remember the user in session.
	 * You can use zero (`0`) to browser close moment, but some browsers can
	 * restore previous session after next browser application start. Or any
	 * colleague in your project could use session for storing any information
	 * for some longer time in your application and session cookie could then
	 * exists much longer then browser close moment only.
	 * So better is not to use a zero value.
	 * Default value is 10 minutes (600 seconds).
	 * @var int
	 */
	protected $expirationSeconds = 600;

	/**
	 * Full class name to use for user instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IUser`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\User`.
	 * @var string
	 */
	protected $userClass = 'User';

	/**
	 * Full class name to use for user role class.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IRole`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\Role`.
	 * @var string
	 */
	protected $roleClass = 'Role';

	/**
	 * Full class name to use for controller instance
	 * to submit auth form(s). Class name has to implement interfaces:
	 * - `\MvcCore\Ext\Auths\Basics\Interfaces\IController`
	 * - `\MvcCore\Interfaces\IController`
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\Controller`.
	 * @var string
	 */
	protected $controllerClass = 'Controller';

	/**
	 * Full class name to use for sign in form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\SignInForm`.
	 * @var string
	 */
	protected $signInFormClass = 'SignInForm';

	/**
	 * Full class name to use for sign out form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\SignOutForm`.
	 * @var string
	 */
	protected $signOutFormClass = 'SignOutForm';

	/**
	 * Full url to redirect user, after sign in
	 * POST request was successful.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign in form rendered.
	 * @var string|NULL
	 */
	protected $signedInUrl = NULL;

	/**
	 * Full url to redirect user, after sign out
	 * POST request was successful.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign out form rendered.
	 * @var string|NULL
	 */
	protected $signedOutUrl = NULL;

	/**
	 * Full url to redirect user, after sign in POST
	 * request or sign out POST request was not successful,
	 * for example wrong credentials.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign in/out form rendered.
	 * @var string|NULL
	 */
	protected $signErrorUrl = NULL;

	/**
	 * Route to submit sign in form to.
	 * It could be defined only as a string (route pattern),
	 * or as route configuration array or as route instance.
	 * Default match/reverse pattern for route sign request is
	 * `/signin` by POST.
	 * @var string|array|\MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	protected $signInRoute = array(
		'name'		=> 'auth_signin',
		'match'		=> '#^/signin(?=/$|$)#',
		'reverse'	=> '/signin',
		'method'	=> \MvcCore\Interfaces\IRequest::METHOD_POST
	);

	/**
	 * Route to submit sign out form into.
	 * It could be defined only as a string (route pattern),
	 * or as route configuration array or as route instance.
	 * Default match/reverse pattern for route sign request is
	 * `/signout` by POST.
	 * @var string|array|\MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	protected $signOutRoute = array(
		'name'		=> 'auth_signout',
		'match'		=> '#^/signout(?=/$|$)#',
		'reverse'	=> '/signout',
		'method'	=> \MvcCore\Interfaces\IRequest::METHOD_POST
	);

	/**
	 * Salt for `passord_hash();` to generate password by `PASSWORD_BCRYPT`.
	 * `NULL` by default. This option is the only one option required
	 * to configure authentication module to use it properly.
	 * @var string
	 */
	protected $passwordHashSalt = NULL;

	/**
	 * Timeout to `sleep();` PHP script before sending response to user,
	 * when user submitted invalid username or password.
	 * Default value is `3` (3 seconds).
	 * @var int
	 */
	protected $invalidCredentialsTimeout = 3;

	/**
	 * Callable translator to set it into auth form
	 * to translate form labels, placeholders, buttons or error messages.
	 * Default value is `NULL` (forms without translations).
	 * @var callable|NULL
	 */
	protected $translator = NULL;

	/**
	 * Pre-route and pre-dispatch application callable handlers priority index.
	 * This property has no setter and getter. It's possible to configure only throw constructor.
	 * @var int
	 */
	protected $preHandlersPriority = 100;


	/***********************************************************************************
	 *                               Internal Properties                               *
	 ***********************************************************************************/

	/**
	 * MvcCore application instance reference from
	 * `\MvcCore\Application::GetInstance()`, because
	 * it's used many times in authentication class.
	 * @var \MvcCore\Application|\MvcCore\Interfaces\IApplication
	 */
	protected $application = NULL;

	/**
	 * User model instace or `NULL` if user has no username record in session namespace.
	 * @var \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser|NULL
	 */
	protected $user = NULL;

	/**
	 * Sign in form instance, sign out form instance or any
	 * other authentication form instance in extended classes.
	 * If user is authenticated by username record in session namespace,
	 * there is completed sign out form, if not authenticated, sign in form otherwise etc...
	 * @var \MvcCore\Ext\Auths\Basics\Traits\Form|\MvcCore\Ext\Auths\Basics\Interfaces\IForm|\MvcCore\Ext\Auths\Basics\SignInForm|\MvcCore\Ext\Auths\Basics\SignOutForm
	 */
	protected $form = NULL;

	/**
	 * This is only internal semaphore to call
	 * `\MvcCore\Ext\Auths\Basics\User::SetUpUserBySession()`
	 * only once (if result is `NULL`) in request predispatch state.
	 * `TRUE`if method `\MvcCore\Ext\Auth::GetInstance()->GetUser()`
	 * has been called already with any result and also `TRUE` if
	 * method `\MvcCore\Ext\Auth::GetInstance()->SetUser($user)` has been
	 * already called with any first argument `$user` value.
	 * @var bool
	 */
	protected $userInitialized = FALSE;

	/**
	 * This is only internal semaphore to define when to add
	 * sign in or sign out route into router in pre route request state.
	 * If any configured route is for different http method than `POST`,
	 * than this property is set to `TRUE`. If both configured routes
	 * use only `POST` method, this property is automaticly `FALSE` to
	 * not add routes for all requests, only for `POST` requests.
	 * Default value is `FALSE` because both default routes use `POST` methods.
	 * @var bool
	 */
	protected $addRoutesForAnyRequestMethod = FALSE;


	/***********************************************************************************
	 *                                     Getters                                     *
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
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IUser`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\User`.
	 * @return string
	 */
	public function GetUserClass () {
		return $this->userClass;
	}

	/**
	 * Get full class name to use for user role class.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IRole`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\Role`.
	 * @return string
	 */
	public function GetRoleClass () {
		return $this->roleClass;
	}

	/**
	 * Get full class name to use for controller instance
	 * to submit auth form(s). Class name has to implement interfaces:
	 * - `\MvcCore\Ext\Auths\Basics\Interfaces\IController`
	 * - `\MvcCore\Interfaces\IController`
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\Controller`.
	 * @return string
	 */
	public function GetControllerClass () {
		return $this->controllerClass;
	}

	/**
	 * Get full class name to use for sign in form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\SignInForm`.
	 * @return string
	 */
	public function GetSignInFormClass () {
		return $this->signInFormClass;
	}

	/**
	 * Full class name to use for sign out form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\SignOutForm`.
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
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser|NULL
	 */
	public function & GetUser () {
		if (!$this->userInitialized && $this->user === NULL) {
			$configuredUserClass = $this->userClass;
			$this->user = $configuredUserClass::SetUpUserBySession();
			if ($this->user !== NULL) $this->user->SetPasswordHash(NULL);
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
	 * - `\MvcCore\Ext\Auths\Basic::GetInstance()->GetSignInForm();` for not authenticated users.
	 * - `\MvcCore\Ext\Auths\Basic::GetInstance()->GetSignOutForm();` for authenticated users.
	 * @var \MvcCore\Ext\Auths\Basics\Traits\Form|\MvcCore\Ext\Auths\Basics\Interfaces\IForm|\MvcCore\Ext\Auths\Basics\SignInForm|\MvcCore\Ext\Auths\Basics\SignOutForm
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
	 * @return \MvcCore\Ext\Auths\Basics\SignInForm|\MvcCore\Ext\Auths\Basics\Interfaces\IForm
	 */
	public function GetSignInForm () {
		$routerClass = $this->application->GetRouterClass();
		$route = $this->getInitializedRoute('SignIn');
		$method = $route->GetMethod();
		$htmlId = \MvcCore\Ext\Auths\Basics\Interfaces\IForm::HTML_ID_SIGNIN;
		$this->form = new \MvcCore\Ext\Auths\Basics\SignInForm($this->application->GetController());
		return $this->form
			->SetId($htmlId)
			->SetCssClass(str_replace('_', ' ', $htmlId))
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
	 * @return \MvcCore\Ext\Auths\Basics\SignOutForm|\MvcCore\Ext\Auths\Basics\Interfaces\IForm
	 */
	public function GetSignOutForm () {
		$routerClass = $this->application->GetRouterClass();
		$route = $this->getInitializedRoute('SignOut');
		$method = $route->GetMethod();
		$htmlId = \MvcCore\Ext\Auths\Basics\Interfaces\IForm::HTML_ID_SIGNOUT;
		$this->form = new \MvcCore\Ext\Auths\Basics\SignOutForm($this->application->GetController());
		return $this->form
			->SetId($htmlId)
			->SetCssClass(str_replace('_', ' ', $htmlId))
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


	/***********************************************************************************
	 *                                     Setters                                     *
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
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetExpirationSeconds ($expirationSeconds = 600) {
		$this->expirationSeconds = $expirationSeconds;
		return $this;
	}

	/**
	 * Set full class name to use for user instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IUser`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\User`.
	 * @param string $userClass User full class name implementing `\MvcCore\Ext\Auths\Basics\Interfaces\IUser`.
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetUserClass ($userClass = '') {
		$this->userClass = $this->checkClassImplementation(
			$userClass, \MvcCore\Ext\Auths\Basics\Interfaces\IUser::class, TRUE
		);
		return $this;
	}

	/**
	 * Set full class name to use for user role class.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IRole`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\Role`.
	 * @param string $roleClass Role full class name implementing `\MvcCore\Ext\Auths\Basics\Interfaces\IRole`.
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetRoleClass ($roleClass = '') {
		$this->userClass = $this->checkClassImplementation(
			$roleClass, \MvcCore\Ext\Auths\Basics\Interfaces\IRole::class, TRUE
		);
		return $this;
	}

	/**
	 * Set full class name to use for controller instance
	 * to submit auth form(s). Class name has to implement interfaces:
	 * - `\MvcCore\Ext\Auths\Basics\Interfaces\IController`
	 * - `\MvcCore\Interfaces\IController`
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\Controller`.
	 * @param string $controllerClass Controller full class name implementing `\MvcCore\Ext\Auths\Basics\Interfaces\IController`.
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetControllerClass ($controllerClass = '') {
		$controllerClass = $this->checkClassImplementation(
			$controllerClass, \MvcCore\Ext\Auths\Basics\Interfaces\IController::class, FALSE
		);
		$this->controllerClass = $this->checkClassImplementation(
			$controllerClass, \MvcCore\Interfaces\IController::class, TRUE
		);
		return $this;
	}

	/**
	 * Set full class name to use for sign in form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\SignInForm`.
	 * @param string $signInFormClass Form full class name implementing `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetSignInFormClass ($signInFormClass = '') {
		$this->signInFormClass = $this->checkClassImplementation(
			$signInFormClass, \MvcCore\Ext\Auths\Basics\Interfaces\IForm::class, FALSE
		);
		return $this;
	}

	/**
	 * Set full class name to use for sign out form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\SignOutForm`.
	 * @param string $signInFormClass Form full class name implementing `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetSignOutFormClass ($signOutFormClass = '') {
		$this->signOutFormClass = $this->checkClassImplementation(
			$signOutFormClass, \MvcCore\Ext\Auths\Basics\Interfaces\IForm::class, FALSE
		);
		return $this;
	}

	/**
	 * Set full url to redirect user, after sign in
	 * POST request was successful.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign in form rendered.
	 * @param string|NULL $signedInUrl
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
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
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
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
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetSignErrorUrl ($signErrorUrl = NULL) {
		$this->signErrorUrl = $signErrorUrl;
		return $this;
	}

	/**
	 * Set route instance to submit sign in form into.
	 * Default configured route for sign in request is `/signin` by POST.
	 * @param string|array|\MvcCore\Route|\MvcCore\Interfaces\IRoute $signInRoute
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
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
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
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
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
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
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
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
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetTranslator (callable $translator = NULL) {
		$this->translator = $translator;
		return $this;
	}

	/**
	 * Set user instance manualy. If you use this method
	 * no authentication by `{$configuredUserClass}::SetUpUserBySession();`
	 * is used and authentication state is always positive.
	 * @param \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser|NULL $user
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetUser (\MvcCore\Ext\Auths\Basics\Interfaces\IUser & $user = NULL) {
		$this->user = $user;
		if ($this->user !== NULL) $this->user->SetPasswordHash(NULL);
		$this->userInitialized = TRUE;
		return $this;
	}

	/**
	 * Set sign in, sign out or any authentication form instance.
	 * Use this method only if you need sometimes to complete different form to render.
	 * @param \MvcCore\Ext\Auths\Basics\SignInForm|\MvcCore\Ext\Auths\Basics\SignOutForm|\MvcCore\Ext\Auths\Basics\Traits\Form|\MvcCore\Ext\Auths\Basics\Interfaces\IForm $form
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetForm (\MvcCore\Ext\Auths\Basics\Interfaces\IForm & $form) {
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
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
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
	 * to database user: `\MvcCore\Ext\Auths\Basics\Users\Database`.
	 * Alias for `\MvcCore\Ext\Auths\Basics\Users\Database::SetUsersTableStructure($tableName, $columnNames);`.
	 * @param string|NULL	$tableName Database table name.
	 * @param string[]|NULL	$columnNames Keys are user class protected properties names in camel case, values are database columns names.
	 * @return \MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetTableStructureForDbUsers ($tableName = NULL, $columnNames = NULL) {
		$userClass = $this->userClass;
		$toolClass = static::$toolClass;
		if ($toolClass::CheckClassInterface($userClass, \MvcCore\Ext\Auths\Basics\Interfaces\IDatabaseUser::class, TRUE, TRUE)) {
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
