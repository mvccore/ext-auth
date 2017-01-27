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

namespace MvcCore\Ext\Auth\Virtual;

class User extends \MvcCore\Model {

	/** @var int */
	public $Id = NULL;

	/** @var string */
	public $UserName = '';

	/** @var string */
	public $FullName = '';

	/** @var string */
	public $PasswordHash = '';

	/**
	 * Try to get user model instance from
	 * any place by session username record
	 * if there is any or return null.
	 * @return \MvcCore\Ext\Auth\User|null
	 */
	public static function GetUserBySession () {
		return NULL;
	}

	/**
	 * Get user instance if the username exists and hashed password is the same
	 * @param string $username
	 * @param string $password
	 * @return \MvcCore\Ext\Auth\User|null
	 */
	public static function Authenticate ($username = '', $password = '') {
		return NULL;
	}

	/**
	 * Set up unique user name in session namespace
	 * @param string $uniqueUserName
	 * @return void
	 */
	public static function StoreInSession ($uniqueUserName = '') {
	}

	/**
	 * Clear unique user name from session
	 * @return void
	 */
	public static function ClearFromSession () {
	}

	/**
	 * Get any password hash with salt by Auth extension configuration
	 * @param string $password
	 * @return string
	 */
	public static function GetPasswordHash ($password = '') {
		return sha1(crypt(
			(string) $password, 
			\MvcCore\Ext\Auth::GetInstance()->GetConfig()->passwordHashSalt /*. $_SERVER['SERVER_NAME']*/
		));
	}
}
