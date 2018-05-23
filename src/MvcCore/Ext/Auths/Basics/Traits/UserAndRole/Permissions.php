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

namespace MvcCore\Ext\Auths\Basics\Traits\UserAndRole;

/**
 * Trait for `\MvcCore\Ext\Auths\Basics\User` and `\MvcCore\Ext\Auths\Basics\Role` class. Trait contains:
 * - Instance property `$permissions` with their public getters and setters to manupulate with permitions.
 */
trait Permissions
{
	/**
	 * Array of strings describing what is allowed to do for user or role.
	 * @var \string[]
	 */
	protected $permissions = array();

	/**
	 * Get `TRUE` if given permission string is allowed for user or role. `FALSE` otherwise.
	 * @param string $permissionName
	 * @return bool
	 */
	public function GetPermission ($permissionName) {
		if ($this->admin) return TRUE;
		if (in_array($permissionName, $this->permissions)) return TRUE;
		return FALSE;
	}

	/**
	 * Set `$permissionName` string with `$allow` boolean to allow
	 * or to disallow permission (with `$allow = FALSE`) for user or role.
	 * @param string $permissionName Strings describing what is allowed/disallowed to do for user or role.
	 * @param bool $allow `TRUE` by default.
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser|\MvcCore\Ext\Auths\Basics\Role|\MvcCore\Ext\Auths\Basics\Interfaces\IRole
	 */
	public function & SetPermission ($permissionName, $allow = TRUE) {
		if (!in_array($permissionName, $this->permissions) && $allow) {
			$this->permissions[] = $permissionName;
		} else if (in_array($permissionName, $this->permissions) && !$allow) {
			$position = array_search($permissionName, $this->permissions);
			if ($position !== FALSE) array_splice($this->permissions, $position, 1);
		}
		return $this;
	}

	/**
	 * Get array of strings describing what is allowed to do for user or role.
	 * @return \string[]
	 */
	public function & GetPermissions() {
		return $this->permissions;
	}

	/**
	 * Set array of strings describing what is allowed to do for user or role.
	 * @param string|\string[] $permissions Permitions string, separated by comma character or array of strings.
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser|\MvcCore\Ext\Auths\Basics\Role|\MvcCore\Ext\Auths\Basics\Interfaces\IRole
	 */
	public function & SetPermissions ($permissions) {
		if (is_string($permissions)) {
			$this->permissions = explode(',', $permissions);
		} else if (is_array($permissions)) {
			$this->permissions = $permissions;
		}
		return $this;
	}
}
