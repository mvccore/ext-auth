<?php

namespace MvcCore\Ext\Auth\Basic\Interfaces;

interface IDatabaseUser
{
	public static function SetUsersTableStructure ($tableName = NULL, $columnNames = NULL);
}
