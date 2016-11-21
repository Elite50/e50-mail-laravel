# Elite50 E50Mail Laravel Facade

An extension of [Laravel's](http://laravel.com/) Mail facade with support for multiple Mailgun domains and dynamic drivers.

## Using the facade

### Install via composer

```shell
composer require elite50/e50-mail-laravel
```

### Include in `app/config/app.php`

```php
'providers' => array (
    ...
		'Elite50\E50MailLaravel\E50MailServiceProvider',
),

'aliases' => array (
    ...
    'E50Mail' => 'Elite50\E50MailLaravel\Facades\E50Mail',
    'E50MailWorker' => 'Elite50\E50MailLaravel\E50MailWorker',
)
```

### Use the facade in your application

##### Example:
```php
E50Mail::queue(
    'example.com',
    ['html' => 'views.html-email'],
    ['name' => 'John Doe'],
    [
        'toEmail' => 'john@example.com',
        'toName' => 'John Doe',
        'fromEmail' => 'robot@example.com',
        'fromName' => 'Mail Robot',
        'subject' => 'Action Required!',
        'headers' => [
            'X-Mail-Header' => 'abcd1234'
        ]
    ],
    'mailgun'
);
```
