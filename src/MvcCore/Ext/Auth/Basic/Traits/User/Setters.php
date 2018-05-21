<?php

namespace MvcCore\Ext\Auth\Basic\Traits\User;

trait Setters
{
	public function & SetId ($id) {
		$this->id = $id;
		return $this;
	}
	public function & SetUserName ($userName) {
		$this->userName = $userName;
	}
	public function & SetFullName ($fullName) {
		$this->fullName = $fullName;
	}
	public function & SetPasswordHash ($passwordHash) {
		$this->passwordHash = $passwordHash;
		return $this;
	}
}
