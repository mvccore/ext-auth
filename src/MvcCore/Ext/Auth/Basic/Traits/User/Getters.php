<?php

namespace MvcCore\Ext\Auth\Basic\Traits\User;

trait Getters
{
	public function GetId () {
		return $this->id;
	}
	public function GetUserName () {
		return $this->userName;
	}
	public function GetFullName () {
		return $this->fullName;
	}
	public function GetPasswordHash () {
		return $this->passwordHash;
	}
}
