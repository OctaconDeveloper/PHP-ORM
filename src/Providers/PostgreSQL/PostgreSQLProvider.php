<?php

namespace PhpOrm\Providers\PostgreSQL;

use PhpOrm\Interfaces\QueryInterface;

class PostgreSQLProvider {

    protected $connection;

    public function __construct()
    {
        $this->connection = (new PostgreSQLConnection())->connection();
    }

    public function all(string $table)
    {
        $sqlString =  "SELECT * FROM `".$table."`";
        $result = pg_query($this->connection, $sqlString);
        return json_encode($result);
    }
}