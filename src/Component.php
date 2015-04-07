<?php namespace Teepluss\Component;

use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Foundation\Application as Application;

class Component {

    use AppNamespaceDetectorTrait;

    protected $app;

    protected $component;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function uses($component, $arguments = [])
    {
        $this->component = $this->getComponent($component, $arguments);

        $this->app['view']->addNamespace(
            $this->component->getComponentNamespace(),
            $this->component->getComponentPath().'/views'
        );

        return $this;
    }

    public function getComponent($name, $arguments)
    {
        $component = "\\{$this->getAppNamespace()}Components\\$name\\$name";

        return new $component($this->app, $arguments);
    }

    public function scripts()
    {
        return $this->app['view']->yieldContent('component-scripts');
    }

    public function styles()
    {
        return $this->app['view']->yieldContent('component-styles');
    }

    public function asset($path)
    {
        return $this->component->getComponentPublicPath().'/assets/'.ltrim($path, '/');
    }

    public function src($path)
    {
        return $this->component->getComponentPath().'/'.ltrim($path, '/');
    }

    public function render()
    {
        $cacheKey = $this->component->getCacheKey();

        // Using object cache to save performance.
        return $this->app['cache']->driver('array')->remember($cacheKey, 9999, function()
        {
            $view = $this->component->prepare()->execute();

            return view($this->component->getComponentNamespace().'::'.$view['path'], $view['data'])->render();
        });
    }

}