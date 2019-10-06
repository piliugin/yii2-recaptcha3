# yii2-recaptcha3

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require piliugin/yii2-recaptcha3 "*"
```

or add

```
"piliugin/yii2-recaptcha3": "*"
```

to the require section of your `composer.json` file and run `composer update`.

Usage
-----

Once the extension is installed, simply use it in your code by:

add this to your components main.php

```php
'components' => [
    ...
    'recaptcha' => [
        'class' => 'piliugin\recaptcha3\Recaptcha',
        'site_key' => '###',
        'secret_key' => '###',
    ],
    ...
```

and in your model:

`acceptanceScore` is the minimum score for this request (0.0 - 1.0). Default is 0.5.

`actionName` is the name of action that you send to google to get `captchaToken` on frontend
(see [Frontend integration](https://developers.google.com/recaptcha/docs/v3))

```php
public $captchaToken;
 
public function rules()
{
    return [
        ...
        [
            ['captchaToken'],
            RecaptchaValidator::class,
            'action' => 'yourActionName',
            'acceptanceScore' => 0.4,
        ],
    ];
}
```
