<?php

namespace MvcCore\Ext\Auth\Basics\Traits\User;

trait Auth
{
	public static function SetUpUserBySession () {
		$userSessionNamespace = static::GetUserSessionNamespace();
		$sessionUserNameKey = \MvcCore\Ext\Auth\Basics\Interfaces\IUser::SESSION_USERNAME_KEY;
		$sessionAuthenticatedKey = \MvcCore\Ext\Auth\Basics\Interfaces\IUser::SESSION_AUTHENTICATED_KEY;
		if (
			isset($userSessionNamespace->$sessionUserNameKey) &&
			isset($userSessionNamespace->$sessionAuthenticatedKey) &&
			$userSessionNamespace->$sessionAuthenticatedKey
		) {
			return static::GetByUserName($userSessionNamespace->$sessionUserNameKey);
		}
		return NULL;
	}

	public static function LogIn ($userName = '', $password = '') {
		$hashedPassword = static::EncodePasswordToHash($password);
		$user = static::GetByUserName($userName);
		if ($user && $user->passwordHash === $hashedPassword) {
			$userSessionNamespace = & static::GetUserSessionNamespace();
			$userSessionNamespace->{\MvcCore\Ext\Auth\Basics\Interfaces\IUser::SESSION_USERNAME_KEY} = $user->userName;
			$userSessionNamespace->{\MvcCore\Ext\Auth\Basics\Interfaces\IUser::SESSION_AUTHENTICATED_KEY} = TRUE;
			return $user;
		}
		return NULL;
	}

	/**
	 * Destroy user credentials in session storrage.
	 * @return void
	 */
	public static function LogOut ($destroyWholeSession = FALSE) {
		$userSessionNamespace = & static::GetUserSessionNamespace();
		if ($destroyWholeSession) {
			static::GetUserSessionNamespace()->Destroy();
		} else {
			$userSessionNamespace->{\MvcCore\Ext\Auth\Basics\Interfaces\IUser::SESSION_AUTHENTICATED_KEY} = FALSE;
		}
	}

	/**
	 * Get any password hash with salt by Auth extension configuration
	 * @param string $password
	 * @return string
	 */
	public static function EncodePasswordToHash ($password = '', $options = array()) {
		if (!isset($options['salt'])) {
			$configuredSalt = \MvcCore\Ext\Auth\Basic::GetInstance()->GetPasswordHashSalt();
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
}
