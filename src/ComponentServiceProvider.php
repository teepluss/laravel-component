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

		// Auto create app alias with boot method.
        $loader = AliasLoader::getInstance()->alias('Component', 'Teepluss\Component\Facades\Component');

        // Addition router.
        $this->app['router']->get('/c/{component}', function($component)
        {
        	$args = $this->app['request']->all();

        	$component = $this->app['component']->uses($component, $args)->render();

        	return view('component::micro', compact('component'));
        });
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('component', function($app)
		{
			return new Component($app);
		});

		$this->app->singleton('component.asset', function($app)
		{
			return  new Asset();
		});

		$this->app->singleton('component.make', function($app)
		{
			return new Commands\ComponentMake($app);
		});

		// Assign commands.
        $this->commands(
            'component.make'
        );

        $this->app->alias('component', 'Teepluss\Component\Contracts\Component');
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
