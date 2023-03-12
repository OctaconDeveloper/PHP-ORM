<?php

namespace PhpOrm\Providers\SqlLite;

use Exception;
use PDO;
use PDOException;
use PhpOrm\Interfaces\ConnectionInterface;
use SQLite3;

class SqlLiteConnection implements ConnectionInterface
{
    protected $databasPath;

    public function __construct()
    {
        $this->databasPath = $_ENV["DB_PATH"];
    }


    public function connection()
    {
        try {
            $connection = new SQLite3($this->databasPath);
            return $connection;
        } catch (Exception $error) {
            die(handleSQLError($error->getMessage()));
        }
    }
}