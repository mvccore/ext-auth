<?php

namespace MvcCore\Ext\Auth\Basic\Traits\Auth;

trait Props
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
		'name'		=> 'auth_signin',
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
}
