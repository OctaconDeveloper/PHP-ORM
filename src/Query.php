<?php

declare(strict_types=1);

namespace PhpOrm;

use PhpOrm\Interfaces\QueryInterface;
use PhpOrm\Utils\Provider;

class Query implements QueryInterface
{

    protected static $provider;

    public function __construct()
    {
        static::$provider = (new Provider())->load();
    }

    public static function init()
    {
        return new self();
    }

    /**
     * Initialize table for query
     *
     * @param string $table
     * @return new self()
     */
    public static function table(string $table)
    {
        static::init();
        return static::$provider->setTable($table);
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


    /**
     * Insert new record
     *
     * @param array $data
     * @return bool
     */
    public function insert(array $data)
    {
        return self::$provider->insert($data);
    }

    /**
     * Insert bulk record
     *
     * @param array $data
     * @return bool
     */
    public function insertBulk(array $data)
    {
        return self::$provider->insertBulk($data);
    }


    /**
     * Query based an a contion
     *
     * @param string $column
     * @param string $operand
     * @param string|null $value
     * 
     */
    public function where(string $column, string $operand, string $value = null)
    {
        return self::$provider->where($column, $operand, $value);
    }

    /**
     * Add another compulsory query condition
     *
     * @param string $column
     * @param string $operand
     * @param string|null $value
     * 
     */
    public function andWhere(string $column, string $operand, string $value = null)
    {
        return self::$provider->andWhere($column, $operand, $value);
    }

    /**
     * Add another optional query condition
     *
     * @param string $column
     * @param string $operand
     * @param string|null $value
     * 
     */
    public function orWhere(string $column, string $operand, string $value = null)
    {
        return self::$provider->orWhere($column, $operand, $value);
    }

    /**
     * Arrange data rows based on order
     *
     * @param string $column
     * @param string $order
     * 
     */
    public function orderBy(string $column, string $order = "asc")
    {
        return self::$provider->orderBy($column, $order);
    }

    /**
     * Enter raw sql query
     *
     * @param string $sqlQuery
     * @return mixed
     */
    public static function rawQuery(string $sqlQuery)
    {
        static::init();
        return self::$provider->rawQuery($sqlQuery);
    }

    /**
     * Select a query result limit
     *
     * @param integer $limit
     * 
     */
    public function take(int $limit)
    {
        return self::$provider->take($limit);
    }

    /**
     * count occurances
     *
     * @param string|null $column
     * @param string|null $value
     * @param string|null $operand
     */
    public function count(string $column = null, string $value = null, string $operand = null)
    {
        return self::$provider->count($column, $value, $operand);
    }


    /**
     * Get max value
     *
     * @param string $column
     */
    public function max(string $column)
    {
        return self::$provider->max($column);
    }

    /**
     * Get min value
     *
     * @param string $column
     */
    public function min(string $column)
    {
        return self::$provider->min($column);
    }


    /**
     * Update record
     *
     * @param array $data
     * @return bool|null
     */
    public function update(array $data)
    {
        return self::$provider->update($data);
    }

    /**
     * Delete record(s)
     *
     * @return bool|null
     */
    public function delete()
    {
        return self::$provider->delete();
    }


    /**
     * Begin DB Transaction
     *
     * 
     */
    public function begingTransaction()
    {
        self::$provider->begingTransaction();
    }

    /**
     * Commit DB Transaction
     *
     * 
     */
    public function commitTransaction()
    {
        self::$provider->commitTransaction();
    }

    /**
     * Rollback DB Transaction
     *
     * 
     */
    public function rollbackTransaction()
    {
        self::$provider->rollbackTransaction();
    }

    /**
     * Start DB Transaction Session
     *
     * 
     */
    public function transaction($transaction)
    {
        return self::$provider->transaction($transaction);
    }


    /**
     * Get a one to one relationship
     *
     * @param string $table
     * @param string $foreignKey
     * @param string $primaryKey
     */
    public function withOne(string $table, string $foreignKey, string $primaryKey = 'id')
    {
        return self::$provider->withOne($table, $foreignKey, $primaryKey);
    }


    /**
     * Get a one to many relationship
     *
     * @param string $table
     * @param string $foreignKey
     * @param string $primaryKey
     */
    public function withMany(string $table, string $foreignKey, string $primaryKey = 'id')
    {
        return self::$provider->withMany($table, $foreignKey, $primaryKey);
    }
}
