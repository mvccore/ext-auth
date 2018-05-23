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

namespace MvcCore\Ext\Auths\Basics\Traits;

/**
 * Trait for `\MvcCore\Ext\Auths\Basics\Role` class. Trait contains:
 * - `$name` property, it's public getter and setter.
 * - public `IsAllowed()` method.
 */
trait Role
{
	/**
	 * Unique role name.
	 * Example: `"management" | "editor" | "quest"`
	 * @var string
	 */
	protected $name = NULL;

	/**
	 * Get unique role name.
	 * Example: `"management" | "editor" | "quest"`
	 * @return string
	 */
	public function GetName () {
		return $this->name;
	}

	/**
	 * Set unique role name.
	 * Example: `"management" | "editor" | "quest"`
	 * @param string $name
	 * @return \MvcCore\Ext\Auths\Basics\Role|\MvcCore\Ext\Auths\Basics\Interfaces\IRole
	 */
	public function & SetName ($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * Get `TRUE` if given permission string is allowed for role. `FALSE` otherwise.
	 * @param string $permissionName
	 * @return bool
	 */
	public function IsAllowed ($permissionName) {
		return $this->GetPermission($permissionName);
	}
}
