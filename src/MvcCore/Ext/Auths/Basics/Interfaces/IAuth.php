<?php

namespace MvcCore\Ext\Auths\Basics\Interfaces;

interface IAuth
{
	/**
	 * MvcCore Extension - Auth - version:
	 * Comparation by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.0.0-alpha';

	/**
	 * Return singleton instance. If instance exists, return existing instance,
	 * if not, create new basic authentication module instance, store it and return it.
	 * @param array $configuration Optional configuration passed into method
	 *                             `\MvcCore\Ext\Auths\Basic::__construct($configuration)`.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public static function GetInstance ($configuration = array());

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
	public function GetExpirationSeconds ();

	/**
	 * Get full class name to use for user instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IUser`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\User`.
	 * @return string
	 */
	public function GetUserClass ();

	/**
	 * Get full class name to use for user role class.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IRole`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\Role`.
	 * @return string
	 */
	public function GetRoleClass ();

	/**
	 * Get full class name to use for controller instance
	 * to submit auth form(s). Class name has to implement interfaces:
	 * - `\MvcCore\Ext\Auths\Basics\Interfaces\IController`
	 * - `\MvcCore\Interfaces\IController`
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\Controller`.
	 * @return string
	 */
	public function GetControllerClass ();

	/**
	 * Get full class name to use for sign in form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\SignInForm`.
	 * @return string
	 */
	public function GetSignInFormClass ();

	/**
	 * Full class name to use for sign out form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\SignOutForm`.
	 * @return string
	 */
	public function GetSignOutFormClass ();

	/**
	 * Get full url to redirect user, after sign in
	 * POST request was successful.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign in form rendered.
	 * @return string|NULL
	 */
	public function GetSignedInUrl ();

	/**
	 * Get full url to redirect user, after sign out
	 * POST request was successful.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign out form rendered.
	 * @return string|NULL
	 */
	public function GetSignedOutUrl ();

	/**
	 * Get full url to redirect user, after sign in POST
	 * request or sign out POST request was not successful,
	 * for example wrong credentials.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign in/out form rendered.
	 * @param string $signErrorUrl
	 * @return string|NULL
	 */
	public function GetSignErrorUrl ();

	/**
	 * Get route instance to submit sign in form into.
	 * Default configured route for sign in request is `/signin` by POST.
	 * @return \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	public function & GetSignInRoute ();

	/**
	 * Get route to submit sign out form into.
	 * Default configured route for sign in request is `/signout` by POST.
	 * @return \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	public function & GetSignOutRoute ();

	/**
	 * Get configured salt for `passord_hash();` to generate password by `PASSWORD_BCRYPT`.
	 * `NULL` by default. This option is the only one option required
	 * to configure authentication module to use it properly.
	 * @return string|NULL
	 */
	public function GetPasswordHashSalt ();

	/**
	 * Get timeout to `sleep();` PHP script before sending response to user,
	 * when user submitted invalid username or password.
	 * Default value is `3` (3 seconds).
	 * @return int
	 */
	public function GetInvalidCredentialsTimeout ();

	/**
	 * Get configred callable translator to set it into auth form
	 * to translate form labels, placeholders, buttons or error messages.
	 * Default value is `NULL` (forms without translations).
	 * @return callable|NULL
	 */
	public function GetTranslator ();

	/**
	 * Get authenticated user model instance reference
	 * or `NULL` if user has no username record in session namespace.
	 * If user has not yet been initialized, load the user internaly by
	 * `{$configuredUserClass}::SetUpUserBySession();` to try to load
	 * user by username record in session namespace.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IUser|NULL
	 */
	public function & GetUser ();

	/**
	 * Return `TRUE` if user is authenticated/signed in,
	 * `TRUE` if user has any username record in session namespace.
	 * If user has not yet been initialized, load the user internaly by
	 * `$auth->GetUser();` to try to load user by username record in session namespace.
	 * @return bool
	 */
	public function IsAuthenticated ();

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
	 * @var \MvcCore\Ext\Auths\Basics\Interfaces\IForm
	 */
	public function & GetForm ();

	/**
	 * Return completed sign in form instance.
	 * Form instance completition is processed only once,
	 * created form instance is stored in `$auth->form` property.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IForm
	 */
	public function GetSignInForm ();

	/**
	 * Return completed sign out form instance.
	 * Form instance completition is processed only once,
	 * created form instance is stored in `$auth->form` property.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IForm
	 */
	public function GetSignOutForm ();

	/**
	 * Return `\stdClass` object with values with all protected configuration properties.
	 * @return \stdClass
	 */
	public function & GetConfiguration ();

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
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetExpirationSeconds ($expirationSeconds = 600);

	/**
	 * Set full class name to use for user instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IUser`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\User`.
	 * @param string $userClass User full class name implementing `\MvcCore\Ext\Auths\Basics\Interfaces\IUser`.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetUserClass ($userClass = '');

	/**
	 * Set full class name to use for user role class.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IRole`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\Role`.
	 * @param string $roleClass Role full class name implementing `\MvcCore\Ext\Auths\Basics\Interfaces\IRole`.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetRoleClass ($roleClass = '');

	/**
	 * Set full class name to use for controller instance
	 * to submit auth form(s). Class name has to implement interfaces:
	 * - `\MvcCore\Ext\Auths\Basics\Interfaces\IController`
	 * - `\MvcCore\Interfaces\IController`
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\Controller`.
	 * @param string $controllerClass Controller full class name implementing `\MvcCore\Ext\Auths\Basics\Interfaces\IController`.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetControllerClass ($controllerClass = '');

	/**
	 * Set full class name to use for sign in form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\SignInForm`.
	 * @param string $signInFormClass Form full class name implementing `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetSignInFormClass ($signInFormClass = '');

	/**
	 * Set full class name to use for sign out form instance.
	 * Class name has to implement interface
	 * `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * Default value after auth module init is
	 * configured to `\MvcCore\Ext\Auths\Basics\SignOutForm`.
	 * @param string $signInFormClass Form full class name implementing `\MvcCore\Ext\Auths\Basics\Interfaces\IForm`.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetSignOutFormClass ($signOutFormClass = '');

	/**
	 * Set full url to redirect user, after sign in
	 * POST request was successful.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign in form rendered.
	 * @param string|NULL $signedInUrl
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetSignedInUrl ($signedInUrl = NULL);

	/**
	 * Set full url to redirect user, after sign out
	 * POST request was successful.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign out form rendered.
	 * @param string|NULL $signedOutUrl
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetSignedOutUrl ($signedOutUrl = NULL);

	/**
	 * Set full url to redirect user, after sign in POST
	 * request or sign out POST request was not successful,
	 * for example wrong credentials.
	 * If `NULL` (by default), user will be redirected
	 * to the same url, where was sign in/out form rendered.
	 * @param string|NULL $signErrorUrl
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetSignErrorUrl ($signErrorUrl = NULL);

	/**
	 * Set route instance to submit sign in form into.
	 * Default configured route for sign in request is `/signin` by POST.
	 * @param string|array|\MvcCore\Interfaces\IRoute $signInRoute
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetSignInRoute ($signInRoute = NULL);

	/**
	 * Set route to submit sign out form into.
	 * Default configured route for sign in request is `/signout` by POST.
	 * @param string|array|\MvcCore\Interfaces\IRoute $signOutRoute
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetSignOutRoute ($signOutRoute = NULL);

	/**
	 * Set configured salt for `passord_hash();` to generate password by `PASSWORD_BCRYPT`.
	 * `NULL` by default. This option is the only one option required
	 * to configure authentication module to use it properly.
	 * @param string $passwordHashSalt
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetPasswordHashSalt ($passwordHashSalt = '');

	/**
	 * Set timeout to `sleep();` PHP script before sending response to user,
	 * when user submitted invalid username or password.
	 * Default value is `3` (3 seconds).
	 * @param int $seconds
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetInvalidCredentialsTimeout ($seconds = 3);

	/**
	 * Set callable translator to set it into auth form
	 * to translate form labels, placeholders or buttons.
	 * Default value is `NULL` (forms without translations).
	 * @param callable $translator
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetTranslator (callable $translator = NULL);

	/**
	 * Set user instance manualy. If you use this method
	 * no authentication by `{$configuredUserClass}::SetUpUserBySession();`
	 * is used and authentication state is always positive.
	 * @param \MvcCore\Ext\Auths\Basics\Interfaces\IUser|NULL $user
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetUser (\MvcCore\Ext\Auths\Basics\Interfaces\IUser & $user = NULL);

	/**
	 * Set sign in, sign out or any authentication form instance.
	 * Use this method only if you need sometimes to complete different form to render.
	 * @param \MvcCore\Ext\Auths\Basics\Interfaces\IForm $form
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetForm (\MvcCore\Ext\Auths\Basics\Interfaces\IForm & $form);

	/**
	 * Set up authorization module configuration.
	 * Each array key has to be key by protected configuration property in this class.
	 * All properties are one by one configured by it's setter method.
	 * @param array $configuration Keys by protected properties names in camel case.
	 * @param bool $throwExceptionIfPropertyIsMissing
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public function & SetConfiguration ($configuration = array(), $throwExceptionIfPropertyIsMissing = TRUE);

	/**
	 * Optional alias method if you have user class configured
	 * to database user: `\MvcCore\Ext\Auths\Basics\Users\Database`.
	 * Alias for `\MvcCore\Ext\Auths\Basics\Users\Database::SetUsersTableStructure($tableName, $columnNames);`.
	 * @param string|NULL	$tableName Database table name.
	 * @param string[]|NULL	$columnNames Keys are user class protected properties names in camel case, values are database columns names.
	 */
	public function & SetTableStructureForDbUsers ($tableName = NULL, $columnNames = NULL);
}
