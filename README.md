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

## Usage

### Create a Component

Using artisan CLI to create a component, then you can found your component into `app/Components`.

~~~shell
php artisan component:make LiveChatBox
~~~

### Render a component.

~~~php
Component::uses(new App\Components\LiveChatBox\LiveChatBox(['args' => '1']))->render();

// or using helper.

component('LiveChatBox', ['args' => '1'])->render();
~~~

### TODO: Working with assets.

