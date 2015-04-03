<?php namespace Teepluss\Component;

class Component {

    protected $component;

    public function __construct()
    {

    }

    public function uses($component)
    {
        $this->component = $component;

        return $this;
    }

    public function render()
    {
        return 'Something I am.';
    }

}