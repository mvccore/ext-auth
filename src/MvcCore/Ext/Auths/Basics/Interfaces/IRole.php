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

namespace MvcCore\Ext\Auths\Basics\Interfaces;

/**
 * Responsibility - base role model class.
 */
interface IRole
{
	// trait: \MvcCore\Ext\Auths\Basics\Traits\UserAndRole\Base

	/**
	 * User unique id, representing primary key in database
	 * or sequence number in system config.
	 * Example: `0 | 1 | 2...`
	 * @return int|NULL
	 */
	public function GetId ();

	/**
	 * Set user unique id, representing primary key in database
	 * or sequence number in system config.
	 * Example: `0 | 1 | 2...`
	 * @param int|NULL $id
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IRole
	 */
	public function & SetId ($id);

	/**
	 * Get user active state boolean. `TRUE` for active, `FALSE` otherwise.
	 * This function is only alias for `$user->GetActive();`.
	 * @return bool
	 */
	public function IsActive ();

	/**
	 * Get user active state boolean. `TRUE` for active, `FALSE` otherwise.
	 * @return bool
	 */
	public function GetActive ();

	/**
	 * Set user active state boolean. `TRUE` for active, `FALSE` otherwise.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IRole
	 */
	public function & SetActive ($active);


	// trait: \MvcCore\Ext\Auths\Basics\Traits\UserAndRole\Permissions

	/**
	 * Get `TRUE` if given permission string is allowed for user or role. `FALSE` otherwise.
	 * @param string $permissionName
	 * @return bool
	 */
	public function GetPermission ($permissionName);

	/**
	 * Set `$permissionName` string with `$allow` boolean to allow
	 * or to disallow permission (with `$allow = FALSE`) for user or role.
	 * @param string $permissionName Strings describing what is allowed/disallowed to do for user or role.
	 * @param bool $allow `TRUE` by default.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IRole
	 */
	public function & SetPermission ($permissionName, $allow = TRUE);

	/**
	 * Get array of strings describing what is allowed to do for user or role.
	 * @return \string[]
	 */
	public function & GetPermissions();

	/**
	 * Set array of strings describing what is allowed to do for user or role.
	 * @param string|\string[] $permissions Permitions string, separated by comma character or array of strings.
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IRole
	 */
	public function & SetPermissions ($permissions);


	// trait: \MvcCore\Ext\Auths\Basics\Traits\Role

	/**
	 * Get unique role name.
	 * Example: `"management" | "editor" | "quest"`
	 * @return string
	 */
	public function GetName ();

	/**
	 * Set unique role name.
	 * Example: `"management" | "editor" | "quest"`
	 * @param string $name
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IRole
	 */
	public function & SetName ($name);

	/**
	 * Get `TRUE` if given permission string is allowed for role. `FALSE` otherwise.
	 * @param string $permissionName
	 * @return bool
	 */
	public function IsAllowed ($permissionName);


	// class: \MvcCore\Ext\Auths\Basics\Role

	/**
	 * Get role instance from application roles list. It could be database or any other custom resource.
	 * @param string $name Role unique name.
	 * @throws \RuntimeException
	 * @return \MvcCore\Ext\Auths\Basics\Interfaces\IRole
	 */
	public function GetByName ($roleName);
}
