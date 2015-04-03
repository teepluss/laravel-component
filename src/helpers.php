<?php

if ( ! function_exists('component'))
{
    function component($name, $arguments = [])
    {
        $component = app('component');

        $core = "\App\Component\{$name}($arguments)";

        return $component->uses(new $core);
    }
}