<?php namespace {AppNamespace}Components\{ComponentName};

use Teepluss\Component\BaseComponent;
use Teepluss\Component\BaseComponentInterface;
use Illuminate\Foundation\Application as Application;

class {ComponentName} extends BaseComponent implements BaseComponentInterface {

    protected $app;

    protected $namespace = '{ComponentNamespace}';

    protected $arguments;

    public function __construct(Application $app, $arguments = array())
    {
        parent::__construct($app, $arguments);
    }

    final public function prepare()
    {
        $arguments = array_merge($this->arguments, [
            'component' => $this->getComponentName()
        ]);

        $this->view('index', $arguments);

        return $this;
    }

}