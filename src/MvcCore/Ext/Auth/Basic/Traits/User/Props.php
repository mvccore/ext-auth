<?php

namespace MvcCore\Ext\Auth\Basic\Traits\User;

trait Props
{
	/** @var int */
	protected $id = NULL;

	/** @var string */
	protected $userName = NULL;

	/** @var string */
	protected $fullName = NULL;

	/** @var string */
	protected $passwordHash = NULL;

	/** @var \MvcCore\Session */
	protected static $userSessionNamespace = NULL;
}
