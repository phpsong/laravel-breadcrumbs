<?php namespace DaveJamesMiller\Breadcrumbs;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	// Can't enable this because there appears to be a bug in Laravel where a
	// non-deferred service provider can't use a deferred one because the boot
	// method is not called - see CustomServiceProviderTest.
	// protected $defer = true;

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['breadcrumbs'];
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['breadcrumbs'] = $this->app->share(function($app)
		{
			$breadcrumbs = $this->app->make('DaveJamesMiller\Breadcrumbs\Manager');

			$viewPath = __DIR__ . '/../views/';

			$this->loadViewsFrom($viewPath, 'breadcrumbs');
			$this->loadViewsFrom($viewPath, 'laravel-breadcrumbs'); // Backwards-compatibility with 2.x

			$breadcrumbs->setView($app['config']['breadcrumbs.view']);

			return $breadcrumbs;
		});
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$configFile = __DIR__ . '/../config/breadcrumbs.php';

		$this->mergeConfigFrom($configFile, 'breadcrumbs');

		$this->publishes([
			$configFile => config_path('breadcrumbs.php')
		]);

		// Load the app breadcrumbs if they're in app/Http/breadcrumbs.php
		if (file_exists($file = $this->app['path'].'/Http/breadcrumbs.php'))
		{
			require $file;
		}
	}

}
