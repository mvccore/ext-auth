<?php


namespace MvcCore\Ext\Auth\Basics\Interfaces;

interface IForm
{
	const ID = 'authentication';

	public function & SetId ($id = '');
	public function & SetCssClass ($cssClass = '');
	public function & SetMethod ($method = '');
	public function & SetAction ($action = '');
	public function & SetSuccessUrl ($url = '');
	public function & SetErrorUrl ($url = '');
	public function & SetTranslator (callable $translator = null);
	public function Init ();
	public function AddField (\MvcCore\Ext\Form\IField $field);
	public function Submit ($rawParams = array());
	public function ClearSession ();
	public function RedirectAfterSubmit ();
}
