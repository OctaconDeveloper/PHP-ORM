<?php
declare(strict_types=1);

namespace PhpOrm\Interfaces;

interface QueryInterface
{
    public function insert(array $data);
    public function insertBulk(array $data);
    public function all();
    public function first();
    public function last(string $column);
    public function where(string $column, string $operand, string $value);
    public function andWhere(string $column, string $operand, string $value);
    public function orWhere(string $column, string $operand, string $value);
    public function orderBy(string $column, string $order);
    public function count(string $column, string $value, string $operand);
    public function max(string $column);
    public function min(string $column);
    public function update(array $data);
    public function delete();
    public function begingTransaction();
    public function commitTransaction();
    public function rollbackTransaction();
    public function transaction($transaction);
}
