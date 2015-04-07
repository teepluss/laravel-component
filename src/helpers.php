<?php

if ( ! function_exists('component'))
{
    /**
     * Helper to call component.
     *
     * @return object
     */
    function component()
    {
        return app('component');
    }
}
