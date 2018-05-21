<?php

namespace MvcCore\Ext\Auth\Basic\Traits\Auth;

trait Logic
{
	/**
	 * Create new Auth service instance.
	 * For each configuration item- check if it is class definition
	 * and if it is, complete whole class definition.
	 */
	public function __construct ($config = array()) {
		// set up possible configuration
		if ($config) $this->SetConfiguration($config);
		// initialize classes configuration
		$baseClassName = '\\' . __CLASS__ . '\\';
		if ($this->signInCtrlClass && substr($this->signInCtrlClass, 0, 1) != '\\')
			$this->signInCtrlClass = $baseClassName . $this->signInCtrlClass;
		if ($this->signOutCtrlClass && substr($this->signOutCtrlClass, 0, 1) != '\\')
			$this->signOutCtrlClass = $baseClassName . $this->signOutCtrlClass;
		if ($this->signInFormClass && substr($this->signInFormClass, 0, 1) != '\\')
			$this->signInFormClass = $baseClassName . $this->signInFormClass;
		if ($this->signOutFormClass && substr($this->signOutFormClass, 0, 1) != '\\')
			$this->signOutFormClass = $baseClassName . $this->signOutFormClass;
		if ($this->userClass && substr($this->userClass, 0, 1) != '\\')
			$this->userClass = $baseClassName . $this->userClass;
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
		if (
			$this->addRoutesForAnyRequestMethod ||
			$this->application->GetRequest()->GetMethod() == \MvcCore\Interfaces\IRequest::METHOD_POST
		) {
			$this->preRouteHandlerSetUpUrlAdresses();
			$this->preRouteHandlerSetUpRouter();
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
		$router = & $routerClass::GetInstance();
		if ($this->IsAuthenticated()) {
			$router->AddRoute(
				$this->getInitializedRoute($this->signOutCtrlClass, 'SignOut'), TRUE
			);
		} else {
			$router->AddRoute(
				$this->getInitializedRoute($this->signInCtrlClass, 'SignIn'), TRUE
			);
		}
	}

	protected function initializeAuthForm () {
		$controller = & $this->application->GetController();
		$routerClass = $this->application->GetRouterClass();
		$router = & $routerClass::GetInstance();
		$successUrl = '';
		if ($this->IsAuthenticated()) {
			$this->form = new \MvcCore\Ext\Auth\Basic\SignOutForm($controller);
			$route = $this->getInitializedRoute($this->signOutCtrlClass, 'SignOut');
			$successUrl = $this->signedOutUrl;
			$cssClass = 'sign-out';
		} else {
			$this->form = new \MvcCore\Ext\Auth\Basic\SignInForm($controller);
			$route = $this->getInitializedRoute($this->signInCtrlClass, 'SignIn');
			$successUrl = $this->signedInUrl;
			$cssClass = 'sign-in';
		}
		$routeName = $route->GetName();
		$routerHasRoute = $router->HasRoute($routeName);
		if (!$routerHasRoute) $router->AddRoute($route, FALSE, FALSE);
		$actionUrl = $router->Url($routeName);
		if (!$routerHasRoute) $router->RemoveRoute($routeName);
		$method = $route->GetMethod();
		$this->form
			->SetId('authentication')
			->SetCssClass($cssClass)
			->SetMethod($method !== NULL ? $method : \MvcCore\Interfaces\IRequest::METHOD_POST)
			->SetAction($actionUrl)
			->SetSuccessUrl($successUrl)
			->SetErrorUrl($this->signErrorUrl)
			->SetTranslator($this->translator)
			->Init();
	}

	/**
	 * Prepare configured route record into route instance if record is string or array.
	 * @param string $routeName
	 * @param string $actionName
	 * @return \MvcCore\Route|\MvcCore\Interfaces\IRoute
	 */
	protected function getInitializedRoute ($controllerName, $actionName) {
		$routeName = lcfirst($actionName) . 'Route';
		$rawRoute = & $this->$routeName;
		if ($rawRoute instanceof \MvcCore\Interfaces\IRoute) {
			return $rawRoute;
		} else {
			$routeClass = $this->application->GetRouteClass();
			$routeInitData = array('controller' => $controllerName, 'action' => $actionName);
			$route = $routeClass::GetInstance(
				gettype($rawRoute) == 'array'
					? array_merge($routeInitData, $rawRoute)
					: array_merge(array('pattern' => $rawRoute), $routeInitData)
			);
			$this->$routeName = & $route;
			return $route;
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
