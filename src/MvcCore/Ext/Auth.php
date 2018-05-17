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

/**
 * Responsibility - managing login/logout forms, authentication requests and user instance.
 * -
 */
class Auth
{
	use \MvcCore\Ext\Auth\Traits\AuthPropsGettersSetters;

	/**
	 * Create new Auth service instance.
	 * For each configuration item- check if it is class definition
	 * and if it is, complete whole class definition.
	 */
	public function __construct ($config = array()) {
		// set up possible configuration
		if ($config) $this->SetConfiguration($config);
		// set up application reference
		$this->application = & \MvcCore\Application::GetInstance();
		// set up tools class
		static::$toolClass = $this->application->GetToolClass();
		// add sing in or sing out forms routes, complete form success and error addresses
		$this->application
			->AddPreRouteHandler(function (\MvcCore\Interfaces\IRequest & $request) {
				$this->preRouteHandler($request);
		});
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
	protected function preRouteHandler () {
		$this->GetUser();
		$requestMethod = $this->application->GetRequest()->GetMethod();
		if ($requestMethod == \MvcCore\Interfaces\IRequest::METHOD_POST) {
			$this->preRouteHandlerSetUpRoutes();
			$this->preRouteHandlerSetUpUrlAdresses();
			$this->preRouteHandlerSetUpRouter();
		}
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
	protected function preRouteHandlerSetUpRoutes () {
		$authControllerClass = & $this->controllerClass;
		if (strpos($authControllerClass, get_called_class()) === 0) {
			$authControllerClass = '\\'.$authControllerClass;
		}
		$authenticated = $this->IsAuthenticated();
		if (!$authenticated)
			$this->preRouteHandlerSetUpRoute($authControllerClass.':SignIn', 'signInRoute');
		if ($authenticated)
			$this->preRouteHandlerSetUpRoute($authControllerClass.':SignOut', 'signOutRoute');
	}

	/**
	 * Prepare configured route record into route instance if record is string or array.
	 * @param string $authCtrlAndActionName
	 * @param string $routeName
	 * @return void
	 */
	protected function preRouteHandlerSetUpRoute ($authCtrlAndActionName, $routeName) {
		$route = & $this->$routeName;
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
	 * Third prepare handler internal method:
	 * - Set up sign in form success url, sign out form success url and error
	 *   url for both sign in/out forms, as current request url by default.
	 *   If any url is configured already, nothing is changed.
	 * @return void
	 */
	protected function preRouteHandlerSetUpUrlAdresses () {
		$currentFullUrl = $this->application->GetRequest()->GetFullUrl();
		if ($this->signedInUrl === NULL)	$this->signedInUrl	= $currentFullUrl;
		if ($this->signedOutUrl === NULL)	$this->signedOutUrl	= $currentFullUrl;
		if ($this->signErrorUrl === NULL)	$this->signErrorUrl	= $currentFullUrl;
	}

	/**
	 * Fourth prepare handler internal method:
	 * - Set up sign in or sign out route into router, only route which
	 *   is currently by authenticated/not authenticated user necessary
	 *   to process in $router->Route() processing.
	 * @return void
	 */
	protected function preRouteHandlerSetUpRouter () {
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

	protected function initAuthForm () {
		$controller = $this->application->GetController();
		$routerClass = $this->application->GetRouterClass();
		$router = $routerClass::GetInstance();
		$action = '';
		$successUrl = '';
		if ($this->IsAuthenticated()) {
			$this->form = new \MvcCore\Ext\Auth\SignOutForm($controller);
			$action = $router->Url($this->signOutRoute->GetName());
			$successUrl = $this->signedOutUrl;
		} else {
			$this->form = new \MvcCore\Ext\Auth\SignInForm($controller);
			$action = $router->Url($this->signInRoute->GetName());
			$successUrl = $this->signedInUrl;
		}
		$this->form
			->SetAction($action)
			->SetSuccessUrl($successUrl)
			->SetErrorUrl($this->signErrorUrl)
			->SetTranslator($this->translator)
			->Init();
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
