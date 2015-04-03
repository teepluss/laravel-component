<?php namespace Teepluss\Component\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Container\Container as Application;
use Illuminate\Console\AppNamespaceDetectorTrait;

class ComponentMake extends Command {

    use AppNamespaceDetectorTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'component:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create component.';

    /**
     * Foundation application.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $componentName = $this->argument('component');
        $componentName = ucfirst($componentName);

        $componentPath = app_path('Components/'.$componentName);

        if ($this->app['files']->isDirectory($componentPath))
        {
            return $this->error("Component {$componentName} is already exists.");
        }

        $this->app['files']->makeDirectory($componentPath, 0777, true);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['component', InputArgument::REQUIRED, 'Component name.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }

}
