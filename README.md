# MvcCore Extension - Basic Authentication
Simple authentication service, only to signin/signout an user. Service use  credentials defined in system config.ini by default. Service is very easy to extend and configure for new user model class to authenticate the user by any database credentials or anything else and the same is possible for sign in/out controller or form class to use any registration implementation you need.

## Installation
```shell
composer require mvccore/ext-auth
```

## Usage
Add this to Bootstrap.php or to very application beginning, before application routing.
```php
MvcCoreExt_Auth::GetInstance()->Init();
```

To translate your signin and signout form visible elements, use:
```php
MvcCoreExt_Auth::GetInstance()->Init()->SetTranslator(function ($key, $lang = NULL) {
	// your custom translator model/service:
	return App_Models_Translator::GetInstance()->Translate($key, $lang);
});
```