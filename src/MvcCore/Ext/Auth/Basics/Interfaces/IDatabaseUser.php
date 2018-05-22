<?php

namespace MvcCore\Ext\Auth\Basics\Interfaces;

interface IDatabaseUser
{
	public static function SetUsersTableStructure ($tableName = NULL, $columnNames = NULL);
}
