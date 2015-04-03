<?php namespace Teepluss\Component;

use Illuminate\Container\Container as Application;

class Component {

    protected $app;

    protected $component;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function uses($component)
    {
        $this->component = $component;

        $this->app['view']->addNamespace($this->getComponentNamespace(), $this->getComponentPath().'/views');

        return $this;
    }

    protected function getComponentNamespace()
    {
        return $this->component->getNamespace();
    }

    protected function getComponentPath()
    {
        return app_path('Components/'.ucfirst($this->component->getNamespace()));
    }

    public function render()
    {
        $view = $this->component->prepare()->execute();

        return view($this->getComponentNamespace().'::'.$view['path'], $view['data']);
    }

}