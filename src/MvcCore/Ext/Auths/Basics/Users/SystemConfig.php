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

namespace MvcCore\Ext\Auths\Users;

/**
 * Responsibility - simply and only load user instance from system config, ids by users list sequence.
 */
class SystemConfig extends \MvcCore\Ext\Auths\Basics\User
{
	/**
	 * Get user model instance from system config or any other users list
	 * resource by submitted and cleaned `$userName` field value.
	 * @param string $userName Submitted and cleaned username. Characters `' " ` < > \ = ^ | & ~` are automaticly encoded to html entities by default `\MvcCore\Ext\Auths\Basic` sign in form.
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser
	 */
	public static function & GetByUserName ($userName) {
		$result = NULL;
		$configClass = \MvcCore\Application::GetInstance()->GetConfigClass();
		$allSysConfigCredentials = $configClass::GetSystem()->users;
		foreach ($allSysConfigCredentials as $key => & $sysConfigCredentials) {
			if ($sysConfigCredentials->userName === $userName) {
				$result = (new static())
					->SetId($key)
					->SetUserName($sysConfigCredentials->userName)
					->SetFullName($sysConfigCredentials->fullName)
					->SetPasswordHash($sysConfigCredentials->passwordHash);
				break;
			}
		}
		return $result;
	}
}
