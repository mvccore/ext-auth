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
 * Responsibility - simply and only load user instance from configurable database table structure.
 */
class Database
	extends		\MvcCore\Ext\Auths\Basics\User
	implements	\MvcCore\Ext\Auths\Basics\Interfaces\IDatabaseUser
{
	/**
	 * Users table nested database structure,
	 * configured by method `SetUsersTableStructure()`
	 * and used by method `GetByUserName()`.
	 * @var array
	 */
	protected static $usersTableStructure = array(
		'table'		=> 'users',
		'columns'	=> array(
			'id'			=> 'id',
			'active'		=> 'active',
			'userName'		=> 'user_name',
			'passwordHash'	=> 'password_hash',
			'fullName'		=> 'full_name',
		)
	);

	/**
	 * Set database table structure how to load user from db.
	 * @param string|NULL	$tableName Database table name.
	 * @param string[]|NULL	$columnNames Keys are user class protected properties names in camel case, values are database columns names.
	 */
	public static function SetUsersTableStructure ($tableName = NULL, $columnNames = NULL) {
		if ($tableName !== NULL) static::$usersTableStructure['table'] = $tableName;
		if ($columnNames !== NULL) {
			$columns = & static::$usersTableStructure['columns'];
			foreach ($columnNames as $propertyName => $columnName)
				$columns[$propertyName] = $columnName;
		}
	}

	/**
	 * Get user model instance from database or any other users list
	 * resource by submitted and cleaned `$userName` field value.
	 * @param string $userName Submitted and cleaned username. Characters `' " ` < > \ = ^ | & ~` are automaticly encoded to html entities by default `\MvcCore\Ext\Auths\Basic` sign in form.
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\Interfaces\IUser
	 */
	public static function & GetByUserName ($userName) {
		$table = static::$usersTableStructure['table'];
		$columns = (object) static::$usersTableStructure['columns'];
		$sql = "
			SELECT
				u.$columns->id AS id,
				u.$columns->active AS active,
				u.$columns->userName AS userName,
				u.$columns->passwordHash AS passwordHash,
				u.$columns->fullName AS fullName
			FROM
				$table u
			WHERE
				u.$columns->userName = :user_name AND
				u.$columns->active = :active
		";
		$db = static::getDb();
		if (!$select = $db->prepare($sql))
			throw new \RuntimeException(
				implode(' ', $db->errorInfo()) . ': ' . $sql, intval($db->errorCode())
			);
		$select->execute(array(
			":user_name"	=> $userName,
			":active"		=> 1,
		));
		$user = NULL;
		$data = $select->fetch(\PDO::FETCH_ASSOC);
		if ($data) {
			$user = (new static())->SetUp($data, TRUE, TRUE, FALSE);
			return $user;
		}
		return $user;
	}
}
