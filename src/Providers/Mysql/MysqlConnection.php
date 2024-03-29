<?php
declare(strict_types=1);

namespace PhpOrm\Providers\Mysql;

use PDO;
use PDOException;
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

        $connectionString = "mysql:host={$this->databaseHost};dbname={$this->databaseName};port={$this->databasePort}";
        try {
            $connection = new PDO($connectionString, $this->databaseUser, $this->databasePassword);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $connection;
        } catch (PDOException $error) {
            die(handleSQLError($error->getMessage()));
        }
    }
}
