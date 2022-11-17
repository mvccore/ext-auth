<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext;

/**
 * Responsibility - create authentication module by loaded (existing) classes.
 */
class Auth extends \MvcCore\Ext\Auths\Basic {

	/**
	 * MvcCore Extension - Auth - version:
	 * Comparison by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.2.0';

	/**
	 * Full authentication module type with all features.
	 */
	const AUTH_CLASS_FULL = '\\MvcCore\\Ext\\Auths\\Full';

	/**
	 * Basic authentication module type with signin/signout form, system config user or database user only.
	 */
	const AUTH_CLASS_BASIC = '\\MvcCore\\Ext\\Auths\\Basic';

	/**
	 * Authentication module type. Possible values: `NULL | "full" | "basic"`.
	 * @var string|NULL
	 */
	protected static $authType = NULL;

	/**
	 * Detected or configured authentication class name.
	 * @var string|NULL
	 */
	protected static $authClass = NULL;

	/**
	 * Return authentication module full class name.
	 * @return string|NULL
	 */
	public static function GetAuthClass () {
		if (self::$authClass === NULL) {
			if (class_exists(self::AUTH_CLASS_FULL)) {
				self::$authClass = self::AUTH_CLASS_FULL;
			} else {
				self::$authClass = self::AUTH_CLASS_BASIC;
			}
		}
		return self::$authClass;
	}

	/**
	 * Set authentication module full class name implementing `\MvcCore\Ext\Auths\IBasic`.
	 * @return string|NULL
	 */
	public static function SetAuthClass ($authClass) {
		$toolClass = \MvcCore\Application::GetInstance()->GetToolClass();
		if ($toolClass::CheckClassInterface($authClass, 'MvcCore\\Ext\\Auths\\IBasic', TRUE, TRUE)) 
			self::$authClass = $authClass;
	}

	/**
	 * Return singleton instance. If instance exists, return existing instance,
	 * if not, create new authentication module instance by existing classes,
	 * store it and return it. Try to create authentication modules in this order:
	 * - `\MvcCore\Ext\Auths\Full` - if class exists
	 * - `\MvcCore\Ext\Auths\Basic`	- always loaded
	 * @param array $configuration Optional configuration passed into method
	 *							 `\MvcCore\Ext\Auths\Basic::__construct($configuration)`.
	 * @return \MvcCore\Ext\Auths\Full|\MvcCore\Ext\Auths\Basic
	 */
	public static function GetInstance ($configuration = []) {
		if (self::$instance === NULL) {
			$authClass = self::GetAuthClass();
			self::$instance = $authClass::GetInstance($configuration);
		}
		return self::$instance;
	}
}
