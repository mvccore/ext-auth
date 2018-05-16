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

namespace MvcCore\Ext\Auth\Traits;

trait User {

	/** @var int */
	protected $id = NULL;

	/** @var string */
	protected $userName = NULL;

	/** @var string */
	protected $fullName = NULL;

	/** @var string */
	protected $passwordHash = NULL;

	/** @var \MvcCore\Session */
	protected static $userSessionNamespace = NULL;



	public function GetId () {
		return $this->id;
	}

	public function & SetId ($id) {
		$this->id = $id;
		return $this;
	}

	public function GetUserName () {
		return $this->userName;
	}

	public function & SetUserName ($userName) {
		$this->userName = $userName;
		return $this;
	}

	public function GetFullName () {
		return $this->fullName;
	}

	public function & SetFullName ($fullName) {
		$this->fullName = $fullName;
		return $this;
	}

	public function GetPasswordHash () {
		return $this->passwordHash;
	}

	public function & SetPasswordHash ($passwordHash) {
		$this->passwordHash = $passwordHash;
		return $this;
	}


	public static function SetUpUserBySession () {
		$userSessionNamespace = static::getUserSessionNamespace();
		if (isset($userSessionNamespace->userName)) {
			return static::GetByUserName($userSessionNamespace->userName);
		}
		return NULL;
	}

	public static function LogIn ($userName = '', $password = '') {
		$hashedPassword = static::EncodePasswordToHash($password);
		$user = static::GetByUserName($userName);
		if ($user && $user->passwordHash === $hashedPassword) {
			static::getUserSessionNamespace()->userName = $user->userName;
			return $user;
		}
		return NULL;
	}

	/**
	 * Destroy user credentials in session storrage.
	 * @return void
	 */
	public static function LogOut () {
		static::getUserSessionNamespace()->Destroy();
	}
	public function & GetRoles () {
		return $this;
	}
	public function & SetRoles ($roles = array()){
		return $this;
	}
	public function & AddRole ($role){
		return $this;
	}
	public function & RemoveRole ($role){
		return $this;
	}

	/**
	 * Get any password hash with salt by Auth extension configuration
	 * @param string $password
	 * @return string
	 */
	public static function EncodePasswordToHash ($password = '', $options = array()) {
		if (!isset($options['salt'])) {
			$configuredSalt = \MvcCore\Ext\Auth::GetInstance()->GetConfig()->passwordHashSalt;
			if ($configuredSalt !== NULL) {
				$options['salt'] = $configuredSalt;
			} else {
				throw new \InvalidArgumentException(
					'['.__CLASS__.'] No option `salt` given by second argument `$options`'
					." or no salt configured by `\MvcCore\Ext\Auth::GetInstance()->SetPasswordHashSalt('...')`."
				);
			}
		}
		if (isset($options['cost']) && ($options['cost'] < 4 || $options['cost'] > 31))
			throw new \InvalidArgumentException(
				'['.__CLASS__.'] `cost` option has to be from `4` to `31`, `' . $options['cost'] . '` given.'
			);
		$result = @password_hash($password, PASSWORD_BCRYPT, $options);
		if (!$result || strlen($result) < 60) throw new \RuntimeException(
			'['.__CLASS__.'] Hash computed by `password_hash()` is invalid.'
		);
		return $result;
	}

	/**
	 * Get session to get/set/clear username,
	 * if session is not started - start the session.
	 * @return \MvcCore\Session
	 */
	protected static function & getUserSessionNamespace () {
		if (static::$userSessionNamespace === NULL) {
			$app = \MvcCore\Application::GetInstance();
			$app->SessionStart(); // start session if not started or do nothing if session has been started already
			$sessionClass = $app->GetSessionClass();
			static::$userSessionNamespace = $sessionClass::GetNamespace(\MvcCore\Ext\Auth::class);
			static::$userSessionNamespace->SetExpirationSeconds(
				\MvcCore\Ext\Auth::GetInstance()->GetConfig()->expirationSeconds
			);
		}
		return static::$userSessionNamespace;
	}
}
