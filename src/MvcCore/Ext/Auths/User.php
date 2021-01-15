<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Auths;

if (\MvcCore\Ext\Auth::GetAuthClass() == \MvcCore\Ext\Auth::AUTH_CLASS_BASIC) {
	class User extends \MvcCore\Ext\Auths\Basics\User{}
} else {
	class User extends \MvcCore\Ext\Auths\Fulls\User{}
}
