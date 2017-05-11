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

class Auth {
	/**
	 * MvcCore Extension - Auth - version:
	 * Comparation by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '4.1.0';
	/**
	 * Singleton instance of authentication extension service.
	 * @var \MvcCore\Ext\Auth
	 */
	protected static $instance = NULL;
	/**
	 * User model isntace or null if user is not authenticated in session.
	 * @var \MvcCore\Ext\Auth\Virtual\User
	 */
	protected $user = NULL;
	/**
	 * If user is authenticated in session, there is scompleted 
	 * sign in form, else there is sign out form.
	 * @var \MvcCore\Ext\Auth\Virtual\Form
	 */
	protected $form = NULL;
	/**
	 * Authentication configuration, there is possible to change 
	 * any configuration option ne by one by any setter method
	 * multiple values or by \MvcCore\Ext\Auth::GetInstance()->Configure([...]) method.
	 * Config array is retyped to stdClass in __constructor().
	 * @var \stdClass
	 */
	protected $config = array(
		'expirationSeconds'	=> 600, // 10 minutes
		'userClass'			=> '\User',
		'controllerClass'	=> '\Controller',
		'signInFormClass'	=> '\SignInForm',
		'signOutFormClass'	=> '\SignOutForm',
		'signedInUrl'		=> '',
		'signedOutUrl'		=> '',
		'errorUrl'			=> '',
		'signInRoute'		=> "#^/signin#",
		'signOutRoute'		=> "#^/signout#",
		'passwordHashSalt'	=> 'S3F8OI2P3X6ER1F6XY2Q9ZCY',
		'translator'		=> NULL,
	);
	/**
	 * If true, authentication service allready try to load
	 * user from session and authentication detection os not
	 * necessary to run again. False by default.
	 * @var bool
	 */
	protected $userInitialized = FALSE;

	/**
	 * Return singleton instance. If instance exists, return existing instance,
	 * if not, create new Auth service instance, store it and return it.
	 * @return \MvcCore\Ext\Auth
	 */
	public static function GetInstance () {
		if (is_null(static::$instance)) {
			static::$instance = new static();
		}
		return static::$instance;
	}
	/**
	 * Create new Auth service instance.
	 * For each configuration item- check if it is class definition
	 * and if it is, complete whole class definition.
	 */
	public function __construct () {
		foreach ($this->config as $key => & $value) {
			if (strpos($key, 'Class') !== FALSE) {
				$value = __CLASS__ . $value;
			}
		}
		$this->config = (object) $this->config;
	}
	/**
	 * Return configuration object.
	 * @return \stdClass
	 */
	public function & GetConfig () {
		return $this->config;
	}
	/**
	 * Set up authorization service configuration.
	 * Each array key must have key by default configuration item.
	 * If configuration item is class, it's checked if it exists.
	 * @param array $config 
	 * @return \MvcCore\Ext\Auth
	 */
	public function Configure ($config = array()) {
		foreach ($config as $key => $value) {
			if (isset($this->config->$key)) {
				if (strpos($key, 'Class') !== FALSE) {
					$this->_checkClass($value);
				}
				$this->config->$key = $value;
			}
		}
		return $this;
	}
	/**
	 * Set authorization expiration seconds, 10 minutes by default.
	 * @param int $expirationSeconds 
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetExpirationSeconds ($expirationSeconds = 600) {
		$this->config->expirationSeconds = $expirationSeconds;
		return $this;
	}
	/**
	 * Set user's passwords hash salt, put here any string, every request the same.
	 * @param string $passwordHashSalt 
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetPasswordHashSalt ($passwordHashSalt = '') {
		$this->config->passwordHashSalt = $passwordHashSalt;
		return $this;
	}
	/**
	 * Set authorization service user class
	 * to get store username from session stored from previous 
	 * requests for 10 minutes by default, by sign in action to compare
	 * sender credentials with any user from your custom place
	 * and by sign out action to remove username from session.
	 * It has to extend \MvcCore\Ext\Auth\Virtual\User.
	 * @param string $userClass 
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetUserClass ($userClass = '') {
		$this->config->userClass = $this->_checkClass($userClass);
		return $this;
	}
	/**
	 * Set authorization service controller class
	 * to handle signin and signout actions,
	 * it has to extend \MvcCore\Ext\Auth\Virtual\Controller.
	 * @param string $controllerClass 
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetControllerClass ($controllerClass = '') {
		$this->config->controllerClass = $this->_checkClass($controllerClass);
		return $this;
	}
	/**
	 * Set authorization service sign in form class,
	 * to create, render and submit sign in user.
	 * it has to implement \MvcCore\Ext\Auth\Virtual\Form.
	 * @param string $signInFormClass 
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetSignInFormClass ($signInFormClass = '') {
		$this->config->signInFormClass = $this->_checkClass($signInFormClass);
		return $this;
	}
	/**
	 * Set authorization service sign out form class,
	 * to create, render and submit sign out user.
	 * it has to implement \MvcCore\Ext\Auth\Virtual\Form.
	 * @param string $signInFormClass
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetSignOutFormClass ($signOutFormClass = '') {
		$this->config->signOutFormClass = $this->_checkClass($signOutFormClass);
		return $this;
	}
	/**
	 * Set translator callable if you want to translate 
	 * sign in and sign out forms labels, placeholders and error messages.
	 * @param callable $translator 
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetTranslator (callable $translator = NULL) {
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
	public function SetSignedInUrl ($signedInUrl ='') {
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
	public function SetSignedOutUrl ($signedOutUrl ='') {
		$this->config->signedOutUrl = $signedOutUrl;
		return $this;
	}
	/**
	 * Set url to redirect user after sign in or sign out process was 
	 * not successfull. By default signed in/out error url is the same as 
	 * current request url, internaly configured by default 
	 * authentication service pre request handler.
	 * @param string $errorUrl
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetErrorUrl ($errorUrl ='') {
		$this->config->errorUrl = $errorUrl;
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
	 * @param string|array|\stdClass|\MvcCore\Route $signInRoute 
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetSignInRoute ($signInRoute = NULL) {
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
	 * @param string|array|\stdClass|\MvcCore\Route $signInRoute
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetSignOutRoute ($signOutRoute = NULL) {
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
		return !is_null($this->GetUser());
	}
	/**
	 * Get authenticated user instance reference or null if user is not authenticated.
	 * If user is not loaded yet, load the user internaly by $auth->GetUser();
	 * to start session and try to load user by session username record.
	 * @return \MvcCore\Ext\Auth\Virtual\User
	 */
	public function & GetUser () {
		if (!$this->userInitialized && is_null($this->user)) {
			$userClass = $this->config->userClass;
			$this->user = $userClass::GetUserBySession();
			$this->userInitialized = TRUE;
		}
		return $this->user;
	}
	/**
	 * Set user instance by you custom external authorization service.
	 * If user instance is not null, set internal $auth->userInitialized property
	 * to TRUE to not load user internaly again.
	 * @param \MvcCore\Ext\Auth\Virtual\User $user 
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetUser (\MvcCore\Ext\Auth\Virtual\User & $user) {
		$this->user = $user;
		if (!is_null($user)) $this->userInitialized = TRUE;
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
	 * @return \MvcCore\Ext\Auth\SignInForm|\MvcCore\Ext\Auth\SignOutForm|mixed
	 */
	public function & GetForm () {
		if (is_null($this->form)) {
			$controller = \MvcCore::GetInstance()->GetController();
			if ($this->IsAuthenticated()) {
				$this->form = new \MvcCore\Ext\Auth\SignOutForm($controller);
				$this->form->Action = \MvcCore::GetInstance()->Url($this->config->signOutRoute->Name);
				$this->form->SuccessUrl = $this->config->signedOutUrl;
			} else {
				$this->form = new \MvcCore\Ext\Auth\SignInForm($controller);
				$this->form->Action = \MvcCore::GetInstance()->Url($this->config->signInRoute->Name);
				$this->form->SuccessUrl = $this->config->signedInUrl;
			}
			$this->form->ErrorUrl = $this->config->errorUrl;
			$this->form->SetTranslator($this->config->translator);
		}
		return $this->form;
	}
	/**
	 * Set sign in/sign out form instance.
	 * Use this method only if you need sometimes to 
	 * complete different form to render.
	 * @param \MvcCore\Ext\Auth\Virtual\Form $form 
	 * @return \MvcCore\Ext\Auth
	 */
	public function SetForm (& $form) {
		$this->form = $form;
		return $this;
	}
	/**
	 * Initialize necessary authentication service handlers.
	 * Call this method always in Bootstrap before request is routed by:
	 * MvcCore\Ext\Auth::GetInstance()->Init();
	 * @return \MvcCore\Ext\Auth
	 */
	public function Init () {
		// add sing in or sing out forms routes, complete form success and error addresses
		\MvcCore::AddPreRouteHandler(function (\MvcCore\Request & $request) {
			$this->PrepareHandler($request);
		});
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
		if (strpos($authControllerClass, __CLASS__) === 0) {
			$authControllerClass = '\\'.$authControllerClass;
		}
		$authenticated = $this->IsAuthenticated();
		if (!$authenticated && is_string($this->config->signInRoute)) {
			$this->config->signInRoute = \MvcCore\Route::GetInstance(array(
				'name'		=> "$authControllerClass:SignIn",
				'pattern'	=> $this->config->signInRoute,
			));
		}
		if ($authenticated && is_string($this->config->signOutRoute)) {
			$this->config->signOutRoute = \MvcCore\Route::GetInstance(array(
				'name'		=> "$authControllerClass:SignOut",
				'pattern'	=> $this->config->signOutRoute,
			));
		}
	}
	/**
	 * Third prepare handler internal method:
	 * - Set up sign in form success url, sign out form success url and error
	 *   url for both ign in/out forms, as current request url by default.
	 *   If any url is configured already, nothing is changed.
	 * @return void
	 */
	public function PrepareAdresses () {
		$request = & \MvcCore::GetInstance()->GetRequest();
		if (!$this->config->signedInUrl)	$this->config->signedInUrl = $request->FullUrl;
		if (!$this->config->signedOutUrl)	$this->config->signedOutUrl = $request->FullUrl;
		if (!$this->config->errorUrl)		$this->config->errorUrl = $request->FullUrl;
	}
	/**
	 * Fourth prepare handler internal method:
	 * - Set up sign in or sign out route into router, only route which
	 *   is currently by authenticated/not authenticated user necessary
	 *   to process in $router->Route() processing.
	 * @return void
	 */
	public function PrepareRouter () {
		if ($this->IsAuthenticated()) {
			\MvcCore\Router::GetInstance()->AddRoute(
				$this->config->signOutRoute, TRUE
			);
		} else {
			\MvcCore\Router::GetInstance()->AddRoute(
				$this->config->signInRoute, TRUE
			);
		}
	}
	/**
	 * Check if configured class exists and thrown exception if not.
	 * @param string $className 
	 * @throws \Exception 
	 * @return string
	 */
	private function _checkClass (& $className) {
		if (!class_exists($className)) {
			throw new \Exception("[".__CLASS__."] Configured class: '$className' doesn't exists.'");
		}
		return $className;
	}
}