# MvcCore Extension - Basic Authentication

[![Latest Stable Version](https://img.shields.io/badge/Stable-v4.3.1-brightgreen.svg?style=plastic)](https://github.com/mvccore/ext-auth/releases)
[![License](https://img.shields.io/badge/Licence-BSD-brightgreen.svg?style=plastic)](https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md)
![PHP Version](https://img.shields.io/badge/PHP->=5.3-brightgreen.svg?style=plastic)

Simple authentication service, only to signin/signout an user. Service use  credentials defined in system config.ini by default. Service is very easy to extend and configure for new user model class to authenticate the user by any database credentials or anything else and the same is possible for sign in/out controller or form class to use any registration implementation you need.

## Installation
```shell
composer require mvccore/ext-auth
```

## Usage
Add this to Bootstrap.php or to very application beginning, before application routing.
```php
\MvcCore\Ext\Auth::GetInstance()->Init();
```

To translate your signin and signout form visible elements, use:
```php
\MvcCore\\Ext\Auth::GetInstance()->Init()->SetTranslator(function ($key, $lang = NULL) {
	// your custom translator model/service:
	return \App\Models\Translator::GetInstance()->Translate($key, $lang);
});
```
