<?php

namespace MvcCore\Ext\Auth\Basics\Interfaces;

interface IRole
{
	public function GetId ();
	public function & SetId ($id);
	public function GetName ();
	public function & SetName ($userName);
	public function GetByName ($roleName);
	public function GetPermission ($permitionName);
}
