# MvcCore - Extension - Authentication

[![Latest Stable Version](https://img.shields.io/badge/Stable-v5.3.0-brightgreen.svg?style=plastic)](https://github.com/mvccore/ext-auth/releases)
[![License](https://img.shields.io/badge/License-BSD%203-brightgreen.svg?style=plastic)](https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md)
![PHP Version](https://img.shields.io/badge/PHP->=5.4-brightgreen.svg?style=plastic)

Authentication module with automatic authentication module type detection by loaded classes.

## Installation
```shell
composer require mvccore/ext-auth
```

## Usage
Add this to `Bootstrap.php` or to very application beginning, before application routing.
```php
\MvcCore\Ext\Auth::GetInstance()
	->SetPasswordHashSalt('s9E56/QH6!a69sJML9aS$6s+')
	->SetUserClass('\\MvcCore\\Ext\\Auths\\Users\\SystemConfig');
```
For system config users, you need to specify users in `system.ini` like this:
```ini
[users]
0.userName		= admin
0.fullName		= Administrator
0.passwordHash	= $2y$10$czlFNTYvUUg2IWE2OXNKTO8PB5xPGXz9i8IH7Fa7M0YsPlSLriJZu
; admin password is `demo`
```
To get sign in form into view in your application controller:
```php
...
	public function IndexAction () {
		if ($this->user !== NULL)
			self::Redirect($this->Url('administration_index_page'));
		$this->view->SignInForm = \MvcCore\Ext\Auth::GetInstance()
			->GetSignInForm()
			->SetValues(array(// set signed in url to administration index page by default:
				'successUrl' => $this->Url('administration_index_page'),
			));
	}
...
```
To get sign out form into view in your application controller:
```php
...
	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->viewEnabled && $this->user) {
			$this->view->SignOutForm =\MvcCore\Ext\Auth::GetInstance()
				->GetSignOutForm()
				->SetValues(array(
					'successUrl' => $this->Url('login_page')
				));
		}
	}
...
```
For any forms CSRF errors - you can call in base controller `Init()` action:
```php
...
	public function Init() {
		parent::Init();
		// when any CSRF token is outdated or not the same - sign out user by default
		\MvcCore\Ext\Form::AddCsrfErrorHandler(function (\MvcCore\Ext\Form & $form, $errorMsg) {
			\MvcCore\Ext\Auth\User::LogOut();
			self::Redirect($this->Url(
				'Index:Index',
				array('absolute' => TRUE, 'sourceUrl'	=> rawurlencode($form->ErrorUrl))
			));
		});
	}
...
```
To translate your signin and signout form visible elements, use:
```php
\MvcCore\Ext\Auth::GetInstance()->SetTranslator(function ($key, $lang = NULL) {
	// your custom translator model/service:
	return \App\Models\Translator::GetInstance()->Translate($key, $lang);
});
```
