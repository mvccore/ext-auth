<?php

namespace MvcCore\Ext\Auth\Traits;

trait AuthPropsGettersSetters
{
	/**
	 * Singleton instance of authentication extension service.
	 * @var \MvcCore\Ext\Auth
	 */
	protected static $instance = NULL;

	protected static $toolClass = NULL;

	/**
	 * @var array
	 */
	protected static $nonConfigurationProperties = array(
		'userInitialized', 'application', 'user', 'form',
	);

	/**
	 * Expiration time (in seconds) how long to remember the user in session.
	 * You can use zero (`0`) to browser close moment, but many browsers can restore previous session.
	 * And any colleague in your project could use session for some longer time in your application,
	 * so better is not to use a zero value.
	 * @var int
	 */
	protected $expirationSeconds = NULL;

	/**
	 * Full class name to use for user instance.
	 * @var string
	 */
	protected $userClass = NULL;

	/**
	 * Full class name to use for controller instance to submit sign in/out form.
	 * @var string
	 */
	protected $controllerClass = NULL;

	/**
	 * Full class name to use for sign in form instance.
	 * @var string
	 */
	protected $signInFormClass = NULL;

	/**
	 * Full class name to use for sign out form instance.
	 * @var string
	 */
	protected $signOutFormClass = NULL;

	/**
	 * Url to redirect signed in user.
	 * Null means the same url where is sign in/out form rendered.
	 * @var string|NULL
	 */
	protected $signedInUrl = NULL;

	/**
	 * Url to redirect signed out user.
	 * Null means the same url where is sign in/out form rendered.
	 * @var string|NULL
	 */
	protected $signedOutUrl = NULL;

	/**
	 * Url to redirect user with wrong credentials.
	 * Null means the same url where is sign in/out form rendered.
	 * @var string|NULL
	 */
	protected $signErrorUrl = NULL;

	/**
	 * Route to submit sign in form to.
	 * @var string|array|\MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	protected $signInRoute = NULL;

	/**
	 * Route to submit sign out form to.
	 * @var string|array|\MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	protected $signOutRoute = NULL;

	/**
	 * Salt for `passord_hash() = NULL;`.
	 * @var string
	 */
	protected $passwordHashSalt = NULL;

	/**
	 * Valid callable to set up sign in/out form translator.
	 * @var callable|NULL
	 */
	protected $translator = NULL;

	/**
	 * MvcCore application instance reference.
	 * @var \MvcCore\Application|\MvcCore\Interfaces\IApplication
	 */
	protected $application = NULL;

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
	 * `TRUE`if there was already called method `GetUser()`
	 * with any result or `SetUSer()` with any param.
	 * @var bool
	 */
	protected $userInitialized = FALSE;




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
		$this->signOutRoute = $signOutRoute;
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
		if ($this->form === NULL) $this->initAuthForm();
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
