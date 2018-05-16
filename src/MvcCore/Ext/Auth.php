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

namespace MvcCore\Ext;

class Auth
{
	/**
	 * MvcCore Extension - Auth - version:
	 * Comparation by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.0.0-alpha';

	/**
	 * Singleton instance of authentication extension service.
	 * @var \MvcCore\Ext\Auth
	 */
	protected static $instance = NULL;

	/**
	 * User model isntace or null if user is not authenticated in session.
	 * @var \MvcCore\Ext\Auth\Traits\User|\MvcCore\Ext\Auth\Interfaces\IUser
	 */
	protected $user = NULL;

	/**
	 * If user is authenticated in session, there is scompleted
	 * sign in form, else there is sign out form.
	 * @var \MvcCore\Ext\Auth\Traits\SignForm|\MvcCore\Ext\Auth\Interfaces\ISignForm|\MvcCore\Ext\Auth\SignInForm|\MvcCore\Ext\Auth\SignOutForm
	 */
	protected $form = NULL;

	/**
	 * MvcCore application instance reference.
	 * @var \MvcCore\Application|\MvcCore\Interfaces\IApplication
	 */
	protected $application = NULL;

	/**
	 * Sign in route instance by configured core class.
	 * @var \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	protected $signInRoute = NULL;

	/**
	 * Sign out route instance by configured core class.
	 * @var \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	protected $signOutRoute = NULL;

	/**
	 * Sign error route instance by configured core class.
	 * @var \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	protected $signErrorRoute = NULL;

	/**
	 * `TRUE`if there was already called method `GetUser()`
	 * with any result or `SetUSer()` with any param.
	 * @var bool
	 */
	protected $userInitialized = FALSE;

	/**
	 * Authentication configuration, there is possible to change
	 * any configuration option ne by one by any setter method
	 * multiple values or by \MvcCore\Ext\Auth::GetInstance()->Configure([...]) method.
	 * Config array is retyped to stdClass in __constructor().
	 * @var \stdClass
	 */
	protected $config = array(
		'expirationSeconds'	=> 600, // 10 minutes
		/** @var string Full class name to use for user instance. */
		'userClass'			=> '\User',
		/** @var string Full class name to use for controller instance to submit sign in/out form. */
		'controllerClass'	=> '\SignController',
		/** @var string Full class name to use for sign in form instance. */
		'signInFormClass'	=> '\SignInForm',
		/** @var string Full class name to use for sign out form instance. */
		'signOutFormClass'	=> '\SignOutForm',
		/** @var string|NULL Optional custom url to redirect signed in user. Null means the same url where is sign in/out form rendered */
		'signedInUrl'		=> NULL,
		/** @var string|NULL Optional custom url to redirect signed out user. Null means the same url where is sign in/out form rendered */
		'signedOutUrl'		=> NULL,
		/** @var string|NULL Optional custom url to redirect user with wrong credentials. Null means the same url where is sign in/out form rendered */
		'signErrorUrl'		=> NULL,
		/** @var string|array|\MvcCore\Route|\MvcCore\Interfaces\IRoute Route to submit sign in form to */
		'signInRoute'		=> array('match' => '#^/signin(?=/$|$)#', 'reverse' => '/signin'),
		/** @var string|array|\MvcCore\Route|\MvcCore\Interfaces\IRoute Route to submit sign out form to */
		'signOutRoute'		=> array('match' => '#^/signout(?=/$|$)#', 'reverse' => '/signout'),
		/** @var string|NULL Optional custom salt for `passord_hash();`. */
		'passwordHashSalt'	=> NULL,
		/** @var callable Valid callable to set up sign in/out form translator */
		'translator'		=> NULL,
	);

	/**
	 * Return singleton instance. If instance exists, return existing instance,
	 * if not, create new Auth service instance, store it and return it.
	 * @return \MvcCore\Ext\Auth
	 */
	public static function GetInstance ($config = array()) {
		if (static::$instance === NULL) {
			static::$instance = new static($config);
		}
		return static::$instance;
	}

	/**
	 * Create new Auth service instance.
	 * For each configuration item- check if it is class definition
	 * and if it is, complete whole class definition.
	 */
	public function __construct ($config = array()) {
		// initialize default configuration
		foreach ($this->config as $key => & $value) {
			if (strpos($key, 'Class') !== FALSE)
				$value = get_called_class() . $value;
		}
		$this->config = (object) $this->config;
		// merge another possible configuration
		if ($config) $this->Configure($config);
		// set up application reference
		$this->application = & \MvcCore\Application::GetInstance();
		// add sing in or sing out forms routes, complete form success and error addresses
		$this->application
			->AddPreRouteHandler(function (\MvcCore\Interfaces\IRequest & $request) {
				$this->PrepareHandler($request);
		});
	}

	/**
	 * Return configuration object.
	 * @return \stdClass
	 */
	public function & GetConfig () {
		return $this->config;
	}

	/**
	 * Replace whole configuration by new values, no merging with default configuration.
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetConfig ($config = array()) {
		$this->config = (object) $config;
		return $this;
	}

	/**
	 * Set up authorization service configuration.
	 * Each array key must have key by default configuration item.
	 * If configuration item is class, it's checked if it exists.
	 * @param array $config
	 * @return \MvcCore\Ext\Auth
	 */
	public function Configure ($config = array()) {
		$configEntries = array_keys((array) $this->config);
		foreach ($config as $key => & $value) {
			if (in_array($key, $configEntries)) {
				if (strpos($key, 'Class') !== FALSE)
					$this->checkClassExistence($value);
				$this->config->$key = & $value;
			}
		}
		return $this;
	}

	/**
	 * Set authorization expiration seconds, 10 minutes by default.
	 * @param int $expirationSeconds
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetExpirationSeconds ($expirationSeconds = 600) {
		$this->config->expirationSeconds = $expirationSeconds;
		return $this;
	}

	/**
	 * Set user's passwords hash salt, put here any string, every request the same.
	 * @param string $passwordHashSalt
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetPasswordHashSalt ($passwordHashSalt = '') {
		$this->config->passwordHashSalt = $passwordHashSalt;
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
		$this->config->userClass = $this->checkClassExistence($userClass);
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
		$this->config->controllerClass = $this->checkClassExistence($controllerClass);
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
		$this->config->signInFormClass = $this->checkClassExistence($signInFormClass);
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
		$this->config->signOutFormClass = $this->checkClassExistence($signOutFormClass);
		return $this;
	}

	/**
	 * Set translator callable if you want to translate
	 * sign in and sign out forms labels, placeholders and error messages.
	 * @param callable $translator
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetTranslator (callable $translator = NULL) {
		$this->config->translator = $translator;
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
		$this->config->signedInUrl = $signedInUrl;
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
		$this->config->signedOutUrl = $signedOutUrl;
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
		$this->config->signErrorUrl = $signErrorUrl;
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
		$this->config->signInRoute = $signInRoute;
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
	 * @param string|array|\MvcCore\Interfaces\IRoute $signInRoute
	 * @return \MvcCore\Ext\Auth
	 */
	public function & SetSignOutRoute ($signOutRoute = NULL) {
		$this->config->signOutRoute = $signOutRoute;
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
	 * Get authenticated user instance reference or null if user is not authenticated.
	 * If user is not loaded yet, load the user internaly by $auth->GetUser();
	 * to start session and try to load user by session username record.
	 * @return \MvcCore\Ext\Auth\Traits\User|\MvcCore\Ext\Auth\Interfaces\IUser
	 */
	public function & GetUser () {
		if (!$this->userInitialized && $this->user === NULL) {
			$userClass = $this->config->userClass;
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
	 * Alias for `\MvcCore\Ext\Auth\Users\Database::SetUsersTableStructure($tableName, $columnNames);`.
	 * @param string|NULL	$tableName
	 * @param string[]|NULL	$columnNames
	 */
	public function & SetTableStructureForDbUsers ($tableName = NULL, $columnNames = NULL) {
		$userClass = $this->config->userClass;
		$toolClass = $this->application->GetToolClass();
		if ($toolClass::CheckClassInterface($userClass, 'MvcCore\Ext\Auth\Interfaces\IDatabaseUser', TRUE)) {
			$userClass::SetUsersTableStructure($tableName, $columnNames);
		};
		return $this;
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
		if ($this->form === NULL) {
			$controller = $this->application->GetController();
			$routerClass = $this->application->GetRouterClass();
			$router = $routerClass::GetInstance();
			$action = '';
			$successUrl = '';
			if ($this->IsAuthenticated()) {
				$this->form = new \MvcCore\Ext\Auth\SignOutForm($controller);
				$action = $router->Url($this->signOutRoute->GetName());
				$successUrl = $this->config->signedOutUrl;
			} else {
				$this->form = new \MvcCore\Ext\Auth\SignInForm($controller);
				$action = $router->Url($this->signInRoute->GetName());
				$successUrl = $this->config->signedInUrl;
			}
			$this->form
				->SetAction($action)
				->SetSuccessUrl($successUrl)
				->SetErrorUrl($this->config->signErrorUrl)
				->SetTranslator($this->config->translator)
				->Init();
		}
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
	 * Process necessary operations before request is routed.
	 * This method is called internaly by default and it's called
	 * by \MvcCore pre route handler initialized in $auth->Init(); method.
	 *
	 * - Try to load user by stored session username from previous requests.
	 *
	 * - If controller class begins with substring containing this
	 *   authentication class name, then it is obvious that controller
	 *   has to have in route definition full class name defined by slash
	 *   character in class name begin - so correct this controller class
	 *   name if necessary to set up routes properly immediately on lines bellow.
	 * - If configured singin/out routes are still strings only, create
	 *   from those strings new \MvcCore\Route instances into the same config
	 *   place to add them into router immediately on lines bellow.
	 *
	 * - Set up sign in form success url, sign out form success url and error
	 *   url for both ign in/out forms, as current request url by default.
	 *   If any url is configured already, nothing is changed.
	 *
	 * - Set up sign in or sign out route into router, only route which
	 *   is currently by authenticated/not authenticated user necessary
	 *   to process in $router->Route() processing.
	 * @return void
	 */
	public function PrepareHandler () {
		$this->GetUser();
		$this->PrepareRoutes();
		$this->PrepareAdresses();
		$this->PrepareRouter();
	}

	/**
	 * Second prepare handler internal method:
	 * - If controller class begins with substring containing this
	 *   authentication class name, then it is obvious that controller
	 *   has to have in route definition full class name defined by slash
	 *   character in class name begin - so correct this controller class
	 *   name if necessary to set up routes properly immediately on lines bellow.
	 * - If configured singin/out routes are still strings only, create
	 *   from those strings new \MvcCore\Route instances into the same config
	 *   place to add them into router immediately on lines bellow.
	 * @return void
	 */
	public function PrepareRoutes () {
		$authControllerClass = & $this->config->controllerClass;
		if (strpos($authControllerClass, get_called_class()) === 0) {
			$authControllerClass = '\\'.$authControllerClass;
		}
		$authenticated = $this->IsAuthenticated();
		if (!$authenticated)
			$this->prepareConfiguredRoute($authControllerClass.':SignIn', 'signInRoute');
		if ($authenticated)
			$this->prepareConfiguredRoute($authControllerClass.':SignOut', 'signOutRoute');
	}

	/**
	 * Third prepare handler internal method:
	 * - Set up sign in form success url, sign out form success url and error
	 *   url for both sign in/out forms, as current request url by default.
	 *   If any url is configured already, nothing is changed.
	 * @return void
	 */
	public function PrepareAdresses () {
		$currentFullUrl = $this->application->GetRequest()->GetFullUrl();
		if ($this->config->signedInUrl === NULL)	$this->config->signedInUrl = $currentFullUrl;
		if ($this->config->signedOutUrl === NULL)	$this->config->signedOutUrl = $currentFullUrl;
		if ($this->config->signErrorUrl === NULL)	$this->config->signErrorUrl = $currentFullUrl;
	}

	/**
	 * Fourth prepare handler internal method:
	 * - Set up sign in or sign out route into router, only route which
	 *   is currently by authenticated/not authenticated user necessary
	 *   to process in $router->Route() processing.
	 * @return void
	 */
	public function PrepareRouter () {
		$routerClass = $this->application->GetRouterClass();
		if ($this->IsAuthenticated()) {
			$routerClass::GetInstance()->AddRoute(
				$this->signOutRoute, TRUE
			);
		} else {
			$routerClass::GetInstance()->AddRoute(
				$this->signInRoute, TRUE
			);
		}
	}

	/**
	 * Prepare configured route record into route instance if record is string or array.
	 * @param string $authCtrlAndActionName
	 * @param string $routeName
	 * @return void
	 */
	protected function prepareConfiguredRoute ($authCtrlAndActionName, $routeName) {
		$route = & $this->config->$routeName;
		if ($route instanceof \MvcCore\Interfaces\IRoute) {
			$this->$routeName = & $route;
		} else {
			$routeClass = $this->application->GetRouteClass();
			$routeInitData = array('name' => $authCtrlAndActionName);
			$this->$routeName = $routeClass::GetInstance(
				gettype($route) == 'array'
					? array_merge($routeInitData, $route)
					: array_merge(array('pattern' => $route), $routeInitData)
			);
		}
	}

	/**
	 * Check if configured class exists and thrown exception if not.
	 * @param string $className
	 * @throws \Exception
	 * @return string
	 */
	protected function checkClassExistence ($className, $thrownException = TRUE) {
		if (!class_exists($className) && $thrownException) throw new \InvalidArgumentException(
			"[".__CLASS__."] Configured class: '$className' doesn't exists.'"
		);
		return $className;
	}
}
