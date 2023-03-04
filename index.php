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
// // $query = Query::table('cryptos')::first();
// // $query = Query::table('cryptos')->last('name');
// $query = Query::rawQuery("SELECT * FROM `cryptos` WHERE `symbol` = 'BTC'");
// $dataToBeInserted = [
//     [
//     "name" => "Harrys",
//     "symbol" => "Harrys",
//     "iconpath" => "Harrys",
//     "filepath" => "local.kj.jpeg",
//     "address" => "Ikotun",
//     "exchange_rate_dollar" => "20",
//     ],
//     [
//         "name" => "Jil",
//         "symbol" => "Jil",
//         "iconpath" => "Jil",
//         "filepath" => "local.kj.jpeg",
//         "address" => "Ikotun",
//         "exchange_rate_dollar" => "20",
//     ],
//     [
//         "name" => "Harrys",
//         "symbol" => "Harrys",
//         "iconpath" => "Harrys",
//         "filepath" => "local.kj.jpeg",
//         "address" => "Ikotun",
//         "exchange_rate_dollar" => "20",
//     ]
//     ];
// $query = Query::table('cryptos')::insertBulk($dataToBeInserted);
// $query = Query::table('cryptos')->where("name","Harrys")->orWhere("symbol","ETH")->orderBy("name", "asc")->take(2)->min("exchange_rate_dollar");
// $query = Query::table('cryptos')->where("name","Harrys")->orWhere("symbol","Bitcoin")->update([
//     "exchange_rate_dollar" => 30,
//     "address" => "Lagos"
// ]);

// $query = Query::table('cryptos')->count();
// $query = Query::table('cryptos')->count('symbol', 'BTC');
// $query = Query::table('cryptos')->min("amount");
// $query = Query::table('cryptos')->max("amount");
// $query = Query::table('cryptos')->oderBy("name", "desc");
// $query = Query::table('cryptos')->take("6");

// $query = Query::table('cryptos')->insertBulk($dataToBeInserted);

// $data = [
//     "name" => "Harrys",
//     "symbol" => "Harrys",
//     "iconpath" => "Harrys",
//     "filepath" => "local.kj.jpeg",
//     "address" => "Ikotun",
//     "exchange_rate_dollar" => "20",
// ];
// $query = Query::table('cryptos')->insert($data);


// $query = Query::table('levels')->where('name','Test')->delete();

// $query = Query::table('levels'); //->first();
// $query = Query::rawQuery("CREATE TABLE boy34 (
//     c1 INT,
//     c2 VARCHAR(10)
//   );");

  $data = [
    [
    "c1" => 200,
    "c2" => "Harrys"
    ],
    [
    "c1" => 400,
    "c2" => "Boy"
    ],
    [
    "c1" => 600,
    "c2" => "Girl"
    ],
];
$query = Query::table('boy34')->last();


var_dump($query);
