<?php namespace Teepluss\Component;

use Illuminate\Console\AppNamespaceDetectorTrait;

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

    // protected function getComponentNamespace()
    // {
    //     return $this->component->getNamespace();
    // }

    // protected function getComponentPath()
    // {
    //     return app_path('Components/'.ucfirst($this->component->getNamespace()));
    // }

    // protected function getComponentPublicPath()
    // {
    //     return $this->app['url']->asset('teepluss/components/'.ucfirst($this->component->getNamespace()));
    // }

    public function getComponent($name, $arguments)
    {
        $component = "\\{$this->getAppNamespace()}Components\\$name\\$name";

        return new $component($arguments);
    }

    public function scripts()
    {
        // return component scripts.
    }

    public function styles()
    {
        // return component styles.
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