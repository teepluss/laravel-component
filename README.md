# Laravel 5 Component

Component is used to separate small parts from the main view and still works perfectly.

## Installation

### Composer

Add custom repository on composer.json

~~~json
...
"repositories": [
{
    "type": "vcs",
    "url": "https://github.com/teepluss/laravel-component"
},
~~~

Run `composer require` to install the package.

~~~shell
composer require "teepluss/component:dev-master"
~~~

### Laravel

In your `config/app.php` add `'Teepluss\Component\ComponentServiceProvider'` to the end of the `providers` array:

~~~php
'providers' => [
    ...
    'Teepluss\Component\ComponentServiceProvider',
]
~~~

Publish Configuration

~~~shell
php artisan vendor:publish --provider="Teepluss\Component\ComponentServiceProvider"
~~~
