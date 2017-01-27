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

namespace MvcCore\Ext\Auth\Virtual;

class Form extends \MvcCore\Ext\Form {

	/**
	 * Unique form id.
	 * @var string
	 */
	public $Id = 'authentication';

	/**
	 * Form http method.
	 * @var string
	 */
	public $Method = \MvcCore\Ext\Form::METHOD_POST;

	/**
	 * For sign in form:
	 *   initialize all form fields, initialize hidden field with
	 *   sourceUrl for cases when in request params is any source url param.
	 *   To return there after form is submitted.
	 * For sign out form:
	 * - initialize sign out button and user into
	 *   template for any custom template rendering.
	 * @throws \MvcCore\Ext\Form\Core\Exception
	 * @return \MvcCore\Ext\Auth\Virtual\Form|\MvcCore\Ext\Form
	 */
	public function Init () {
		return parent::Init();
	}

	/**
	 * For sign in form:
	 * - if there is any user with the same password imprint
	 *   store user in session for next requests, if there is not - wait for
	 *   three seconds and then go to error page.
	 * For sign out form:
	 * - sign out submit - if everything is ok, unser user unique name from session
	 *   for next requests to hanve not authenticated user anymore.
	 * @param array $rawParams
	 * @return array
	 */
	public function Submit ($rawParams = array()) {
		return parent::Submit($rawParams);
	}

	/**
	 * Add success and error url which are used
	 * to redirect user to success url or error url
	 * after form is submitted.
	 * @param string $successUrl
	 * @param string $errorUrl
	 */
	protected function addSuccessAndErrorUrlHiddens ($successUrl = '', $errorUrl = '') {
		$this->AddField(new \MvcCore\Ext\Form\Hidden(array(
			'name'			=> 'successUrl',
			'value'			=> $successUrl,
			'validators'	=> array('Url'),
		)));
		$this->AddField(new \MvcCore\Ext\Form\Hidden(array(
			'name'			=> 'errorUrl',
			'value'			=> $errorUrl,
			'validators'	=> array('Url'),
		)));
	}
}