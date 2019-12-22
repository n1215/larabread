<?php
declare(strict_types=1);

namespace N1215\Larabread\Providers;

use Illuminate\Contracts\Config\Repository;
use N1215\Larabread\BreadcrumbListFactory;
use N1215\Larabread\BreadcrumbManager;
use N1215\Larabread\ViewComposers\BreadcrumbComposer;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerInterface;

/**
 * Class BreadcrumbServiceProvider
 * @package N1215\Larabread\Providers
 */
class BreadcrumbServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // merge config
        $configFilePath = __DIR__ . '/../../config/larabread.php';
        $this->mergeConfigFrom($configFilePath, 'larabread');

        // publish config file
        $this->publishes([$configFilePath => $this->app->configPath('larabread.php')], 'larabread-config');

        // load template
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'larabread');

        $this->app->singleton(BreadcrumbListFactory::class);

        $this->app->singleton(BreadcrumbManager::class);

        $this->app->singleton(BreadcrumbComposer::class);
        /** @var Repository $config */
        $config = $this->app['config'];
        $this->app->when(BreadcrumbComposer::class)
            ->needs('$config')
            ->give($config->get('larabread'));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /** @var Repository $config */
        $config = $this->app['config'];
        View::composer(
            array_values($config->get('larabread.templates', [])),
            BreadcrumbComposer::class
        );
    }
}
