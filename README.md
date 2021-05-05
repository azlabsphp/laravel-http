# drewlabs-laravel-http

The `drewlabs/http` package provides utility classes and functions for unified HTTP response API and middleware classes for handling CORS, and an alternative to Laravel EmptyStringToNull middleware.


## Providers

By default providers are automatically registered when running Laravel application after composer finishes installing the package.

- For Lumen appliation we must manually register the providers in the bootstrap/app.php:

```php
// bootstrap/app.php
// ...
// Register the HttpService provider
$app->register(Drewlabs\Packages\Http\HttpServiceProvider::class);
// Rebgister cors handler service provider
$app->register(Drewlabs\Packages\Http\Middleware\Cors\ServiceProvider::class);
// ...
```
## Cors handler

After registring the providers, publish the cors.php configuration file to configure authorized method, origin and headers.

> php artisan vendor:publish --tag="drewlabs-cors"

To use the cors middleware in your application add the following code to your kernel based on the framework being used:

- Laravel

```php
    // app/Http/Kernel.php

    // ...
    protected $middleware = [
        // ...
        \Drewlabs\Packages\Http\Middleware\Cors\Middleware::class,
    ];
```

- Lumen

```php
    // bootstrap/app.php

    $app->middleware([
        // Other globally registered middlewares...
        \Drewlabs\Packages\Http\Middleware\Cors\Middleware::class,
    ]);
    // ...
```

### Cors config

In order to allow any host or method, or headers use the `*` in the matching key of the config array.

## EmptyStringToNull

It's a midleware that convert all empty string query parameteres and empty request body entry to null.

- Laravel 

Note: Laravel already provide implementation for such case. But if you still want to use the current package middleware do it as follow.

```php
    // app/Http/Kernel.php

    // ...
    protected $middleware = [
        // ...
        \Drewlabs\Packages\Http\Middleware\EmptyStringToNull::class,
    ];
```

- Lumen

```php
    // bootstrap/app.php

    $app->middleware([
        // Other globally registered middlewares...
        \Drewlabs\Packages\Http\Middleware\EmptyStringToNull::class,
    ]);
    // ...
```

## Response Handler provider & Example Controller

Response handler provider provide and unified way to return JSON response to the request client. It will always return a value in the format:

```json
{
    "success": <Boolean>,
    "body": {
        "error_message": <String>, // "<Error Message in case request Error Occurs>"
        "response_data": <Any>, // "<Actual response data format send from the controller action>",
        "errors": <Array>, // "<Laravel validation error array>",
    },
    "code": <HTTP_STATUS_CODE>, // 200|201 Success response, 404 - Not Found, 422 - Validation Error
}
```

```php

// ExampleController.php

namespace Drewlabs\Packages\Http\Controllers;

use Drewlabs\Core\Validator\Contracts\IValidator;
use Illuminate\Http\JsonResponse as Response;
use Illuminate\Http\Request;
use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;
use Drewlabs\Packages\Http\Traits\LaravelOrLumenFrameworksApiController;
use App\Models\Example;


class ApiDataProviderController
{
    use LaravelOrLumenFrameworksApiController;
    /**
     *
     * @var IValidator
     */
    private $validator;

    /**
     *
     * @var IActionResponseHandler
     */
    private $responseHandler;


    public function __construct(
        IValidator $validator,
        IActionResponseHandler $responseHandler
    ) {
        $this->validator = $validator;
        $this->responseHandler = $responseHandler;
    }

    /**
     * Display a listing of the resource.
     *
     * @route GET /examples[/{$id}]
     *
     * @param Request $request
     * @param string|int|null $id
     *
     * @return Response
     */
    public function index(Request $request, int $id = null)
    {
        //...
        // Provide implementation for the index route

        // Example of returning empty array to the request client
        return $this->responseHandler->respondOk([])
    }

    /**
     * Store a newly created resource in storage.
     *
     * @route POST /examples
     *
     * @param Request $request
     * @param string $collection
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate request
        // Note: Example class must implements {Drewlabs\Core\Validator\Contracts\Validatable} in order to be eligible for the validator as model
        // Else the validation throws an {Exception}
        $validator = $this->validator->validate(Example::class, $request->all(), $messages = []);

        // Checks if validator fails
        if ($validator->fails()) {
            return $this->responseHandler->respondBadRequest($validator->errors());
        }

        // Validation successful
        // ...
        // Respond to client with response data
        return $this->responseHandler->respondOk(/* Action result to send back to user */);
    }

    /**
     * Display the specified resource.
     *
     * @route GET /examples/{$id}
     *
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function show(Request $request, int id)
    {
        //...
        // Provide implementation for the index route

        // Example of returning empty array to the request client
        return $this->responseHandler->respondOk()
    }


    /**
     * Update the specified resource in storage.
     *
     * @route UPDATE /examples/{$id}
     *
     * @param Request $request
     * @param int|mixed $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Validate request
        // Note : setUpdate() let the validator knows which validation rule to load on the model class
        // Note: Example class must implements {Drewlabs\Core\Validator\Contracts\Validatable} in order to be eligible for the validator as model
        // Else the validation throws an {Exception}
        $validator = $this->validator->setUpdate(true)->validate(Example::class, $request->all(), $messages = []);

        // Checks if validator fails
        if ($validator->fails()) {
            return $this->responseHandler->respondBadRequest($validator->errors());
        }

        // Validation successful
        // ...
        // Respond to client with response data
        return $this->responseHandler->respondOk(/* Action result to send back to user */);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @route DELETE /examples/{$id}
     *
     * @param Request $request
     * @param string $collection
     * @param array $param
     *
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        // ...
        // Provide code for deleting the model using the provided parameters
        // Respond to client with response data
        return $this->responseHandler->respondOk(/* Action result to send back to user */);
    }
}

```

## Http package configuration

This configuration file contains middleware aliases keys definition for the application Http request handlers, like auth, policy middlewares.

- Publishing the configuration files

> php artisan vendor:publish --tag="drewlabs-http-configs"