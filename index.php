<?php

require("vendor/autoload.php");


use PhpOrm\Providers\Mysql\MysqlConnection;
use PhpOrm\Providers\Mysql\MysqlProvider;
use PhpOrm\Query;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// new MongoDB\Client($this->databaseUri);


// // $connection = new MysqlConnection();
// // $connection = $connection->connection();

// // var_dump($connection);

// $res = (new MysqlProvider())->all('cryptos');







// $query = Query::table('cryptos');
// $query = Query::table('cryptos')::all();
// $query = Query::table('cryptos')::first();
// $query = Query::table('cryptos')->last('name');
$dataToBeInserted = [
    [
    "name" => "Harrys",
    "symbol" => "Harrys",
    "iconpath" => "Harrys",
    "filepath" => "local.kj.jpeg",
    "address" => "Ikotun",
    "exchange_rate_dollar" => "20",
    ],
    [
        "name" => "Jil",
        "symbol" => "Jil",
        "iconpath" => "Jil",
        "filepath" => "local.kj.jpeg",
        "address" => "Ikotun",
        "exchange_rate_dollar" => "20",
    ],
    [
        "name" => "Harrys",
        "symbol" => "Harrys",
        "iconpath" => "Harrys",
        "filepath" => "local.kj.jpeg",
        "address" => "Ikotun",
        "exchange_rate_dollar" => "20",
    ]
    ];
// $query = Query::table('cryptos')::insertBulk($dataToBeInserted);
// $query = Query::table('cryptos')->where("name","Harrys")->orWhere("symbol","ETH")->orderBy("name", "asc")->take(2)->min("exchange_rate_dollar");
$query = Query::table('cryptos')->where("name","Harrys")->orWhere("symbol","Bitcoin")->update([
    "exchange_rate_dollar" => 30,
    "address" => "Lagos"
]);

var_dump($query);
