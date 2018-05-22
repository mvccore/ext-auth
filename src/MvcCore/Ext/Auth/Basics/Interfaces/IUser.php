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

namespace MvcCore\Ext\Auth\Basics\Interfaces;

interface IUser
{
	const SESSION_USERNAME_KEY = 'userName';
	const SESSION_AUTHENTICATED_KEY = 'authenticated';

	public function GetId ();

	public function & SetId ($id);

	public function GetUserName ();

	public function & SetUserName ($userName);

	public function GetFullName ();

	public function & SetFullName ($fullName);

	public function GetPasswordHash ();

	public function & SetPasswordHash ($passwordHash);

	public function & GetRoles ();

	public function & SetRoles ($roles = array());

	public function & AddRole ($role);

	public function & RemoveRole ($role);

		/**
	 * Try to get user model instance from
	 * any place by session username record
	 * if there is any or return null.
	 * @return \MvcCore\Ext\Auth\User|null
	 */
	public static function SetUpUserBySession ();

	/**
	 * Get user instance if the username exists and hashed password is the same
	 * @param string $username
	 * @param string $password
	 * @return \MvcCore\Ext\Auth\User|null
	 */
	public static function LogIn ($username = '', $password = '');

	/**
	 * Destroy user credentials in session storrage.
	 * @return void
	 */
	public static function LogOut ();

	/**
	 * Get any password hash with salt by Auth extension configuration
	 * @param string $password
	 * @return string
	 */
	public static function EncodePasswordToHash ($password = '');

}
