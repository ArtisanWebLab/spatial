<?php

namespace ArtisanWebLab\Spatial;

use ArtisanWebLab\Spatial\Query\Grammars\PostgresGrammar;
use Closure;
use Illuminate\Database\Grammar;
use Illuminate\Database\PostgresConnection as IlluminatePostgresConnection;
use PDO;

class PostgresConnection extends IlluminatePostgresConnection
{
    /**
     * Create a new database connection instance.
     *
     * @param  PDO|Closure  $pdo
     * @param  string       $database
     * @param  string       $tablePrefix
     * @param  array        $config
     *
     * @return void
     */
    public function __construct(Closure|PDO $pdo, string $database = '', string $tablePrefix = '', array $config = [])
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return Grammar
     */
    protected function getDefaultQueryGrammar(): Grammar
    {
        return $this->withTablePrefix(new PostgresGrammar);
    }
}
