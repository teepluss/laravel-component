<?php namespace Teepluss\Component;

use Teepluss\Component\BaseComponentInterface;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Foundation\Application as Application;

class Component {

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
    protected $coreLoaded = false;

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

        if ($this->coreLoaded == false)
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

            $this->coreLoaded = true;
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

    /**
     * Render scripts.
     *
     * Render all component's scripts.
     *
     * @return string
     */
    public function scripts()
    {
        return $this->app['view']->yieldContent('component-scripts');
    }

    /**
     * Render styles.
     *
     * Render all component's styles.
     *
     * @return string
     */
    public function styles()
    {
        return $this->app['view']->yieldContent('component-styles');
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

        // Using object cache to save performance.
        return $this->app['cache']->driver('array')->remember($cacheKey, 9999, function()
        {
            $view = $this->component->prepare()->execute();

            return view($this->component->getComponentNamespace().'::'.$view['path'], $view['data'])->render();
        });
    }

}