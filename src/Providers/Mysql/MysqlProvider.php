<?php

declare(strict_types=1);

namespace PhpOrm\Providers\Mysql;

use PDOException;
use PhpOrm\Interfaces\QueryInterface;
use PhpOrm\Utils\Constant;

class MysqlProvider implements QueryInterface
{

    private static $connection;
    private static $query;
    private static $secondQuery = null;
    private static $secondQueryOrder = Constant::WITHMANY;
    private static $conditions;
    private static $table;

    public function __construct()
    {
        self::$connection = (new MysqlConnection())->connection();
    }

    /**
     * Set table name
     *
     * @param string $table
     * @return void
     */
    public function setTable(string $table)
    {
        self::$table = $table;
        self::$query = "SELECT * FROM `" . self::$table . "`";
        return new self();
    }


    /**
     * Insert single rows
     *
     * @param array $data
     * @return bool|null
     */
    public function insert(array $data): bool
    {
        try {
            $columns = getMysqlColumns($data);
            $attributes = getMysqlColumnsAttribute($data);

            $sqlString =  "INSERT INTO `" . self::$table . "` ({$columns}) VALUES ({$attributes})";
            return self::$connection->prepare($sqlString)->execute($data);
        } catch (PDOException $error) {
            die(handleSQLError($error->getMessage()));
        }
    }


    /**
     * Handle bulk insert
     *
     * @param array $data
     * @return bool
     */
    public function insertBulk(array $data): bool
    {
        self::$connection->beginTransaction();
        try {
            foreach ($data as $datum) {
                $columns = getMysqlColumns($datum);
                $attributes = getMysqlColumnsAttribute($datum);

                $sqlString =  "INSERT INTO `" . self::$table . "` ({$columns}) VALUES ({$attributes})";
                self::$connection->prepare($sqlString)->execute($datum);
            }

            self::$connection->commit();
            return true;
        } catch (PDOException $error) {
            self::$connection->rollBack();
            die(handleSQLError($error->getMessage()));
        }
    }




    /**
     * Get all rows
     *
     * @param string $column
     * @return integer|string
     */
    public function all()
    {
        $sqlString =  self::$query . self::$conditions;
        $sqlResults = self::$connection->query($sqlString)->fetchAll();
        if (self::$secondQuery) {
            $results = [];
            foreach ($sqlResults as $sqlResult) {
                $newQuery = self::withCondition($sqlResult, self::$secondQuery[0], self::$secondQuery[1], self::$secondQuery[2], self::$secondQueryOrder);
                $secondSqlResult =  (self::$secondQueryOrder == Constant::WITHONE) ? self::$connection->query($newQuery)->fetch() : self::$connection->query($newQuery)->fetchAll();
                $sqlResult["with"] = $secondSqlResult;
                $results[] = $sqlResult;
            }

            return $results;
        }

        return $sqlResults;
    }

    /**
     * Get first row
     *
     * @param string $column
     * @return integer|string
     */
    public function first()
    {
        if (isset(self::$conditions) && strpos(self::$conditions, "LIMIT") !== false) {
            die(handleSQLError("first() can not be used with take() \n\n"));
        }

        $sqlString =  self::$query . self::$conditions . " LIMIT 1";

        $sqlResult = self::$connection->query($sqlString)->fetch();

        if (self::$secondQuery) {

            $query = self::withCondition($sqlResult, self::$secondQuery[0], self::$secondQuery[1], self::$secondQuery[2], self::$secondQueryOrder);
            $secondSqlResult =  (self::$secondQueryOrder == Constant::WITHONE) ? self::$connection->query($query)->fetch() : self::$connection->query($query)->fetchAll();
            $sqlResult["with"] = $secondSqlResult;
        }

        return $sqlResult;
    }

    /**
     * Get last row
     *
     * @param string $column
     * @return integer|string
     */
    public function last(string $column = null)
    {
        if (!$column) {
            $column =  self::getFirstColumn();
        }
        if (strpos(self::$conditions, "LIMIT") !== false) {
            die(handleSQLError("last() can not be used with take() \n\n"));
        }
        //check if condition has ORDER BY;
        if (strpos(self::$conditions, "ORDER BY") !== false) {
            $sqlString =  self::$query  . self::$conditions;
        } else {
            $sqlString =  self::$query  . self::$conditions . " ORDER BY `{$column}` DESC LIMIT 1";
        }

        $sqlResult = self::$connection->query($sqlString)->fetch();

        if (self::$secondQuery) {
            $query = self::withCondition($sqlResult, self::$secondQuery[0], self::$secondQuery[1], self::$secondQuery[2], self::$secondQueryOrder);
            $secondSqlResult =  (self::$secondQueryOrder == Constant::WITHONE) ? self::$connection->query($query)->fetch() : self::$connection->query($query)->fetchAll();
            $sqlResult["with"] = $secondSqlResult;
        }

        return $sqlResult;
    }

    /**
     * Get the first column of a table
     *
     * @return string|null
     */
    protected function getFirstColumn(): ?string
    {
        $sqlString = "SELECT * FROM `" . self::$table . "`;";
        $result = self::$connection->query($sqlString)->fetch();
        return array_key_first($result);
    }

    /**
     * Select where condition is meet
     *
     * @param string $column
     * @return integer|string
     */
    public function where(string $column, string $operand, string $value = null)
    {
        if (isset($value) && in_array($operand, self::getOperators())) {
            $operator = $operand;
            $value = $value;
        } else {
            $operator = "=";
            $value = $operand;
        }
        self::setCondition("`{$column}` {$operator} '{$value}'");
        return  new self();
    }

    /**
     * Optional second condition
     *
     * @param string $column
     * @return integer|string
     */
    public function andWhere(string $column, string $operand, string $value = null)
    {
        if (isset($value) && in_array($operand, self::getOperators())) {
            $operator = $operand;
            $value = $value;
        } else {
            $operator = "=";
            $value = $operand;
        }
        self::setCondition("`{$column}` {$operator} '{$value}'", "AND");
        return new self();
    }


    /**
     * Compulsory second condition
     *
     * @param string $column
     * @return integer|string
     */
    public function orWhere(string $column, string $operand, string $value = null)
    {
        if (isset($value) && in_array($operand, self::getOperators())) {
            $operator = $operand;
            $value = $value;
        } else {
            $operator = "=";
            $value = $operand;
        }
        self::setCondition("`{$column}` {$operator} '{$value}'", "OR");
        return new self();
    }


    /**
     * Order query by given constraints
     *
     * @param string $column
     * @return integer|string
     */
    public function orderBy(string $column, string $order)
    {
        $orderby = ($order == strtolower("ASC")) ? "ASC" : "DESC";

        self::setCondition("ORDER BY `{$column}` {$orderby}", "ORDER");
        return new self();
    }

    /**
     * Get limit of query
     *
     * @param string $column
     * @return integer|string
     */
    public function take(int $limit)
    {
        self::setCondition("LIMIT {$limit}", "LIMIT");
        return new self();
    }

    /**
     * Get total count of query
     *
     * @param string $column
     * @param string $value = null
     * @param string $operand = null
     * @return integer|string
     */
    public function count(string $column = null, string $value = null, string $operand = null)
    {
        if (isset($operand) && in_array($operand, self::getOperators())) {
            $operator = $operand;
        } else {
            $operator = "=";
        }

        if (!$column) {
            $sqlString = "SELECT count(*) FROM `" . self::$table . "`" . self::$conditions;
        } else {
            self::setCondition("{$column} {$operator} '{$value}'");
            $sqlString = "SELECT count({$column}) FROM `" . self::$table . "`" . self::$conditions;
        }
        var_dump("string " . $sqlString, "conditions " . self::$conditions);

        // return self::$connection->query($sqlString)->fetchColumn();
        return "Yea";
    }

    /**
     * Get maximum value of row
     *
     * @param string $column
     * @return integer|string
     */
    public function max(string $column = null)
    {
        if (!$column) {
            $column =  self::getFirstColumn();
        }
        $sqlString = "SELECT MAX({$column}) FROM `" . self::$table . "`" . self::$conditions;
        return self::$connection->query($sqlString)->fetchColumn();
    }

    /**
     * Get minimum value of row
     *
     * @param string $column
     * @return integer|string
     */
    public function min(string $column = null)
    {
        if (!$column) {
            $column =  self::getFirstColumn();
        }
        $sqlString = "SELECT MIN({$column}) FROM `" . self::$table . "`" . self::$conditions;
        return self::$connection->query($sqlString)->fetchColumn();
    }


    /**
     * Handle update query
     *
     * @param array $data
     * @return bool|null
     */
    public function update(array $data): ?bool
    {
        try {
            $updateSql = getMysqlUpdateAttribute($data);
            $sqlString = "UPDATE `" . self::$table . "`  SET " . $updateSql . " " . self::$conditions;
            return self::$connection->prepare($sqlString)->execute($data);
        } catch (PDOException $error) {
            die(handleSQLError($error->getMessage()));
        }
    }

    /**
     * Delete record(s)
     *
     * @return boolean|null
     */
    public function delete(): ?bool
    {
        try {
            if (!self::$conditions) {
                $sqlString = "DELETE FROM `" . self::$table . "`";
            } else {
                $sqlString = "DELETE FROM `" . self::$table . "`" . self::$conditions;
            }
            return self::$connection->prepare($sqlString)->execute();
        } catch (PDOException $error) {
            die(handleSQLError($error->getMessage()));
        }
    }




    /**
     * Handle raw sql query
     *
     * @param string $sqlQuery
     * @return mixed
     */
    public function rawQuery(string $sqlQuery)
    {
        return self::$connection->query($sqlQuery)->fetch();
    }


    /**
     * Begin database transaction
     *
     * @return void
     */
    public function begingTransaction()
    {
        self::$connection->beginTransaction();
    }

    /**
     * Handle database commit transaction
     *
     * @return void
     */
    public function commitTransaction()
    {
        self::$connection->commit();
    }

    /**
     * Handle database transaction rollback
     *
     * @return void
     */
    public function rollbackTransaction()
    {
        self::$connection->rollBack();
    }


    /**
     * Handle database transaction action
     *
     * @param [type] $transaction
     * @return void
     */
    public function transaction($transaction)
    {
        self::$connection->beginTransaction();
        try {
            $transaction;
            self::$connection->commit();
            return true;
        } catch (PDOException $error) {
            self::$connection->rollBack();
            die(handleSQLError($error->getMessage()));
        }
    }


    /**
     * Undocumented function
     *
     * @return array
     */
    protected function getOperators(): array
    {
        return [
            "=", ">", "<", ">=", "<=", "!=", "<>", "<>"
        ];
    }

    /**
     * Set select condition
     *
     * @param [type] $condition
     * @param [type] $joinKey
     * @return string
     */
    protected function setCondition($condition, $joinKey = null): string
    {
        if ($joinKey && !empty(self::$conditions)) {
            switch ($joinKey) {
                case 'ORDER':
                case 'LIMIT':
                    self::$conditions .= " " . $condition;
                    break;
                default:
                    self::$conditions .= " " . $joinKey . " " . $condition;
            }
        } else {
            self::$conditions .= " WHERE " . $condition;
        }
        return self::$conditions;
    }

    /**
     * Add one to many ralationship query
     *
     * @param string $table
     * @param string $foreignKey
     * @param string $primaryKey
     * @return new self()
     */
    public function withMany(string $table, string $foreignKey, string $primaryKey = 'id')
    {
        self::$secondQuery = [
            $table,
            $foreignKey,
            $primaryKey
        ];
        self::$secondQueryOrder = Constant::WITHMANY;
        return new self();
    }

    /**
     * Add one to one ralationship query
     *
     * @param string $table
     * @param string $foreignKey
     * @param string $primaryKey
     * @return new self()
     */
    public function withOne(string $table, string $foreignKey, string $primaryKey = 'id')
    {
        self::$secondQuery = [
            $table,
            $foreignKey,
            $primaryKey
        ];
        self::$secondQueryOrder = Constant::WITHONE;

        return new self();
    }

    protected function withCondition(array $query, string $table, string $foreignKey, string $primaryKey, string $limit = Constant::WITHONE)
    {
        $columnValue = $query[$primaryKey];
        $query = "SELECT * FROM {$table} WHERE {$foreignKey} = '$columnValue' ";
        $query .= $limit == Constant::WITHONE ? " LIMIT 1 " : "";
        return $query;
    }
}
