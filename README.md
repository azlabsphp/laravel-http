# Package documentation

The `drewlabs/http` package provides utility classes and functions for unified HTTP response API and middleware classes for handling CORS.

## Providers

By default providers are automatically registered when running Laravel application after composer finishes installing the package.

* For Lumen appliation we must manually register the providers in the bootstrap/app.php:

```php
// bootstrap/app.php
// ...
// Register the HttpService provider
$app->register(Drewlabs\Packages\Http\HttpServiceProvider::class);
// ...
```

## Cors Middleware

To use the cors middleware in your application add the following code to your kernel based on the framework being used:

* Laravel

```php
    // app/Http/Kernel.php

    // ...
    protected $middleware = [
        // ...
        \Drewlabs\Packages\Http\Middleware\Cors::class,
    ];
```

* Lumen

```php
    // bootstrap/app.php

    $app->middleware([
        // Other globally registered middlewares...
        \Drewlabs\Packages\Http\Middleware\Cors::class,
    ]);
    // ...
```

**Note** In order to allow any ` host` or  `method`, or  `headers` use the  `*` in the matching key of the config array.

## EmptyStringToNull Middleware

It's a midleware that convert all empty string query parameteres and empty request body entry to null.

* Laravel

Note: Laravel already provide implementation for such case. But if you still want to use the current package middleware do it as follow.

```php
    // app/Http/Kernel.php
    // ...
    protected $middleware = [
        // ...
        \Drewlabs\Packages\Http\Middleware\EmptyStringToNull::class,
    ];
```

* Lumen

```php
    // bootstrap/app.php

    $app->middleware([
        // Other globally registered middlewares...
        \Drewlabs\Packages\Http\Middleware\EmptyStringToNull::class,
    ]);
    // ...
```

## Http package configuration

This configuration file contains middleware aliases keys definition for the application Http request handlers, like auth, policy middlewares.

* Publishing the configuration files

> php artisan vendor:publish --tag="drewlabs-http"
