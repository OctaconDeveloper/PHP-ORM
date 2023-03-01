<?php

namespace PhpOrm\Utils;

use PhpOrm\Providers\Mysql\MysqlProvider;
use PhpOrm\Providers\PostgreSQL\PostgreSQLProvider;

class Provider
{

    protected $dbConnection;

    public function __construct()
    {
        $this->dbConnection = $_ENV["DB_CONNECTION"];
    }


    /**
     * Load database provider module based on the db connection type
     *
     * @return MysqlProvider|PostgreSQLProvider|MongoDBProvider
     */
    public function load()
    {
        switch (strtolower($this->dbConnection)) {
            case Driver::MYSQL:
                return new MysqlProvider();
                break;
            case Driver::POSTGRES:
                return new PostgreSQLProvider();
                break;
            default:
                return new MysqlProvider();
        }
    }
}
