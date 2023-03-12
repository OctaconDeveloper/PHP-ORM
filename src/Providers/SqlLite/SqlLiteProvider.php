<?php

namespace PhpOrm\Providers\SqlLite;

use Exception;
use PDOException;
use PhpOrm\Interfaces\QueryInterface;

class SqlLiteProvider implements QueryInterface
{

    private $connection;
    private $query;
    private $conditions;
    private $table;

    public function __construct()
    {
        $this->connection = (new SqlLiteConnection())->connection();
    }

    /**
     * Handle raw sql query
     *
     * @param string $sqlQuery
     * @return mixed
     */
    public function rawQuery(string $sqlQuery)
    {
        return $this->connection->exec($sqlQuery);
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
        $this->query = "SELECT * FROM `" . $this->table . "`";
        return $this;
    }

    /**
     * Insert single rows
     *
     * @param array $data
     * @return bool|null
     */
    public function insert(array $data): ?bool
    {
        try {
            $columns = getMysqlColumns($data);
            $attributes = getMysqlColumnsAttribute($data);

            $sqlString =  "INSERT INTO `{$this->table}` ({$columns}) VALUES ({$attributes})";
            return $this->connection->prepare($sqlString)->execute($data);
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
        } catch (Exception $error) {
            $this->connection->rollBack();
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
            die(handleSQLError("first() can not be used with take() \n\n"));
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
    public function last(string $column = null)
    {
        if (!$column) {
            $column =  $this->getFirstColumn();
        }
        if (strpos($this->conditions, "LIMIT") !== false) {
            die(handleSQLError("last() can not be used with take() \n\n"));
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
     * Get the first column of a table
     *
     * @return string|null
     */
    protected function getFirstColumn(): ?string
    {
        $sqlString = "SELECT * FROM {$this->table};";
        $result = $this->connection->query($sqlString)->fetch();
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
     * @param string $value = null
     * @param string $operand = null
     * @return integer|string
     */
    public function count(string $column = "*", string $value = null, string $operand = null)
    {
        if (isset($operand) && in_array($operand, $this->getOperators())) {
            $operator = $operand;
        } else {
            $operator = "=";
        }

        if ($column != "*" && isset($value)) {
            $sqlString = "SELECT count(*) FROM `" . $this->table . "`" . $this->conditions;
        } else {
            $this->setCondition("`{$column}` {$operator} '{$value}'");
            $sqlString = "SELECT count({$column}) FROM `" . $this->table . "`" . $this->conditions;
        }

        return $this->connection->query($sqlString)->fetchColumn();
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
            $column =  $this->getFirstColumn();
        }
        $sqlString = "SELECT MAX({$column}) FROM `" . $this->table . "`" . $this->conditions;
        return $this->connection->query($sqlString)->fetchColumn();
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
            $column =  $this->getFirstColumn();
        }
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
        } catch (Exception $error) {
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
            if (!$this->conditions) {
                $sqlString = "DELETE FROM `{$this->table}`";
            } else {
                $sqlString = "DELETE FROM `{$this->table}`" . $this->conditions;
            }
            return $this->connection->prepare($sqlString)->execute();
        } catch (Exception $error) {
            die(handleSQLError($error->getMessage()));
        }
    }


    /**
     * Begin database transaction
     *
     * @return void
     */
    public function begingTransaction()
    {
        $this->connection->beginTransaction();
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
        $this->connection->beginTransaction();
        try {
            $transaction;
            $this->connection->commit();
            return true;
        } catch (Exception $error) {
            $this->connection->rollBack();
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
