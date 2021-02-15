<?php

namespace ArtisanWebLab\Spatial;

use ArtisanWebLab\Spatial\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseServiceProvider as ServiceProvider;

/**
 * Class SpatialDatabaseServiceProvider
 *
 * @package ArtisanWebLab\Spatial
 */
class SpatialDatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // The connection factory is used to create the actual connection instances on
        // the database. We will inject the factory into the manager so that it may
        // make the connections while they are actually needed and not of before.
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });
    }
}
