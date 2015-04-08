<?php namespace {AppNamespace}Components\{ComponentName};

use Teepluss\Component\BaseComponent;
use Illuminate\Foundation\Application as Application;
use Teepluss\Component\Contracts\BaseComponent as BaseComponentContract;

class {ComponentName} extends BaseComponent implements BaseComponentContract {

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
        // $this->add('name-1', script.js');
        // $this->add('name-2', script-2.js', ['name-1']);

        // Example add external assets.
        // $this->addExternal('name-e1', '//code.jquery.com/jquery-2.1.3.min.js');
        // $this->addExternal('name-e2', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', ['name-e1']);

        $arguments = array_merge($this->arguments, [
            'component' => $this->getComponentName()
        ]);

        $this->view('index', $arguments);

        return $this;
    }

}