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
	protected static $session = NULL;



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

	/**
	 * Try to get user model instance from
	 * any place by session username record
	 * if there is any or return null.
	 * @return \MvcCore\Ext\Auth\User|null
	 */
	public static function SetUpUserBySession () {
		$result = NULL;
		$session = static::getSession();
		if (!isset($session->userName)) return NULL;
		$allCredentials = static::getSystemConfigCredentials();
		foreach ($allCredentials as $key => & $credentials) {
			if ($credentials->username === $session->userName) {
				$result = (new static())
					->SetId($key)
					->SetUserName($credentials->username)
					->SetFullName($credentials->fullname);
				break;
			}
		}
		return $result;
	}

	/**
	 * Get user instance if the username exists and hashed password is the same.
	 * @param string $userName
	 * @param string $password
	 * @return \MvcCore\Ext\Auth\User|NULL
	 */
	public static function LogIn ($userName = '', $password = '') {
		$result = NULL;
		$hashedPassword = static::EncodePasswordToHash($password);
		$allCredentials = static::getSystemConfigCredentials();
		foreach ($allCredentials as $key => & $credentials) {
			if ($credentials->username === $userName) {
				if ($credentials->password === $hashedPassword) {
					$result = (new static())
						->SetId($key)
						->SetUserName($credentials->username)
						->SetFullName($credentials->fullname);
					static::GetSession()->userName = $result->userName;
				}
				break;
			}
		}
		return $result;
	}

	/**
	 * Destroy user credentials in session storrage.
	 * @return void
	 */
	public static function LogOut () {
		static::GetSession()->Destroy();
	}

	/**
	 * Get any password hash with salt by Auth extension configuration
	 * @param string $password
	 * @return string
	 */
	public static function EncodePasswordToHash ($password = '', $options = array()) {
		if (!isset($options['salt'])) {
			$configuredSalt = \MvcCore\Ext\Auth::GetInstance()->GetConfig()->passwordHashSalt;
			if ($configuredSalt !== NULL) $options['salt'] = $configuredSalt;
		}
		if (!isset($options['cost']))
			$options['cost'] = 31;
		if ($options['cost'] < 4 || $options['cost'] > 31)
			throw new \InvalidArgumentException(
				'['.__CLASS__.'] Cost option has to be from `4` to `31`, `' . $options['cost'] . '` given.'
			);
		$result = password_hash($password, PASSWORD_BCRYPT, $options);
		if ($result === FALSE || strlen($result) < 60) throw new \RuntimeException(
			'['.__CLASS__.'] Hash computed by `password_hash()` is invalid.'
		);
		return $result;
	}

	/**
	 * Get session to get/set/clear username,
	 * if session is not started - start the session.
	 * @return \MvcCore\Session
	 */
	protected static function & getSession () {
		if (static::$session === NULL) {
			$app = \MvcCore\Application::GetInstance();
			$app->SessionStart(); // start session if not started or do nothing if session has been started already
			$sessionClass = $app->GetSessionClass();
			static::$session = $sessionClass::GetNamespace(__CLASS__);
			static::$session->SetExpirationSeconds(
				\MvcCore\Ext\Auth::GetInstance()->GetConfig()->expirationSeconds
			);
		}
		return static::$session;
	}

	protected static function getSystemConfigCredentials () {
		$configClass = \MvcCore\Application::GetInstance()->GetConfigClass();
		return $configClass::GetSystem()->credentials;
	}
}
