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

class User extends \MvcCore\Model implements \MvcCore\Ext\Auth\Basics\Interfaces\IUser {

	/** @var bool */
	protected $autoInit = FALSE;

	use \MvcCore\Ext\Auth\Basics\Traits\User\PropsGettersSetters;
	use \MvcCore\Ext\Auth\Basics\Traits\User\Roles;
	use \MvcCore\Ext\Auth\Basics\Traits\User\Auth;

	/**
	 * Get user model instance from database using 
	 * submitted and cleaned `$userName` field value.
	 * @param string $userName
	 * @return \MvcCore\Ext\Auth\Basics\User|\MvcCore\Ext\Auth\Basics\Interfaces\IUser
	 */
	public static function GetByUserName ($userName) {
		throw new \RuntimeException('['.__CLASS__.'] Method not implemented. Use ');
	}
}
