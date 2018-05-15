<?php


namespace MvcCore\Ext\Auth\Interfaces;

interface ISignForm
{
	public function & SetAction ($url = '');
	public function & SetSuccessUrl ($url = '');
	public function & SetErrorUrl ($url = '');
	public function & SetTranslator (callable $translator = null);
	public function Init ();
	public function Submit ($rawParams = array());
}
