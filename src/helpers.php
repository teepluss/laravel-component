<?php

if ( ! function_exists('component'))
{
    function component($name, $arguments = [])
    {
        return app('component');
    }
}


//component()->img();