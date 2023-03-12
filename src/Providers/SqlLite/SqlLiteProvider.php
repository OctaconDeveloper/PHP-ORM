<?php

namespace PhpOrm\Providers\SqlLite;

use Exception;
use PhpOrm\Interfaces\QueryInterface;

class SqlLiteProvider extends QueryInterface {

    private $connection;
    private $query;
    private $conditions;
    private $table;

    public function __construct()
    {
        $this->connection = (new SqlLiteConnection())->connection();
    }



    /**
     * Handle raw sql query
     *
     * @param string $sqlQuery
     * @return mixed
     */
    public function rawQuery(string $sqlQuery)
    {
        return $this->connection->exec($sqlQuery);
    }
}
