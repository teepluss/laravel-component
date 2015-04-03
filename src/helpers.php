<?php

if ( ! function_exists('component'))
{
    function component($name, $arguments = [])
    {
        $component = app('component');

        $component->getComponent($name);

        $instance = $component->getComponent($name, $arguments);

        return $component->uses($instance);
    }
}