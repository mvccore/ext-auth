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

namespace MvcCore\Ext\Auth\Basics;

/**
 * Responsibility - base role model class.
 * This class is necessary to extend and implement method
 * `GetByName()` or more. It's also necessary to implement 
 * loading users with roles and their permitions to be able 
 * to check user roles and permissions.
 */
class Role
	extends		\MvcCore\Model
	implements	\MvcCore\Ext\Auth\Basics\Interfaces\IRole
{
	use \MvcCore\Ext\Auth\Basics\Traits\UserAndRole\Base,
		\MvcCore\Ext\Auth\Basics\Traits\UserAndRole\Permissions,
		\MvcCore\Ext\Auth\Basics\Traits\Role;

	/**
	 * Do not automaticly initialize protected properties
	 * `$user->db`, `$user->config` and `$user->resource`.
	 * @var bool
	 */
	protected $autoInit = FALSE;

	/**
	 * Get role instance from application roles list. It could be database or any other custom resource.
	 * @param string $name Role unique name.
	 * @throws \RuntimeException
	 * @return \MvcCore\Ext\Auth\Basics\Role|\MvcCore\Ext\Auth\Basics\Interfaces\IRole
	 */
	public function GetByName ($roleName) {
		throw new \RuntimeException(
			'['.__CLASS__.'] Method is not implemented. '
			.'Extend class `'.__CLASS__.'` and implement method `GetByName ($roleName)` by your own.'
		);
	}
}
