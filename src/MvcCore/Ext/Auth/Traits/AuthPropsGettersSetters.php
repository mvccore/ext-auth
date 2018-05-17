<?php

namespace MvcCore\Ext\Auth\Traits;

trait AuthPropsGettersSetters
{
	/***********************************************************************************
	 *                     `\MvcCore\Ext\Auth` - Static Properties                     *
	 ***********************************************************************************/

	/**
	 * Singleton instance of authentication extension module.
	 * @var \MvcCore\Ext\Auth|NULL
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
	 *                 `\MvcCore\Ext\Auth` - Configuration Properties                  *
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
	 * Class name has to implement interface `\MvcCore\Ext\Auth\Interfaces\IUser`.
	 * Default value after init is configured to `\MvcCore\Ext\Auth\User`.
	 * @var string
	 */
	protected $userClass = 'User';

	/**
	 * Full class name to use for controller instance to submit sign in/out form.
	 * Class name has to implement interface `\MvcCore\Ext\Auth\Interfaces\ISignController`.
	 * Default value after init is configured to `\MvcCore\Ext\Auth\SignController`.
	 * @var string
	 */
	protected $controllerClass = 'SignController';

	/**
	 * Full class name to use for sign in form instance.
	 * Class name has to implement interface `\MvcCore\Ext\Auth\Interfaces\ISignForm`.
	 * Default value after init is configured to `\MvcCore\Ext\Auth\SignInform`.
	 * @var string
	 */
	protected $signInFormClass = 'SignInForm';

	/**
	 * Full class name to use for sign out form instance.
	 * Class name has to implement interface `\MvcCore\Ext\Auth\Interfaces\ISignForm`.
	 * Default value after init is configured to `\MvcCore\Ext\Auth\SignOutForm`.
	 * @var string
	 */
	protected $signOutFormClass = 'SignOutForm';

	/**
	 * Full url to redirect user, after sign in POST request was successful.
	 * If `NULL` (by default), user will be redirected to the same url, where was sign in form rendered.
	 * @var string|NULL
	 */
	protected $signedInUrl = NULL;

	/**
	 * Full url to redirect user, after sign out POST request was successful.
	 * If `NULL` (by default), user will be redirected to the same url, where was sign out form rendered.
	 * @var string|NULL
	 */
	protected $signedOutUrl = NULL;

	/**
	 * Full url to redirect user, after sign in or sign out POST request was not successful,
	 * for example wrong credentials.
	 * If `NULL` (by default), user will be redirected to the same url, where was sign in/out form rendered.
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
		'match'		=> '#^/signin(?=/$|$)#',
		'reverse'	=> '/signin',
		'method'	=> \MvcCore\Interfaces\IRequest::METHOD_POST
	);

	/**
	 * Route to submit sign out form to.
	 * It could be defined only as a string (route pattern),
	 * or as route configuration array or as route instance.
	 * Default match/reverse pattern for route sign request is
	 * `/signout` by POST.
	 * @var string|array|\MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	protected $signOutRoute = array(
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
	 * Callable to set into sign in/out form translator to translate
	 * form labels, placeholders and buttons.
	 * Default value is `NULL` to not translate anything.
	 * @var callable|NULL
	 */
	protected $translator = NULL;


	/***********************************************************************************
	 *                      `\MvcCore\Ext\Auth` - Internal Properties                  *
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
	 * @var \MvcCore\Ext\Auth\Traits\User|\MvcCore\Ext\Auth\Interfaces\IUser
	 */
	protected $user = NULL;

	/**
	 * Sign in or sign out form instance.
	 * If user is authenticated by username record in session namespace,
	 * there is completed sign out form, if not authenticated, sign in form otherwise.
	 * @var \MvcCore\Ext\Auth\Traits\SignForm|\MvcCore\Ext\Auth\Interfaces\ISignForm|\MvcCore\Ext\Auth\SignInForm|\MvcCore\Ext\Auth\SignOutForm
	 */
	protected $form = NULL;

	/**
	 * This is only internal semaphore to call
	 * `\MvcCore\Ext\Auth\User::SetUpUserBySession()`
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
	 *                       `\MvcCore\Ext\Auth` - Getters & Setters                   *
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
	 * Set authorization expiration seconds, 10 minutes by default.
	 * @param int $expirationSeconds
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetExpirationSeconds ($expirationSeconds = 600) {
		$this->expirationSeconds = $expirationSeconds;
		return $this;
	}

	/**
	 * Get user's passwords hash salt, put here any string, every request the same.
	 * @return string|NULL
	 */
	public function GetPasswordHashSalt () {
		return $this->passwordHashSalt;
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
	 * Get authorization service controller class
	 * to handle signin and signout actions,
	 * it has to extend \MvcCore\Ext\Auth\Abstracts\Controller.
	 * @param string $controllerClass
	 * @return \MvcCore\Ext\Auth
	 */
	public function GetControllerClass () {
		return $this->controllerClass;
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
	 * Get authorization service sign in form class,
	 * to create, render and submit sign in user.
	 * it has to implement \MvcCore\Ext\Auth\Abstracts\Form.
	 * @return string
	 */
	public function GetSignInFormClass () {
		return $this->signInFormClass;
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
	 * Get authorization service sign out form class,
	 * to create, render and submit sign out user.
	 * it has to implement \MvcCore\Ext\Auth\Abstracts\Form.
	 * @return string
	 */
	public function GetSignOutFormClass () {
		return $this->signOutFormClass;
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
	 * Get translator callable if you want to translate
	 * sign in and sign out forms labels, placeholders and error messages.
	 * @return callable
	 */
	public function GetTranslator () {
		return $this->translator;
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
	 * Get url to redirect user after sign in process was successfull.
	 * By default signed in url is the same as current request url,
	 * internaly configured by default authentication service pre request handler.
	 * @return string
	 */
	public function GetSignedInUrl () {
		return $this->signedInUrl;
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
	 * Get url to redirect user after sign out process was successfull.
	 * By default signed out url is the same as current request url,
	 * internaly configured by default authentication service pre request handler.
	 * @return string
	 */
	public function GetSignedOutUrl () {
		return $this->signedOutUrl;
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
	 * @return \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	public function GetSignInRoute () {
		return $this->getInitializedRoute('SignIn');
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
			$method = $signInRoute['method'];
		if ($signInRoute instanceof \MvcCore\Interfaces\IRoute)
			$method = $signInRoute->GetMethod();
		if ($method !== \MvcCore\Interfaces\IRequest::METHOD_POST)
			$this->addRoutesForAnyRequestMethod = TRUE;
		return $this;
	}

	/**
	 * @return \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	public function GetSignOutRoute () {
		return $this->getInitializedRoute('SignOut');
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
			$method = $signOutRoute['method'];
		if ($signOutRoute instanceof \MvcCore\Interfaces\IRoute)
			$method = $signOutRoute->GetMethod();
		if ($method !== \MvcCore\Interfaces\IRequest::METHOD_POST)
			$this->addRoutesForAnyRequestMethod = TRUE;
		return $this;
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
					'['.__CLASS__.'] Property `'.$key."` doesn't method_exists in class `".get_class($this).'`.'
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
		if ($toolClass::CheckClassInterface($userClass, 'MvcCore\Ext\Auth\Interfaces\IDatabaseUser', TRUE)) {
			$userClass::SetUsersTableStructure($tableName, $columnNames);
		};
		return $this;
	}
}
