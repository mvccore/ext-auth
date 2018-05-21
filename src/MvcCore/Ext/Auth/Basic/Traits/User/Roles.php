<?php

namespace MvcCore\Ext\Auth\Basic\Traits\User;

trait Roles
{
	public function & GetRoles () {
		return $this;
	}
	public function & SetRoles ($roles = array()){
		return $this;
	}
	public function & AddRole ($role){
		return $this;
	}
	public function & RemoveRole ($role){
		return $this;
	}
}
