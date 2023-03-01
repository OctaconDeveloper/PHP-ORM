<?php

namespace PhpOrm;

use PhpOrm\Interfaces\QueryInterface;
use PhpOrm\Utils\Provider;

class Query implements QueryInterface
{

    protected static $provider;

    public function __construct()
    {
        self::$provider = (new Provider())->load();
    }

    /**
     * Initialize table for query
     *
     * @param string $table
     * @return new self()
     */
    public static function table(string $table)
    {
        // self::$provider->setTable($table);
        return new self();
    }


    /**
     * Returns all available records in a table
     *
     * @return array
     */
    public function all()
    {
        return self::$provider->all();
    }

    /**
     * Get first record in a table
     *
     * @return array
     */
    public function first()
    {
        return self::$provider->first();
    }

    /**
     * Get last record in a table
     *
     * @return array
     */
    public function last(string $column = 'id')
    {
        return self::$provider->last($column);
    }


    public function insert(array $data)
    {
        return self::$provider->insert($data);
    }

    public function insertBulk(array $data)
    {
        return self::$provider->insertBulk($data);
    }

    public function where(string $column, string $operand, string $value = null)
    {
        return self::$provider->where($column, $operand, $value);
    }

    public function andWhere(string $column, string $operand, string $value = null)
    {
        return self::$provider->andWhere($column,$operand,$value);
    }

    public function orWhere(string $column, string $operand, string $value = null)
    {
        return self::$provider->orWhere($column, $operand, $value);
    }

    public function orderBy(string $column, string $order="asc")
    {
        return self::$provider->orderBy($column, $order);
    }

    public function rawQuery(string $sqlQuery)
    {
        return self::$provider->rawQuery($sqlQuery);
    }

    public function take(int $limit)
    {
        return self::$provider->take($limit);
    }

    public function count(string $column = "*")
    {
        return self::$provider->count($column);
    }

    public function max(string $column)
    {
        return self::$provider->max($column);
    }

    public function min(string $column)
    {
        return self::$provider->min($column);
    }

    public function update(array $data)
    {
        return self::$provider->update($data);
    }


    





    public function begingTransaction(){}
    public function commitTransaction(){}
    public function rollbackTransaction(){}
    public function transaction($transaction){}


}
