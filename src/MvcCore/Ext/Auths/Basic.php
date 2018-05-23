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

/**
 * Responsibility - managing login/logout forms, authentication requests and user instance.
 * - Basic extensible authentication module with sign in and sign out forms 
 *   and automaticly initialized user instance stored in custom session namespace.
 * - Possiblity to configure:
 *   - submit routes to sign in and sign out
 *   - submit success and submit error url addresses
 *   - form classes
 *   - forms submit's controller class
 *   - user instance class
 *   - wrong credentials timeout
 *   - custom password hash salt
 *   - translator and more...
 */
class Basic implements \MvcCore\Ext\Auths\Basics\Interfaces\IAuth
{
	use \MvcCore\Ext\Auths\Basics\Traits\Auth\PropsGettersSetters,
		\MvcCore\Ext\Auths\Basics\Traits\Auth\Handling;
}
