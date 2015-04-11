<?php namespace Teepluss\Component;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ComponentServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadViewsFrom(__DIR__.'/views', 'component');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/component'),
            __DIR__.'/../config/component.php' => config_path('component.php'),
        ]);

		// Auto create app alias with boot method.
        $loader = AliasLoader::getInstance()->alias('Component', 'Teepluss\Component\Facades\Component');

        if (config('component.widget.enable'))
        {
            $path = config('component.widget.path');

            // Addition router.
            $this->app['router']->get($path, function($name)
            {
            	$args = $this->app['request']->all();

            	$component = $this->app['component']->uses($name, $args)->render();

            	return view('component::widget', compact('name', 'component'));
            });
        }
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $configPath = __DIR__.'/../config/component.php';

        // Merge config to allow user overwrite.
        $this->mergeConfigFrom($configPath, 'component');

        $this->registerComponent();
        $this->registerComponentAsset();
        $this->registerComponentMakeCommand();

		// Assign commands.
        $this->commands(
            'component.make'
        );

        $this->app->alias('component', 'Teepluss\Component\Contracts\Component');
	}

    protected function registerComponent()
    {
        $this->app->singleton('component', function($app)
        {
            return new Component($app);
        });
    }

    protected function registerComponentAsset()
    {
        $this->app->singleton('component.asset', function($app)
        {
            return  new Asset();
        });
    }

    protected function registerComponentMakeCommand()
    {
        $this->app->singleton('component.make', function($app)
        {
            return new Commands\ComponentMake($app);
        });
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['component', 'component.asset'];
	}

}
