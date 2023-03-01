<?php

namespace PhpOrm\Providers\Mysql;

use PDOException;
use PhpOrm\Interfaces\QueryInterface;

class MysqlProvider implements QueryInterface
{

    protected $connection;
    protected $query;
    protected $conditions;
    protected $table = "cryptos";

    public function __construct()
    {
        $this->connection = (new MysqlConnection())->connection();
        $this->query = "SELECT * FROM `" . $this->table . "`";
    }

    /**
     * Set table name
     *
     * @param string $table
     * @return void
     */
    public function setTable(string $table)
    {
        $this->table = $table;
        return $this;
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

            $sqlString =  "INSERT INTO `{$this->table}` ({$columns}) VALUES ({$attributes})";
            return $this->connection->prepare($sqlString)->execute($data);
        } catch (PDOException $error) {
            die(handleMysqlError($error->getMessage()));
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
        $this->connection->beginTransaction();
        try {
            foreach ($data as $datum) {
                $columns = getMysqlColumns($datum);
                $attributes = getMysqlColumnsAttribute($datum);

                $sqlString =  "INSERT INTO `{$this->table}` ({$columns}) VALUES ({$attributes})";
                $this->connection->prepare($sqlString)->execute($datum);
            }

            $this->connection->commit();
            return true;
        } catch (PDOException $error) {
            $this->connection->rollBack();
            die(handleMysqlError($error->getMessage()));
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
        $sqlString =  $this->query . $this->conditions;
        return $this->connection->query($sqlString)->fetchAll();
    }

    /**
     * Get first row
     *
     * @param string $column
     * @return integer|string
     */
    public function first()
    {
        if (strpos($this->conditions, "LIMIT") !== false) {
            die(handleMysqlError("first() can not be used with take() \n\n"));
        }

        $sqlString =  $this->query . $this->conditions . " LIMIT 1";

        return $this->connection->query($sqlString)->fetch();
    }

    /**
     * Get last row
     *
     * @param string $column
     * @return integer|string
     */
    public function last(string $column = 'id')
    {
        if (strpos($this->conditions, "LIMIT") !== false) {
            die(handleMysqlError("last() can not be used with take() \n\n"));
        }
        //check if condition has ORDER BY;
        if (strpos($this->conditions, "ORDER BY") !== false) {
            $sqlString =  $this->query  . $this->conditions;
        } else {
            $sqlString =  $this->query  . $this->conditions . " ORDER BY `{$column}` DESC LIMIT 1";
        }
        return $this->connection->query($sqlString)->fetch();
    }

    /**
     * Select where condition is meet
     *
     * @param string $column
     * @return integer|string
     */
    public function where(string $column, string $operand, string $value = null)
    {
        if (isset($value) && in_array($operand, $this->getOperators())) {
            $operator = $operand;
            $value = $value;
        } else {
            $operator = "=";
            $value = $operand;
        }
        $this->setCondition("`{$column}` {$operator} '{$value}'");
        return  $this;
    }

    /**
     * Optional second condition
     *
     * @param string $column
     * @return integer|string
     */
    public function andWhere(string $column, string $operand, string $value = null)
    {
        if (isset($value) && in_array($operand, $this->getOperators())) {
            $operator = $operand;
            $value = $value;
        } else {
            $operator = "=";
            $value = $operand;
        }
        $this->setCondition("`{$column}` {$operator} '{$value}'", "AND");
        return $this;
    }


    /**
     * Compulsory second condition
     *
     * @param string $column
     * @return integer|string
     */
    public function orWhere(string $column, string $operand, string $value = null)
    {
        if (isset($value) && in_array($operand, $this->getOperators())) {
            $operator = $operand;
            $value = $value;
        } else {
            $operator = "=";
            $value = $operand;
        }
        $this->setCondition("`{$column}` {$operator} '{$value}'", "OR");
        return $this;
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

        $this->setCondition("ORDER BY `{$column}` {$orderby}", "ORDER");
        return $this;
    }

    /**
     * Get limit of query
     *
     * @param string $column
     * @return integer|string
     */
    public function take(int $limit)
    {
        $this->setCondition("LIMIT {$limit}", "LIMIT");
        return $this;
    }

    /**
     * Get total count of query
     *
     * @param string $column
     * @return integer|string
     */
    public function count(string $column = "*")
    {
        $sqlString = "SELECT count({$column}) FROM `" . $this->table . "`" . $this->conditions;
        return $this->connection->query($sqlString)->fetchColumn();
    }

    /**
     * Get maximum value of row
     *
     * @param string $column
     * @return integer|string
     */
    public function max(string $column)
    {
        $sqlString = "SELECT MAX({$column}) FROM `" . $this->table . "`" . $this->conditions;
        return $this->connection->query($sqlString)->fetchColumn();
    }

    /**
     * Get minimum value of row
     *
     * @param string $column
     * @return integer|string
     */
    public function min(string $column)
    {
        $sqlString = "SELECT MIN({$column}) FROM `" . $this->table . "`" . $this->conditions;
        return $this->connection->query($sqlString)->fetchColumn();
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
            $sqlString = "UPDATE `{$this->table}`  SET " . $updateSql . " " . $this->conditions;
            return $this->connection->prepare($sqlString)->execute($data);
        } catch (PDOException $error) {
            die(handleMysqlError($error->getMessage()));
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
        return $this->connection->query($sqlQuery)->fetch();
    }


    /**
     * Begin database transaction
     *
     * @return void
     */
    public function begingTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Handle database commit transaction
     *
     * @return void
     */
    public function commitTransaction()
    {
        $this->connection->commit();
    }

    /**
     * Handle database transaction rollback
     *
     * @return void
     */
    public function rollbackTransaction()
    {
        $this->connection->rollBack();
    }


    /**
     * Handle database transaction action
     *
     * @param [type] $transaction
     * @return void
     */
    public function transaction($transaction)
    {
        return $this->connection->beginTransaction();
        try {
            $transaction;
            $this->connection->commit();
        } catch (PDOException $error) {
            $this->connection->rollBack();
            die(handleMysqlError($error->getMessage()));
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
        if ($joinKey && !empty($this->conditions)) {
            switch ($joinKey) {
                case 'ORDER':
                case 'LIMIT':
                    $this->conditions .= " " . $condition;
                    break;
                default:
                    $this->conditions .= " " . $joinKey . " " . $condition;
            }
        } else {
            $this->conditions .= " WHERE " . $condition;
        }
        return $this->conditions;
    }
}
