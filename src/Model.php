<?php

declare(strict_types=1);

namespace PhpOrm;

use PhpOrm\Query;
use PhpOrm\Utils\Inflect;

class Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;


    /**
     * Instance of the Query class
     *
     * @var Query
     */
    private $query;




    function __construct()
    {
        $this->getTableName();
        $this->query = Query::table($this->table);
    }


    /**
     * Set table name for Model Instance
     *
     * @return string
     */
    private function getTableName(): Model
    {
        $this->table =  $this->table ?? Inflect::pluralize(ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', get_class($this))), '_'));
        return $this;
    }


    /**
     * Returns all available records in a table
     *
     * @return array
     */
    public static function all()
    {
        $self = new static;
        return $self->query->all();
    }


    /**
     * Get first record in a table
     *
     * @return array
     */
    public static function first()
    {
        $self = new static;
        return $self->query->first();
    }

    /**
     * Get last record in a table
     *
     * @return array
     */
    public static function last(string $column = 'id')
    {
        $self = new static;
        return $self->query->last($column);
    }

    /**
     * Insert new record
     *
     * @param array $data
     * @return bool
     */
    public static function insert(array $data)
    {
        $self = new static;
        return $self->query->insert($data);
    }

    /**
     * Insert bulk record
     *
     * @param array $data
     * @return bool
     */
    public static function insertBulk(array $data)
    {
        $self = new static;
        return $self->query->insertBulk($data);
    }

    /**
     * Query based an a contion
     *
     * @param string $column
     * @param string $operand
     * @param string|null $value
     * 
     */
    public static function where(string $column, string $operand, string $value = null)
    {
        $self = new static;
        return $self->query->where($column, $operand, $value);
    }

    /**
     * Add another compulsory query condition
     *
     * @param string $column
     * @param string $operand
     * @param string|null $value
     * 
     */
    public static function andWhere(string $column, string $operand, string $value = null)
    {
        $self = new static;
        return $self->query->andWhere($column, $operand, $value);
    }

    /**
     * Add another optional query condition
     *
     * @param string $column
     * @param string $operand
     * @param string|null $value
     * 
     */
    public static function orWhere(string $column, string $operand, string $value = null)
    {
        $self = new static;
        return $self->query->orWhere($column, $operand, $value);
    }


    /**
     * Arrange data rows based on order
     *
     * @param string $column
     * @param string $order
     * 
     */
    public static function orderBy(string $column, string $order = "asc")
    {
        $self = new static;
        return $self->query->orderBy($column, $order);
    }


    /**
     * Select a query result limit
     *
     * @param integer $limit
     * 
     */
    public static function take(int $limit)
    {
        $self = new static;
        return $self->query->take($limit);
    }

    /**
     * count occurances
     *
     * @param string|null $column
     * @param string|null $value
     * @param string|null $operand
     */
    public static function count(string $column = null, string $value = null, string $operand = null)
    {
        $self = new static;
        return $self->query->count($column, $value, $operand);
    }

    /**
     * Get max value
     *
     * @param string $column
     */
    public static function max(string $column)
    {
        $self = new static;
        return $self->query->max($column);
    }


    /**
     * Get min value
     *
     * @param string $column
     */
    public static function min(string $column)
    {
        $self = new static;
        return $self->query->min($column);
    }

    /**
     * Update record
     *
     * @param array $data
     * @return bool|null
     */
    public static function update(array $data)
    {
        $self = new static;
        return $self->query->update($data);
    }

    /**
     * Delete record(s)
     *
     * @return bool|null
     */
    public static function delete()
    {
        $self = new static;
        return $self->query->delete();
    }

    /**
     * Get a one to one relationship
     *
     * @param string $table
     * @param string $foreignKey
     * @param string $primaryKey
     * 
     */
    public static function withOne(string $table, string $foreignKey, string $primaryKey = 'id')
    {
        $self = new static;
        return $self->query->withOne($table, $foreignKey, $primaryKey);
    }

    /**
     * Get a one to many relationship
     *
     * @param string $table
     * @param string $foreignKey
     * @param string $primaryKey
     * 
     */
    public static function withMany(string $table, string $foreignKey, string $primaryKey = 'id')
    {
        $self = new static;
        return $self->query->withMany($table, $foreignKey, $primaryKey);
    }
}
