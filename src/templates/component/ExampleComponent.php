<?php namespace {AppNamespace}Components\{ComponentName};

use Teepluss\Component\BaseComponent;
use Teepluss\Component\BaseComponentInterface;

class {ComponentName} extends BaseComponent implements BaseComponentInterface {

    protected $namespace = '{ComponentNamespace}';

    protected $arguments;

    public function __construct($arguments = null)
    {
        $this->arguments = $arguments;
    }

    final public function prepare()
    {
        $this->view('index', $this->arguments);

        return $this;
    }

}