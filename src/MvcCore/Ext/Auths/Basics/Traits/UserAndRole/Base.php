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
 * - Instance properties `$id` and `$active` with their public getters and setters.
 */
trait Base
{
	/**
	 * User or role unique id, representing primary key in database
	 * or sequence number in system config.
	 * Example: `0 | 1 | 2...`
	 * @var int|NULL
	 */
	protected $id = NULL;

	/**
	 * User or role active state boolean.
	 * @var bool
	 */
	protected $active = TRUE;

	/**
	 * User unique id, representing primary key in database
	 * or sequence number in system config.
	 * Example: `0 | 1 | 2...`
	 * @return int|NULL
	 */
	public function GetId () {
		return $this->id;
	}

	/**
	 * Set user unique id, representing primary key in database
	 * or sequence number in system config.
	 * Example: `0 | 1 | 2...`
	 * @param int|NULL $id
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser|\MvcCore\Ext\Auths\Basics\Role|\MvcCore\Ext\Auths\Basics\Interfaces\IRole
	 */
	public function & SetId ($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Get user active state boolean. `TRUE` for active, `FALSE` otherwise.
	 * This function is only alias for `$user->GetActive();`.
	 * @return bool
	 */
	public function IsActive () {
		return $this->active;
	}

	/**
	 * Get user active state boolean. `TRUE` for active, `FALSE` otherwise.
	 * @return bool
	 */
	public function GetActive () {
		return $this->active;
	}

	/**
	 * Set user active state boolean. `TRUE` for active, `FALSE` otherwise.
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser|\MvcCore\Ext\Auths\Basics\Role|\MvcCore\Ext\Auths\Basics\Interfaces\IRole
	 */
	public function & SetActive ($active) {
		$this->active = (bool) $active;
		return $this;
	}
}
