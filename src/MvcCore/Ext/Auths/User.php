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

namespace MvcCore\Ext\Auths;

if (\MvcCore\Ext\Auth::GetAuthType() == \MvcCore\Ext\Auth::TYPE_BASIC) {
	class User extends \MvcCore\Ext\Auths\Basics\User{}
} else {
	class User extends \MvcCore\Ext\Auths\Fulls\User{}
}
