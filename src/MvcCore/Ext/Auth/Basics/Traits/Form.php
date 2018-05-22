<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Auth\Basics\Traits;

trait Form
{
	/**
	 * @var \MvcCore\Ext\Auth\Basic|\MvcCore\Ext\Auth\Basics\Interfaces\IAuth
	 */
	protected $auth = NULL;

	/**
	 * Add success and error url which are used
	 * to redirect user to success url or error url
	 * after form is submitted.
	 * @return void
	 */
	protected function initAuthFormPropsAndHiddenControls () {
		$this->auth = \MvcCore\Ext\Auth\Basic::GetInstance();
		$this->AddField(new \MvcCore\Ext\Form\Hidden(array(
			'name'			=> 'successUrl',
			'value'			=> $this->auth->GetSignedInUrl(),
			'validators'	=> array('Url'),
		)));
		$this->AddField(new \MvcCore\Ext\Form\Hidden(array(
			'name'			=> 'errorUrl',
			'value'			=> $this->auth->GetSignErrorUrl(),
			'validators'	=> array('Url'),
		)));
	}
}
