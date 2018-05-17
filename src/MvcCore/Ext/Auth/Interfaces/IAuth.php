<?php

namespace MvcCore\Ext\Auth\Interfaces;

interface IAuth
{
	/**
	 * MvcCore Extension - Auth - version:
	 * Comparation by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.0.0-alpha';

	/**
	 * Return singleton instance. If instance exists, return existing instance,
	 * if not, create new Auth module instance, store it and return it.
	 * @param array $configuration Optional configuration passed into `__construct($configuration)` method.
	 * @return \MvcCore\Ext\Auth\Interfaces\IAuth
	 */
	public static function GetInstance ($configuration = array());
}
