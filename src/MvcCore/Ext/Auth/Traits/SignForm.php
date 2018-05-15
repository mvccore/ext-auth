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

namespace MvcCore\Ext\Auth\Traits;

trait SignForm {

	/**
	 * Initialize all form fields, initialize hidden field with
	 * sourceUrl for cases when in request params is any source url param.
	 * To return there after form is submitted.
	 * @return \MvcCore\Ext\Auth\SignInForm
	 */
	public function & Init () {
		parent::Init();
		$cfg = \MvcCore\Ext\Auth::GetInstance()->GetConfig();
		$this->addSuccessAndErrorUrlHiddenControls($cfg->signedInUrl, $cfg->errorUrl);
		return $this;
	}

	/**
	 * Add success and error url which are used
	 * to redirect user to success url or error url
	 * after form is submitted.
	 * @param string $successUrl
	 * @param string $errorUrl
	 */
	protected function addSuccessAndErrorUrlHiddenControls ($successUrl = '', $errorUrl = '') {
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
