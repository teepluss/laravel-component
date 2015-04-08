<?php namespace Teepluss\Component;

use Teepluss\Component\BaseComponentInterface;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Foundation\Application as Application;
use Teepluss\Component\Contracts\Component as ComponentContract;

class Component implements ComponentContract {

    use AppNamespaceDetectorTrait;

    /**
     * Application.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Component.
     *
     * @var \Teepluss\Component\BaseComponentInterface
     */
    protected $component;

    /**
     * Core loaded.
     *
     * @var boolean
     */
    protected $coreLoaded = array();

    /**
     * Core component insrance.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Use component.
     *
     * @param  string $component
     * @param  mixed  $arguments
     * @return object
     */
    public function uses($component, $arguments = [])
    {
        $this->component = $this->getComponent($component, $arguments);

        if ( ! array_key_exists($component, $this->coreLoaded))
        {
            // Add translation hint.
            $this->app['translator']->addNamespace(
                $this->component->getComponentNamespace(),
                $this->component->getComponentPath().'/lang'
            );

            // Ad view hint.
            $this->app['view']->addNamespace(
                $this->component->getComponentNamespace(),
                $this->component->getComponentPath().'/views'
            );

            $this->coreLoaded[$component] = true;
        }

        return $this;
    }

    /**
     * Get component.
     *
     * Change string to component object.
     *
     * @param  string $name
     * @param  mixed  $arguments
     * @return object
     */
    public function getComponent($name, $arguments)
    {
        $component = "\\{$this->getAppNamespace()}Components\\$name\\$name";

        return new $component($this->app, $arguments);
    }

    public function scripts()
    {
        return $this->app['component.asset']->scripts();
    }

    public function styles()
    {
        return $this->app['component.asset']->styles();
    }

    /**
     * Display component public asset path.
     *
     * @param  string $path
     * @return string
     */
    public function asset($path)
    {
        return $this->component->getComponentPublicPath('/assets/'.ltrim($path, '/'));
    }

    /**
     * Display component server path.
     *
     * @param  string $path
     * @return string
     */
    public function src($path)
    {
        return $this->component->getComponentPath('/'.ltrim($path, '/'));
    }

    /**
     * Translate component language.
     *
     * @param  string $key
     * @param  string $file
     * @return string
     */
    public function trans($key, $file = 'messages')
    {
        $line = $this->component->getComponentNamespace().'::'.$file.'.'.$key;

        return $this->app['translator']->get($line);
    }

    /**
     * Render component to HTML.
     *
     * @return string
     */
    public function render()
    {
        $cacheKey = $this->component->getCacheKey();

        $view = $this->component->prepare()->execute();

        if ( ! isset($view['path'])) return '';

        // Using object cache to save performance.
        return $this->app['cache']->driver('array')->remember($cacheKey, 9999, function() use ($view)
        {
            $data = array_get($view, 'data');

            return view($this->component->getComponentNamespace().'::'.$view['path'], $data)->render();
        });
    }

}