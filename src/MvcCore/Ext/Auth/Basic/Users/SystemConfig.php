<?php

namespace MvcCore\Ext\Auth\Users;

class SystemConfig extends \MvcCore\Ext\Auth\Basic\User
{
	public static function GetByUserName ($userName) {
		$result = NULL;
		$configClass = \MvcCore\Application::GetInstance()->GetConfigClass();
		$allSysConfigCredentials = $configClass::GetSystem()->credentials;
		foreach ($allSysConfigCredentials as $key => & $sysConfigCredentials) {
			if ($sysConfigCredentials->username === $userName) {
				$result = (new static())
					->SetId($key)
					->SetUserName($sysConfigCredentials->username)
					->SetFullName($sysConfigCredentials->fullname);
				break;
			}
		}
		return $result;
	}
}
