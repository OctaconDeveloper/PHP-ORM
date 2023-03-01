<?php

namespace PhpOrm\Providers\Mysql;

use PDO;
use Exception;
use PDOException;
use PhpOrm\Errors\MysqlErrorHandler;
use PhpOrm\Interfaces\ConnectionInterface;

class MysqlConnection implements ConnectionInterface
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

        $connectionString = "mysql:host={$this->databaseHost};dbname=alex;port={$this->databasePort}";
        try {
            $connection = new PDO($connectionString, $this->databaseUser, $this->databasePassword);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch (PDOException $error) {
            die(handleMysqlError($error->getMessage()));
        }
    }
}
