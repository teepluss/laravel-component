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

    /**
     * Render scripts.
     *
     * Render all component's scripts.
     *
     * @return string
     */
    public function scripts()
    {
        return $this->assetGroup('script');
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
        return $this->assetGroup('style');
    }

    /**
     * Arrange assets by group.
     *
     * @param  string $group
     * @return string
     */
    protected function assetGroup($group)
    {
        $assets = $this->component->getComponentAssets();

        if ( ! isset($assets[$group]) or count($assets[$group]) == 0) return '';

        $buffer = '';

        foreach ($this->arrange($assets[$group]) as $name => $data)
        {
            $buffer .= $data['source'];
        }

        return $buffer;
    }

    /**
     * Sort and retrieve assets based on their dependencies
     *
     * @param   array  $assets
     * @return  array
     */
    protected function arrange($assets)
    {
        list($original, $sorted) = array($assets, array());

        while (count($assets) > 0)
        {
            foreach ($assets as $asset => $value)
            {
                $this->evaluateAsset($asset, $value, $original, $sorted, $assets);
            }
        }

        return $sorted;
    }

    /**
     * Evaluate an asset and its dependencies.
     *
     * @param  string  $asset
     * @param  string  $value
     * @param  array   $original
     * @param  array   $sorted
     * @param  array   $assets
     * @return void
     */
    protected function evaluateAsset($asset, $value, $original, &$sorted, &$assets)
    {
        // If the asset has no more dependencies, we can add it to the sorted list
        // and remove it from the array of assets. Otherwise, we will not verify
        // the asset's dependencies and determine if they've been sorted.
        if (count($assets[$asset]['dependencies']) == 0)
        {
            $sorted[$asset] = $value;

            unset($assets[$asset]);
        }
        else
        {
            foreach ($assets[$asset]['dependencies'] as $key => $dependency)
            {
                if ( ! $this->dependecyIsValid($asset, $dependency, $original, $assets))
                {
                    unset($assets[$asset]['dependencies'][$key]);

                    continue;
                }

                // If the dependency has not yet been added to the sorted list, we can not
                // remove it from this asset's array of dependencies. We'll try again on
                // the next trip through the loop.
                if ( ! isset($sorted[$dependency])) continue;

                unset($assets[$asset]['dependencies'][$key]);
            }
        }
    }

    /**
     * Verify that an asset's dependency is valid.
     * A dependency is considered valid if it exists, is not a circular reference, and is
     * not a reference to the owning asset itself. If the dependency doesn't exist, no
     * error or warning will be given. For the other cases, an exception is thrown.
     *
     * @param  string $asset
     * @param  string $dependency
     * @param  array $original
     * @param  array $assets
     *
     * @throws \Exception
     * @return bool
     */
    protected function dependecyIsValid($asset, $dependency, $original, $assets)
    {
        if ( ! isset($original[$dependency]))
        {
            return false;
        }
        elseif ($dependency === $asset)
        {
            throw new \Exception("Asset [$asset] is dependent on itself.");
        }
        elseif (isset($assets[$dependency]) and in_array($asset, $assets[$dependency]['dependencies']))
        {
            throw new \Exception("Assets [$asset] and [$dependency] have a circular dependency.");
        }

        return true;
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