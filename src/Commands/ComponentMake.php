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

        $componentPath = app_path('Components/');

        $this->createComponentStuff($componentPath);
        $this->createComponent($componentPath.'/'.$componentName);

        return $this->info("Component $componentName created.");
    }

    protected function createComponentStuff($path)
    {
        if ( ! $this->app['files']->isDirectory($path))
        {
            $this->app['files']->makeDirectory($path, 0777, true);
        }

        $templatePath = realpath(__DIR__.'/../templates');

        $this->app['files']->copy($templatePath.'/gulpfile.js', $path.'/gulpfile.js');
    }

    protected function createComponent($path)
    {
        if ($this->app['files']->isDirectory($path))
        {
            if ( ! $this->confirm('Component is already exists, Do you want to replace? [y|n]'))
            {
                return false;
            }
        }

        $examplePath = realpath(__DIR__.'/../templates/component');

        // Delete component dir.
        $this->app['files']->deleteDirectory($path);

        // Create a component dir, if not exists.
        $this->app['files']->makeDirectory($path, 0777, true);

        // Copy stiff to component dir.
        $this->app['files']->copyDirectory($examplePath, $path);

        // Rename and Fix content in main class.

        $segments = explode('/', $path);
        $name = array_pop($segments);

        // Rename core file.
        $componentName  = $name;
        $ComponentNamespace = lcfirst($componentName);

        $componentClass = $path.'/'.$componentName.'.php';

        $this->app['files']->move($path.'/ExampleComponent.php', $componentClass);

        $tmp = $this->app['files']->get($componentClass);

        $replacements = [
            'AppNamespace'       => $this->getAppNamespace(),
            'ComponentName'      => $componentName,
            'ComponentNamespace' => $ComponentNamespace,
        ];

        $content = preg_replace_callback('/\{([a-z0-9]+)\}/i', function($matches) use ($replacements)
        {
            return $replacements[$matches[1]];

        }, $tmp);

        $this->app['files']->put($componentClass, $content);
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
