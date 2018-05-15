<?php

namespace MvcCore\Ext\Auth\Interfaces;

interface IDatabaseUser
{
	public static function SetUsersTableStructure ($tableName = NULL, $columnNames = NULL);
}
