<?php
declare(strict_types=1);

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
        $connectionString = "sqlite:{$this->databasPath}";
        try {
            $connection = new PDO($connectionString);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $connection;
        } catch (PDOException $error) {
            die(handleSQLError($error->getMessage()));
        }
    }
}