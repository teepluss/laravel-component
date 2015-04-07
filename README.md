# Laravel 5 Component

Component is used to separate small parts from the main view and still works perfectly.

## Installation

### Composer

- [Component on Packagist](https://packagist.org/packages/teepluss/component)
- [Component on GitHub](https://github.com/teepluss/laravel-component)

To get the latest version of Theme simply require it in your `composer.json` file.

~~~
"teepluss/component": "dev-master"
~~~

You'll then need to run `composer update` to download it and have the autoloader updated.

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

~~~html
{!! component()->uses('LiveChatBox', ['args' => '1'])->render() !!}
~~~

### Working with assets.

Install gulp

~~~shell
npm install gulp
~~~

Using gulp to publish component assets.

~~~shell
$ cd app/Components/
$ gulp
$ gulp watch
~~~

Render all component scripts and styles from the main layout

~~~html
// Display component scripts and styles.
{!! component()->scripts() !!}
{!! component()->styles() !!}

// Locate to asset path.
{!! component()->uses('LiveChatBox')->asset('img/someimage.png') !!}
~~~

Localization

~~~php
{!! component()->trans('message') !!}
~~~

## Support or Contact

If you have some problem, Contact teepluss@gmail.com

[![Support via PayPal](https://rawgithub.com/chris---/Donation-Badges/master/paypal.jpeg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9GEC8J7FAG6JA)
