<?php

namespace PhpOrm\Providers\PostgreSQL;

use Exception;
use PDO;
use PhpOrm\Interfaces\ConnectionInterface;

class PostgreSQLConnection implements ConnectionInterface
{
    protected $databaseName;
    protected $databaseUser;
    protected $databaseHost;
    protected $databasePassword;
    protected $databasePort;

    public function __construct()
    {
        $this->databaseName = $_ENV["DB_DATABASE"];
        $this->databaseUser = $_ENV["DB_USERNAME"];
        $this->databaseHost = $_ENV["DB_HOST"];
        $this->databasePassword = $_ENV["DB_PASSWORD"];
        $this->databasePort = $_ENV["DB_PORT"];
    }
    public function connection()
    {
        $connectionString = "pgsql:host={$this->databaseHost}; port={$this->databasePort}; dbname={$this->databaseName}; user={$this->databaseUser}; password={$this->databasePassword}";
        try {
            $connection = new PDO($connectionString, $this->databaseUser, $this->databasePassword);
            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch (Exception $error) {
            die(handleSQLError($error->getMessage()));
        }
    }
}
