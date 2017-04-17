# Ghostscript PHP driver

[![Build Status](https://secure.travis-ci.org/alchemy-fr/Ghostscript-PHP.png)](http://travis-ci.org/alchemy-fr/Ghostscript-PHP)

# API usage

To instantiate Ghostscript driver, the easiest way is :

```php
$transcoder = Ghostscript\Transcoder::create();
```

You can customize your driver by passing a `Psr\Log\LoggerInterface` or
configuration options.

Available options are :

 - `gs.binaries` : the path (or an array of potential paths) to the ghostscript binary.
 - `timeout` : the timeout for the underlying process.

```php
$transcoder = Ghostscript\Transcoder::create(array(
    'timeout' => 42,
    'gs.binaries' => '/opt/local/gs/bin/gs',
), $logger);
```

To process a file to PDF format, use the `toPDF` method :

Third and fourth arguments are respectively the first page and the number of
page to transcode.

```php
$transcoder->toPDF('document.pdf', 'first-page.pdf', 1, 1);
```

To render a file to Image, use the `toImage` method :

```php
$transcoder->toImage('document.pdf', 'output.jpg');
```

## Silex service provider :

A [Silex](silex.sensiolabs.org) Service Provider is available, all parameters
are optional :

```php
$app = new Silex\Application();
$app->register(new Ghostscript\GhostscriptServiceProvider(), array(
    'ghostscript.configuration' => array(
        'gs.binaries' => '/usr/bin/gs',
        'timeout'     => 42,
    ),
    'ghostscript.logger' => $app->share(function () use ($app) {
        return $app['monolog']; // use Monolog service provider
    }),
));

$app['ghostscript.transcoder']->toImage('document.pdf', 'image.jpg');
```

## Laravel 5 service provider

A [Laravel 5](http://laravel.com/) service provider is available

Publish the package config files:

    php artisan vendor:publish

You will also need to add the service provider and optionally the facade alias to your `app/config/app.php`:

```php
'providers' => array(
  'Ghostscript\LaravelGhostscriptServiceProvider'
)

'aliases' => array(
  'Ghostscript' => 'Ghostscript\Facades\Ghostscript'
),
```

# License

Released under the MIT License
