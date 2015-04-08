<?php namespace Teepluss\Component;

use Illuminate\Foundation\Application as Application;

class BaseComponent {

    /**
     * Application.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Component namespace.
     *
     * @var string
     */
    protected $namespace = '{ComponentNamespace}';

    /**
     * Component arguments.
     *
     * @var mixed
     */
    protected $arguments;

    /**
     * Component name.
     *
     * @var string
     */
    protected $componentName;

    /**
     * View data.
     *
     * @var array
     */
    protected $view;

    /**
     * All assets.
     *
     * @var array
     */
    protected $assets;

    /**
     * Component new instance.
     *
     * @param \Illuminate\Foundation\Application $app
     * @param array $arguments
     */
    public function __construct(Application $app, $arguments = array())
    {
        $this->app = $app;

        $this->arguments = $arguments;
    }

    /**
     * Get object cache key.
     *
     * @return string
     */
    public function getCacheKey()
    {
        return md5($this->namespace.json_encode($this->arguments));
    }

    /**
     * Get component namespace.
     *
     * @return string
     */
    public function getComponentNamespace()
    {
        return $this->namespace;
    }

    /**
     * Get component name.
     *
     * @return string
     */
    public function getComponentName()
    {
        return ucfirst($this->namespace);
    }

    /**
     * Get component assets.
     *
     * @return array
     */
    public function getComponentAssets()
    {
        return $this->assets;
    }

    /**
     * Get component path.
     *
     * @param  string $path
     * @return string
     */
    public function getComponentPath($path = null)
    {
        return app_path('Components/'.$this->getComponentName()).'/'.ltrim($path, '/');
    }

    /**
     * Get component public path.
     *
     * @param  string $path
     * @return string
     */
    public function getComponentPublicPath($path = null)
    {
        return asset('components/'.$this->getComponentName().'/'.ltrim($path, '/'));
    }

    /**
     * Get argument by key.
     *
     * @param  string $name
     * @return mixed
     */
    protected function argument($name)
    {
        return array_get($this->arguments, $name);
    }

    /**
     * Add component script.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  array   $dependencies
     * @param  array   $attributes
     * @param  boolean $external
     * @return void
     */
    public function script($name, $source, $dependencies = array(), $attributes = array())
    {
        $this->add('script', $name, $source, $dependencies, $attributes, false);
    }

    /**
     * Add component style.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  array   $dependencies
     * @param  array   $attributes
     * @param  boolean $external
     * @return void
     */
    public function style($name, $source, $dependencies = array(), $attributes = array())
    {
        $this->add('style', $name, $source, $dependencies, $attributes, false);
    }

    /**
     * Add asset script and style.
     *
     * @param string  $type
     * @param string  $name
     * @param string  $source
     * @param array   $dependencies
     * @param array   $attributes
     * @param boolean $external
     */
    protected function add($type, $name, $source, $dependencies = array(), $attributes = array(), $external = false)
    {
        $pattern = "~^//|http|\.js|\.css~i";

        if (preg_match($pattern, $source))
        {
            switch ($type)
            {
                case 'script' :
                    $attributes['src'] = asset($source);
                    $source = '<script'.$this->attributes($attributes).'></script>';
                    break;
                case 'style' :
                    $attributes = array_merge(['rel' => 'stylesheet', 'media' => 'all'], $attributes);

                    $attributes['href'] = asset($source);

                    $source = '<link'.$this->attributes($attributes).'>';
                    break;
            }
        }

        $this->app['component.asset']->added($type, $name, $source, $dependencies, $attributes);
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array  $attributes
     * @return string
     */
    protected function attributes($attributes)
    {
        $html = array();

        // For numeric keys we will assume that the key and the value are the same
        // as this will convert HTML attributes such as "required" to a correct
        // form like required="required" instead of using incorrect numerics.
        foreach ((array) $attributes as $key => $value)
        {
            $element = $this->attributeElement($key, $value);

            if ( ! is_null($element)) $html[] = $element;
        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
    protected function attributeElement($key, $value)
    {
        if (is_numeric($key)) $key = $value;

        if ( ! is_null($value)) return $key.'="'.e($value).'"';
    }

    /**
     * Prepare view path and data.
     *
     * @param  string $path
     * @param  mixed  $data
     * @return string
     */
    public function view($path, $data)
    {
        $this->view = [
            'path' => $path,
            'data' => $data
        ];
    }

    /**
     * Execute component script.
     *
     * @return void
     */
    public function execute()
    {
        return $this->view;
    }

}