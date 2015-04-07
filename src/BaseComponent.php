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
     * Add component script.
     *
     * @param string  $source
     * @param boolean $external
     */
    public function addScript($source, $external = false)
    {
        // If lead with http or file extension, wrap the html tag to source.
        if (preg_match('/^http|\.js/', $source))
        {
            if ($external == false)
            {
                $source = $this->getComponentPublicPath($source);
            }

            $source = '<script src="'.asset($source).'"></script>' . "\n";
        }

        $this->app['view']->inject('component-scripts', '@parent'.$source);
    }

    /**
     * Add component style.
     *
     * @param string  $source
     * @param boolean $external
     * @param string  $media
     */
    public function addStyle($source, $external = false, $media = 'screen')
    {
        // If lead with http or file extension, wrap the html tag to source.
        if (preg_match('/^http|\.css/', $source))
        {
            if ($external == false)
            {
                $source = $this->getComponentPublicPath($source);
            }

            $source = '<link href="'.asset($source).'" rel="stylesheet" media="'.$media.'">' . "\n";
        }

        $this->app['view']->inject('component-styles', '@parent'.$source);
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