<?php

namespace MvcCore\Ext\Auth\Basics\Traits\User;

trait PropsGettersSetters
{
	/**
	 * User unique id, representing primary key in database
	 * or sequence number in system config.
	 * Example: `0 | 1 | 2...`
	 * @var int|NULL
	 */
	protected $id = NULL;

	/**
	 * Unique user name to log in. It could be email,
	 * unique user name or anything uniquelse.
	 * Example: `"admin" | "john" | "tomflidr@gmail.com"`
	 * @var string
	 */
	protected $userName = NULL;

	/**
	 * User full name string to display in application
	 * for authenticated user at sign out button.
	 * Example: `"Administrator" | "John" | "Tom Flidr"`
	 * @var string
	 */
	protected $fullName = NULL;

	/**
	 * Password hash, usually `NULL`. It exists only for authentication moment.
	 * From moment, when is user instance loaded with password hash by session username to
	 * moment, when is compared hashed sended password and stored password hash.
	 * After password hashes comparation, password hash is unseted.
	 * @var string|NULL
	 */
	protected $passwordHash = NULL;

	/**
	 * MvcCore session namespace instance
	 * to get/clear username record from session
	 * to load user for authentication.
	 * @var \MvcCore\Session|\MvcCore\Interfaces\ISession
	 */
	protected static $userSessionNamespace = NULL;


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
	 * Unique user name to log in. It could be email,
	 * unique user name or anything uniquelse.
	 * Example: `"admin" | "john" | "tomflidr@gmail.com"`
	 * @var string
	 */
	public function GetUserName () {
		return $this->userName;
	}

	/**
	 * User full name string to display in application
	 * for authenticated user at sign out button.
	 * Example: `"Administrator" | "John" | "Tom Flidr"`
	 * @var string
	 */
	public function GetFullName () {
		return $this->fullName;
	}

	/**
	 * Password hash, usually `NULL`. It exists only for authentication moment.
	 * From moment, when is user instance loaded with password hash by session username to
	 * moment, when is compared hashed sended password and stored password hash.
	 * After password hashes comparation, password hash is unseted.
	 * @var string|NULL
	 */
	public function GetPasswordHash () {
		return $this->passwordHash;
	}

	/**
	 * MvcCore session namespace instance
	 * to get/clear username record from session
	 * to load user for authentication.
	 * Session is automaticly started if necessary
	 * by `\MvcCore\Session::GetNamespace();`.
	 * @return \MvcCore\Session|\MvcCore\Interfaces\ISession
	 */
	public static function & GetUserSessionNamespace () {
		if (static::$userSessionNamespace === NULL) {
			$sessionClass = \MvcCore\Application::GetInstance()->GetSessionClass();
			static::$userSessionNamespace = $sessionClass::GetNamespace(\MvcCore\Ext\Auth\Basic::class);
			static::$userSessionNamespace->SetExpirationSeconds(
				\MvcCore\Ext\Auth\Basic::GetInstance()->GetExpirationSeconds()
			);
		}
		return static::$userSessionNamespace;
	}


	/**
	 * Set user unique id, representing primary key in database
	 * or sequence number in system config.
	 * Example: `0 | 1 | 2...`
	 * @param int|NULL $id
	 * @return \MvcCore\Ext\Auth\Basics\User|\MvcCore\Ext\Auth\Basics\Interfaces\IUser
	 */
	public function & SetId ($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Set unique user name to log in. It could be email,
	 * unique user name or anything uniquelse.
	 * Example: `"admin" | "john" | "tomflidr@gmail.com"`
	 * @param string $userName
	 * @return \MvcCore\Ext\Auth\Basics\User|\MvcCore\Ext\Auth\Basics\Interfaces\IUser
	 */
	public function & SetUserName ($userName) {
		$this->userName = $userName;
	}

	/**
	 * Set user full name string to display in application
	 * for authenticated user at sign out button.
	 * Example: `"Administrator" | "John" | "Tom Flidr"`
	 * @param string $fullName
	 * @return \MvcCore\Ext\Auth\Basics\User|\MvcCore\Ext\Auth\Basics\Interfaces\IUser
	 */
	public function & SetFullName ($fullName) {
		$this->fullName = $fullName;
	}

	/**
	 * Set password hash, usually `NULL`. It exists only for authentication moment.
	 * From moment, when is user instance loaded with password hash by session username to
	 * moment, when is compared hashed sended password and stored password hash.
	 * After password hashes comparation, password hash is unseted.
	 * @param string|NULL $passwordHash
	 * @return \MvcCore\Ext\Auth\Basics\User|\MvcCore\Ext\Auth\Basics\Interfaces\IUser
	 */
	public function & SetPasswordHash ($passwordHash) {
		$this->passwordHash = $passwordHash;
		return $this;
	}

	/**
	 * Summary of SetUserSessionNamespace
	 * @param \MvcCore\Session|\MvcCore\Interfaces\ISession $userSessionNamespace
	 * @return \MvcCore\Session|\MvcCore\Interfaces\ISession
	 */
	public static function & SetUserSessionNamespace (\MvcCore\Interfaces\ISession & $userSessionNamespace) {
		static::$userSessionNamespace = $userSessionNamespace;
		return $userSessionNamespace;
	}
}
