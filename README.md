## Add custom repository on composer.json
~~~bin
...
"repositories": [
{
    "type": "vcs",
    "url": "https://github.com/teepluss/laravel-component"
},
...
~~~

~~~bin
$ composer require "teepluss/component:dev-master"
~~~

~~~php
'providers' => [
    ...
    'Teepluss\Component\ComponentServiceProvider',
~~~
