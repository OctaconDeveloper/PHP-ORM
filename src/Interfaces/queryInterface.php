<?php

namespace PhpOrm\Interfaces;

interface QueryInterface
{
    function all();
    // function all(string $table);
    function first();
    function last();
    function insert(array $data);
    function insertBulk(array $data);

    function begingTransaction();
    function commitTransaction();
    function rollbackTransaction();
    function transaction($transaction);
    // public static function delete();
    // public static function deleteAll();
    // public static function updateOne();
    // public static function updateAll();
    // public static function where();
    // public static function whereFirst();
    // public static function whereLast();
    // public static function whereAll();
    // public static function with();
}
