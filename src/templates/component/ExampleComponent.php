<?php namespace {AppNamespace}Components\{ComponentName};

use Teepluss\Component\BaseComponent;
use Teepluss\Component\BaseComponentInterface;
use Illuminate\Foundation\Application as Application;

class {ComponentName} extends BaseComponent implements BaseComponentInterface {

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
     * Component new instance.
     *
     * @param \Illuminate\Foundation\Application $app
     * @param mixed $arguments
     */
    public function __construct(Application $app, $arguments = array())
    {
        parent::__construct($app, $arguments);
    }

    /**
     * Prepare your code here!
     *
     * @return void
     */
    final public function prepare()
    {
        // Example add internal assets.
        //$this->addScript('script.js');
        //$this->addStyle('style.css');

        // Example add external assets.
        //$this->addScript('//code.jquery.com/jquery-2.1.3.min.js', true);
        //$this->addStyle('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', true, 'media');

        $arguments = array_merge($this->arguments, [
            'component' => $this->getComponentName()
        ]);

        $this->view('index', $arguments);

        return $this;
    }

}