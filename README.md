## Laravel API Documentation generator

[![Latest Version](https://img.shields.io/github/release/mhamlet/laravel-apidocs.svg?style=flat-square)](https://github.com/mhamlet/laravel-apidocs/releases)
[![Build Status](https://img.shields.io/travis/mhamlet/laravel-apidocs/master.svg?style=flat-square)](https://travis-ci.org/mhamlet/laravel-apidocs)
[![Quality Score](https://img.shields.io/scrutinizer/g/mhamlet/laravel-apidocs.svg?style=flat-square)](https://scrutinizer-ci.com/g/mhamlet/laravel-apidocs)
[![Total Downloads](https://img.shields.io/packagist/dt/mhamlet/laravel-apidocs.svg?style=flat-square)](https://packagist.org/packages/mhamlet/laravel-apidocs)

This Laravel package provides an API Documentation generator. It's based on your Routes and Controller Method DocBlock comments.

The package requires PHP >= 7.0 and Laravel 5.4.

## Installation

Add the package in your composer.json by executing the command.

```bash
composer require mhamlet/laravel-apidocs
```

Next, add the service provider to `config/app.php`

```php
MHamlet\Apidocs\ApidocsServiceProvider::class,
```

## Documentation

```php
// The main class of package is MHamlet\Apidocs\Generator
// Let's write a simple "use"
use MHamlet\Apidocs\Generator;

// Generator has two statically declared methods - "forAllRoutes" and "forRoutesWithPrefix".

// The first one will provide you to generate documentation for all defined
// routes in your application.
$routeGenerator = Generator::forAllRoutes();

// The second one will provide you to generate documentation for routes,
// which URL's starts with given prefix.
$routeGenerator = Generator::forRoutesWithPrefix('api');

// The route generator also has two methods - "describeRoutes" and "generate"

// The first one will parse and describe the routes for you.
$routeGenerator->describeRoutes()

// It will return the following (the result is serialized in json to make it readable in this example)
/*
[
    {
        "path":"api/users",
        "controller":"App\\Http\\Controllers\\UserController",
        "method":"index",
        "verbs":[
            "GET",
            "HEAD"
        ]
    }
]
*/

// The second one will parse routes, controller comments and return the
// API documentation itself.
$routeGenerator->generate()

// It will return the following result
/*
[
    {
        "path":"api\/users",
        "verbs":[
            "GET",
            "HEAD"
        ],
        "description":"Returns list of all users",
        "params":[
            {
                "type":"int",
                "name":"from",
                "description":"test from"
            },
            {
                "type":"int",
                "name":"offset",
                "description":"test offset"
            }
        ],
    }
]
*/

```

### License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
