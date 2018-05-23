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

namespace MvcCore\Ext\Auths\Basics\Traits\User;

/**
 * Trait for `\MvcCore\Ext\Auths\Basics\User` class. Trait contains:
 * - Instance property `$admin` and `$roles` with their public getters and setters to manupulate with user roles.
 * - Method `IsAllowed()` to get allowed permissions from user instance or from user roles.
 */
trait Roles
{
	/**
	 * `TRUE` if user is administrator. Administrator has always allowed everything.
	 * Default value is `FALSE`.
	 * @var bool
	 */
	protected $admin = FALSE;

	/**
	 * Array of roles names assigned for current user instance.
	 * @var \string[]
	 */
	protected $roles = array();

	/**
	 * Get if user is Administrator. Administrator has always allowed everything.
	 * @return bool
	 */
	public function IsAdmin() {
		return $this->admin;
	}

	/**
	 * Get if user is Administrator. Administrator has always allowed everything.
	 * @return bool
	 */
	public function GetAdmin() {
		return $this->admin;
	}

	/**
	 * Set user to Administrator. Administrator has always allowed everything.
	 * @param bool $admin `TRUE` by default.
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser
	 */
	public function & SetAdmin ($admin = TRUE) {
		$this->admin = (bool) $admin;
		return $this;
	}

	/**
	 * Return array of user's roles names.
	 * @return \string[]
	 */
	public function & GetRoles () {
		return $this->roles;
	}

	/**
	 * Set new user's roles or roles names.
	 * @param \string[]|\MvcCore\Ext\Auths\Basics\Role[]|\MvcCore\Ext\Auths\Basics\Interfaces\IRole[] $rolesOrRolesNames
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser
	 */
	public function & SetRoles ($rolesOrRolesNames = array()) {
		$this->roles = array();
		foreach ($rolesOrRolesNames as $roleOrRoleName)
			$this->roles[] = static::getRoleName($roleOrRoleName);
		return $this;
	}

	/**
	 * Add user role or role name.
	 * @param string|\MvcCore\Ext\Auths\Basics\Role|\MvcCore\Ext\Auths\Basics\Interfaces\IRole $roleOrRoleName
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser
	 */
	public function & AddRole ($roleOrRoleName) {
		$roleName = static::getRoleName($roleOrRoleName);
		if (!in_array($roleName, $this->roles))
			$this->roles[] = $roleName;
		return $this;
	}

	/**
	 * Get `TRUE` if user has already assigned role or role name.
	 * @param string|\MvcCore\Ext\Auths\Basics\Role|\MvcCore\Ext\Auths\Basics\Interfaces\IRole $roleOrRoleName
	 * @throws \InvalidArgumentException
	 * @return bool
	 */
	public function HasRole ($roleOrRoleName) {
		$roleName = static::getRoleName($roleOrRoleName);
		return in_array($roleName, $this->roles);
	}

	/**
	 * Remove user role or role name from user roles.
	 * @param string|\MvcCore\Ext\Auths\Basics\Role|\MvcCore\Ext\Auths\Basics\Interfaces\IRole $roleOrRoleName
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser
	 */
	public function & RemoveRole ($roleOrRoleName) {
		$roleName = static::getRoleName($roleOrRoleName);
		$position = array_search($roleName, $this->roles);
		if ($position !== FALSE) array_splice($this->roles, $position, 1);
		return $this;
	}

	/**
	 * Get `TRUE` if given permission string is allowed for user or user role. `FALSE` otherwise.
	 * @param string $permissionName
	 * @return bool
	 */
	public function IsAllowed ($permissionName) {
		// check direct user permitions
		if ($this->GetPermission($permissionName)) return TRUE;
		// check permitions on user roles
		$roleClass = \MvcCore\Ext\Auths\Basic::GetInstance()->GetRoleClass();
		foreach ($this->GetRoles() as $roleName) {
			$role = $roleClass::GetByName($roleName);
			if ($role->GetPermission($permissionName)) return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get role name from given role instance or given role name.
	 * @param string|\MvcCore\Ext\Auths\Basics\Role|\MvcCore\Ext\Auths\Basics\Interfaces\IRole $roleOrRoleName
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	protected static function getRoleName ($roleOrRoleName) {
		if (is_string($roleOrRoleName)) {
			return $roleOrRoleName;
		} else if ($roleOrRoleName instanceof \MvcCore\Ext\Auths\Basics\Interfaces\IRole) {
			return $roleOrRoleName->GetName();
		} else {
			throw new \InvalidArgumentException(
				'['.__CLASS__."] Given argument `$roleOrRoleName` doesn't "
				."implement interface `\MvcCore\Ext\Auths\Basics\Interfaces\IRole` "
				."or it's not string with role name."
			);
		}
	}
}
