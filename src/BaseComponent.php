<?php namespace Teepluss\Component;

class BaseComponent {

    protected $arguments;

    protected $view;

    public function __construct($arguments = null)
    {
        $this->arguments = $arguments;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    protected function argument($name)
    {
        return array_get($this->arguments, $name);
    }

    public function view($path, $data)
    {
        $this->view = [
            'path' => $path,
            'data' => $data
        ];
    }

    public function execute()
    {
        return $this->view;
    }

}