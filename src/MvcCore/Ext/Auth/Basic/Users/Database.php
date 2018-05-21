<?php

namespace MvcCore\Ext\Auth\Users;

class Database extends \MvcCore\Ext\Auth\Basic\User implements \MvcCore\Ext\Auth\Basic\Interfaces\IDatabaseUser
{
	protected static $usersTableStructure = array(
		'table'		=> 'users',
		'columns'	=> array(
			'id'			=> 'id',
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
	 * Get user model instance from database using submitted and cleaned `$userName` field value.
	 * @param string $userName
	 * @return \MvcCore\Ext\Auth\User
	 */
	public static function GetByUserName ($userName) {
		$table = static::$usersTableStructure['table'];
		$columns = (object) static::$usersTableStructure['columns'];
		$sql = "
			SELECT
				u.$columns->id AS id,
				u.$columns->userName AS userName,
				u.$columns->passwordHash AS passwordHash,
				u.$columns->fullName AS fullName
			FROM
				$table u
			WHERE
				u.$columns->userName = :user_name
		";
		$select = static::getDb()->prepare($sql);
		$select->execute(array(
			":user_name" => $userName,
		));
		if ($data = $select->fetch(\PDO::FETCH_ASSOC)) {
			return (new static())->SetUp($data, FALSE, TRUE, FALSE);
		}
		return NULL;
	}
}
