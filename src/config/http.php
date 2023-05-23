<?php

/*
|--------------------------------------------------------------------------
| Configuration definitions for binding to application controllers
|--------------------------------------------------------------------------
|
| This file contains definition for request actions bindings for ressources
| used in  the application
|
*/
return [
    'cors' => [
        'allowed_hosts' => ['*'],
        'allowed_headers' => ['*'],
        'exposed_headers' => ['*'],
        'allowed_methods' => ['*'],
        'allowed_credentials' => true,
        'max_age' => 0
    ]
];
