<?php

namespace ArtisanWebLab\Spatial\Connectors;

use ArtisanWebLab\Spatial\PostgresConnection;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Connectors\ConnectionFactory as IlluminateConnectionFactory;
use PDO;

class ConnectionFactory extends IlluminateConnectionFactory
{
    /**
     * @param  string        $driver
     * @param  \Closure|PDO  $connection
     * @param  string        $database
     * @param  string        $prefix
     * @param  array         $config
     *
     * @return ConnectionInterface
     * @throws BindingResolutionException
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        if ($resolver = Connection::getResolver($driver)) {
            return $resolver($connection, $database, $prefix, $config);
        }

        return match ($driver) {
            'pgsql' => new PostgresConnection($connection, $database, $prefix, $config),
            default => parent::createConnection($driver, $connection, $database, $prefix, $config),
        };
    }
}
