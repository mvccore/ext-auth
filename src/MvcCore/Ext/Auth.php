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
	 * Return singleton instance. If instance exists, return existing instance,
	 * if not, create new authentication module instance by existing classes,
	 * store it and return it. Try to create authentication modules in this order:
	 * - `\MvcCore\Ext\Auths\Full`
	 * - `\MvcCore\Ext\Auths\Extended`
	 * - `\MvcCore\Ext\Auths\Basic`	- always loaded
	 * @param array $configuration Optional configuration passed into method
	 *                             `\MvcCore\Ext\Auths\Basic::__construct($configuration)`.
	 * @return \MvcCore\Ext\Auths\Full|\MvcCore\Ext\Auths\Extended|\MvcCore\Ext\Auths\Basic|\MvcCore\Ext\Auths\Basics\Interfaces\IAuth
	 */
	public static function & GetInstance ($configuration = array()) {
		if (self::$instance === NULL) {
			if (class_exists('\\MvcCore\\Ext\\Auths\\Full')) {
				self::$instance = \MvcCore\Ext\Auths\Full($configuration);
			} else if (class_exists('\\MvcCore\\Ext\\Auths\\Extended')) {
				self::$instance = \MvcCore\Ext\Auths\Extended($configuration);
			} else {
				self::$instance = \MvcCore\Ext\Auths\Basic($configuration);
			}
		}
		return self::$instance;
	}
}
