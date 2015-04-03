<?php namespace Teepluss\Component;

class BaseComponent {

    protected $namespace;

    protected $componentName;

    protected $arguments;

    protected $view;

    public function __construct($arguments = null)
    {
        $this->arguments = $arguments;
    }

    public function getCacheKey()
    {
        return md5($this->namespace.json_encode($this->arguments));
    }

    public function getComponentNamespace()
    {
        return $this->namespace;
    }

    public function getComponentName()
    {
        return ucfirst($this->namespace);
    }

    public function getComponentPath()
    {
        return app_path('Components/'.$this->getComponentName());
    }

    public function getComponentPublicPath()
    {
        return asset('teepluss/components/'.$this->getComponentName());
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