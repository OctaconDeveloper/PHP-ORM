<?php
declare(strict_types=1);

namespace PhpOrm\Utils;

use PhpOrm\Providers\Mysql\MysqlProvider;
use PhpOrm\Providers\SqlLite\SqlLiteProvider;
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
     * @return MysqlProvider|PostgreSQLProvider|SqlLiteProvider
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
            case Driver::SQLITE:
                return new SqlLiteProvider();
                break;
            default:
                return new MysqlProvider();
        }
    }
}
