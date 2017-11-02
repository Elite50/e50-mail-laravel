# Elite50 E50Mail Laravel Facade

An extension of [Laravel's](http://laravel.com/) Mail facade allowing for dynamic configuration.

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
    // Sender domain (required for Mailgun only)
    'example.com',

    // Views
    ['html' => 'views.html-email'],

    // View data
    ['name' => 'John Doe'],

    // Message data
    [
        'toEmail' => 'john@example.com',
        'toName' => 'John Doe',
        'fromEmail' => 'robot@example.com',
        'fromName' => 'Mail Robot',
        'subject' => 'Action Required!',
        'headers' => [
            'X-Mail-Header' => 'abcd1234',
        ],
    ],

    // Custom driver (DEPRECATED - use custom mail config)
    'mailgun',

    // Custom queue
    'QueueName',

    // Custom mail config
    [
        'driver' => 'smtp',
        'host' => 'smtp.myhost.com',
    ]
);
```
