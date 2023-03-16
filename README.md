
# PHP-ORM

This is a light weight ORM for core PHP applications that supports MySQL, Postgres and Sqlite database engine out of the box.


## Badges

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
[![GPLv3 License](https://img.shields.io/badge/License-GPL%20v3-yellow.svg)](https://opensource.org/licenses/)
[![AGPL License](https://img.shields.io/badge/license-AGPL-blue.svg)](http://www.gnu.org/licenses/agpl-3.0)


## Installation

Install PHP-ORM with composer

```bash
  composer require octacondeveloper/php-orm
```

## Env

```bash
  ## MYSQL Connection Example
    DB_CONNECTION=mysql
    DB_HOST=
    DB_PORT=
    DB_DATABASE=
    DB_USERNAME=
    DB_PASSWORD=


    # ## POSTGRES Connection Example
    DB_CONNECTION=postgresql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=
    DB_USERNAME=
    DB_PASSWORD=

    ## SQLite Connection Example
    DB_CONNECTION=sqlite
    DB_PATH="test_db_two.sqlite"
```

## Usage/Examples

```php
use PhpOrm\Query;

$result  = Query::table('users')->all();
```

Or You can use it as a Model Extension

```php
use PhpOrm\Model;

class User extends Model {

    //this is optional if your table name differs from the class name
    protected $table = 'users';
}
```

Then in your class you can call the User Class with the Query methods

```php
use `Your-class-namespace`

$result  = User::all();
```


## Documentation

PHP-ORM provides you with two distinct methods of interactions with methods. Which can be through

### Query
This options allow to specify the table name directly while making the calls and it has several methods. A quick example of it usage is

```
use PhpOrm\Query;

$result  = Query::table('users')->all();

```

#### Available methods
#### insert(array $data) 
Create a new record into the selected table
```
use PhpOrm\Query;

$data = [
    "name" => "Jil Harrys",
    "sex" => "Male"
];
$result  = Query::table('users')->insert($data);

```
#### insertBulk(array $data);
Insert bulk records at once
```
use PhpOrm\Query;

$data = [
    [
        "name" => "Jil Harrys",
        "sex" => "Male"
    ],
    [
        "name" => "Lionel Messi",
        "sex" => "Female"
    ],
    [
        "name" => "John Does",
        "sex" => "Male"
    ],
];
$result  = Query::table('users')->insertBulk($data);

```

#### all();
Fetch all records on the table
```
use PhpOrm\Query;

$result  = Query::table('users')->all();

```
#### first();

Fetch the first record on the table
```
use PhpOrm\Query;

$result  = Query::table('users')->first();

```
#### last(string $column);
Fetch the last record on the datable, If a column is not provided, it would order it based on the primaryKey or based on the first availablecolumn on the table
```
use PhpOrm\Query;

$result  = Query::table('users')->last();
$result  = Query::table('users')->last('updated_at');

```
#### rawQuery(string $sqlQuery);
This allows you to pass direct sql string to the Query instance
```
use PhpOrm\Query;

$result  = Query::rawQuery("SELECT * FROM users");
```

#### where(string $column, string $operand, string $value);
Add a query condition
```
use PhpOrm\Query;

$result  = Query::table('users')->where('sex','harrys');
$result  = Query::table('users')->where('sex',,'!=','harrys');

```
#### andWhere(string $column, string $operand, string $value);
Add another compulsory query condition, this comes after an initial "where" method has been called
```
use PhpOrm\Query;

$result  = Query::table('users')->andWhere('sex','harrys');
$result  = Query::table('users')->andWhere('sex',,'!=','harrys');
```

#### orWhere(string $column, string $operand, string $value);
Add another optional query condition, this comes after an initial "where" method has been called
```
use PhpOrm\Query;

$result  = Query::table('users')->orWhere('sex','harrys');
$result  = Query::table('users')->orWhere('sex',,'!=','harrys');
```
#### orderBy(string $column, string $order);
Order records based on column either in ASC or DESC. Default order is ASC
```
use PhpOrm\Query;

$result  = Query::table('users')->orderBy('id','desc');
$result  = Query::table('users')->orderBy('id');
```

#### count(string $column = null, string $value, string $operand);
Counts the number of records available on the table based on provided column and condition. If no parameters are passed, it count count the total records without any condition
```
use PhpOrm\Query;

$result  = Query::table('users')->count();
$result  = Query::table('users')->count('sex','male');
```
#### max(string $column);
Get the maximum
```
use PhpOrm\Query;

$result  = Query::table('users')->min('score');
```

#### min(string $column);
Get the minimum
```
use PhpOrm\Query;

$result  = Query::table('users')->min('score');
```
#### update(array $data);
Update a record
```
use PhpOrm\Query;

$result  = Query::table('users')->where('id','233')->update([
    "sex" => "female"
]);
```

#### delete();
Delete a record
```
use PhpOrm\Query;

$result  = Query::table('users')->where('id','233')->delete();

$result  = Query::table('users')->delete();
```
#### begingTransaction();

Begin a transaction
```
use PhpOrm\Query;


$result  = Query::table('users')->begingTransaction();
```
#### commitTransaction();

Commit a transaction
```
use PhpOrm\Query;

$result  = Query::table('users')->commitTransaction();
```
#### rollbackTransaction();

Rollback a transaction
```
use PhpOrm\Query;

$result  = Query::table('users')->rollbackTransaction();
```
#### transaction($transaction);

Start a DB trabsaction session
```
use PhpOrm\Query;

$result  = Query::table('users')->transaction(function(){
    $result  = Query::table('users')->where('id','233')->delete();

$result  = Query::table('users')->delete();
});
```
#### withOne(string $table, string $foreignKey, string $primaryKey = 'id')

Fetch a one to one model relationship between two models
```
use PhpOrm\Query;

$result  = Query::table('users')->withOne('files','user_id','id');
```

#### withMany(string $table, string $foreignKey, string $primaryKey = 'id')

Fetch a one to many model relationship between two models
```
use PhpOrm\Query;

$result  = Query::table('users')->withOne('files','user_id','id');
```


### Model
The Model approach allows you to create a class for your table and extends the model base class. This allows you to have other custom SQL related transaction in your class. It can used as follows:
```
use PhpOrm\Model;
class User extends Model{
    protected $table= 'users';
}
```
Defining the table variable is optional. By default the Package gets your class name and assumes the corresponding table name to it. Take for example
```
If the class name is "User", the assumption is that the table name is "users", same for "Student" and "students". But if this assumption is wrong, kindly set the value of the protected $table to override the default table name assumptions.

```

#### Available methods
#### insert(array $data) 
Create a new record into the selected table
```


$data = [
    "name" => "Jil Harrys",
    "sex" => "Male"
];
$result  = User::insert($data);

```
#### insertBulk(array $data);
Insert bulk records at once
```


$data = [
    [
        "name" => "Jil Harrys",
        "sex" => "Male"
    ],
    [
        "name" => "Lionel Messi",
        "sex" => "Female"
    ],
    [
        "name" => "John Does",
        "sex" => "Male"
    ],
];
$result  = User::insertBulk($data);

```

#### all();
Fetch all records on the table
```


$result  = User::all();

```
#### first();

Fetch the first record on the table
```


$result  = User::first();

```
#### last(string $column);
Fetch the last record on the datable, If a column is not provided, it would order it based on the primaryKey or based on the first availablecolumn on the table
```


$result  = User::last();
$result  = User::last('updated_at');

```
#### where(string $column, string $operand, string $value);
Add a query condition
```


$result  = User::where('sex','harrys');
$result  = User::where('sex',,'!=','harrys');

```
#### andWhere(string $column, string $operand, string $value);
Add another compulsory query condition, this comes after an initial "where" method has been called
```


$result  = User::andWhere('sex','harrys');
$result  = User::andWhere('sex',,'!=','harrys');
```

#### orWhere(string $column, string $operand, string $value);
Add another optional query condition, this comes after an initial "where" method has been called
```


$result  = User::orWhere('sex','harrys');
$result  = User::orWhere('sex',,'!=','harrys');
```
#### orderBy(string $column, string $order);
Order records based on column either in ASC or DESC. Default order is ASC
```


$result  = User::orderBy('id','desc');
$result  = User::orderBy('id');
```

#### count(string $column = null, string $value, string $operand);
Counts the number of records available on the table based on provided column and condition. If no parameters are passed, it count count the total records without any condition
```


$result  = User::count();
$result  = User::count('sex','male');
```
#### max(string $column);
Get the maximum
```


$result  = User::min('score');
```

#### min(string $column);
Get the minimum
```


$result  = User::min('score');
```
#### update(array $data);
Update a record
```


$result  = User::where('id','233')->update([
    "sex" => "female"
]);
```

#### delete();
Delete a record
```


$result  = User::where('id','233')->delete();

$result  = User::delete();
```

#### withOne(string $table, string $foreignKey, string $primaryKey = 'id')

Fetch a one to one model relationship between two models
```


$result  = User::withOne('files','user_id','id')->first();
$result  = User::withOne('files','user_id','id')->all();
```

#### withMany(string $table, string $foreignKey, string $primaryKey = 'id')

Fetch a one to many model relationship between two models
```


$result  = User::withOne('files','user_id','id')->first();
$result  = User::withOne('files','user_id','id')->all();
```

### Method Chaining
The package allows method chaining as most of the methods wont return a result until it's chained to certian methods.

```
use PhpOrm\Query;

$query = Query::table('cryptos')->where("name","Harrys")->orWhere("symbol","ETH")->orderBy("name", "asc")->take(2)->min("exchange_rate_dollar");


$query = Query::table('cryptos')->where("name","Harrys")->orWhere("symbol","ETH")->orderBy("name", "asc")->take(2)->first();


$query = Crypto::where("name","Harrys")->orWhere("symbol","ETH")->orderBy("name", "asc")->take(2)->min("exchange_rate_dollar");


$query = Crypto::where("name","Harrys")->orWhere("symbol","ETH")->orderBy("name", "asc")->take(2)->first();
```
## Authors

- [@octaconDeveloper](https://github.com/octacondeveloper)


## Contributing

Contributions are always welcome! This is an Open Source library and Pull Requests would be extremely welcome.


