<?php 
namespace Teepluss\Component;

class Asset 
{
    protected $assets = array();

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
        if (! isset($this->assets[$group]) or count($this->assets[$group]) == 0) return '';

        $assets = '';

        foreach ($this->arrange($this->assets[$group]) as $name => $data) {
            $assets .= $data['source'] . PHP_EOL;
        }

        return $assets;
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

        while (count($assets) > 0) {
            foreach ($assets as $asset => $value) {
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
        if (count($assets[$asset]['dependencies']) == 0) {
            $sorted[$asset] = $value;
            unset($assets[$asset]);
        } else {
            foreach ($assets[$asset]['dependencies'] as $key => $dependency) {
                if (! $this->dependecyIsValid($asset, $dependency, $original, $assets)) {
                    unset($assets[$asset]['dependencies'][$key]);
                    continue;
                }

                // If the dependency has not yet been added to the sorted list, we can not
                // remove it from this asset's array of dependencies. We'll try again on
                // the next trip through the loop.
                if (! isset($sorted[$dependency])) continue;
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
        if (! isset($original[$dependency])) {
            return false;
        } elseif ($dependency === $asset) {
            throw new \Exception("Asset [$asset] is dependent on itself.");
        } elseif (isset($assets[$dependency]) and in_array($asset, $assets[$dependency]['dependencies'])) {
            throw new \Exception("Assets [$asset] and [$dependency] have a circular dependency.");
        }

        return true;
    }

    /**
     * Asset added.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  array   $dependencies
     * @param  array   $attributes
     * @param  boolean $external
     * @return void
     */
    public function added($type, $name, $source, $dependencies)
    {
        $this->register($type, $name, $source, $dependencies);
    }

    /**
     * Register asset.
     *
     * @param  string $type
     * @param  string $name
     * @param  string $source
     * @param  array  $dependencies
     * @return void
     */
    protected function register($type, $name, $source, $dependencies)
    {
        if (! in_array($type, ['style', 'script'])) {
            $type = (pathinfo($source, PATHINFO_EXTENSION) == 'css') ? 'style' : 'script';        
        }

        $this->assets[$type][$name] = compact('source', 'dependencies');
    }

}
