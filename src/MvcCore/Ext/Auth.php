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
 * Responsibility - create authentication module by loaded (existing) classes.
 */
class Auth extends \MvcCore\Ext\Auths\Basic
{
	/**
	 * Full authentication module type with all features.
	 */
	const TYPE_FULL = 'full';

	/**
	 * Basic authentication module type with signin/signout form, system config user or database user only.
	 */
	const TYPE_BASIC = 'basic';

	/**
	 * Authentication module type. Possible values: `NULL | "full" | "basic"`.
	 * @var string
	 */
	protected static $authType = NULL;

	/**
	 * Return singleton instance. If instance exists, return existing instance,
	 * if not, create new authentication module instance by existing classes,
	 * store it and return it. Try to create authentication modules in this order:
	 * - `\MvcCore\Ext\Auths\Full` - if class exists
	 * - `\MvcCore\Ext\Auths\Basic`	- always loaded
	 * @param array $configuration Optional configuration passed into method
	 *							 `\MvcCore\Ext\Auths\Basic::__construct($configuration)`.
	 * @return \MvcCore\Ext\Auths\Full|\MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\IAuth
	 */
	public static function & GetInstance ($configuration = []) {
		if (self::$instance === NULL) {
			if (self::GetAuthType() == self::TYPE_BASIC) {
				self::$instance = new \MvcCore\Ext\Auths\Basic($configuration);
			} else {
				self::$instance = new \MvcCore\Ext\Auths\Full($configuration);
			}
		}
		return self::$instance;
	}

	/**
	 * Return authentication module type by existing classes.
	 * Return `"full"` if class `\MvcCore\Ext\Auths\Full` exists
	 * or return `"basic"` if doesn't.
	 * @return string
	 */
	public static function GetAuthType () {
		if (self::$authType === NULL) {
			if (class_exists('\\MvcCore\\Ext\\Auths\\Full')) {
				self::$authType = self::TYPE_FULL;
			} else {
				self::$authType = self::TYPE_BASIC;
			}
		}
		return self::$authType;
	}
}
