<?php

function getMysqlColumns(array $data)
{
    $cols = array_keys($data);
    $columns = "";
    foreach ($cols as $col) {
        $columns .= "{$col},";
    }

    return substr($columns, 0, -1);
}

function getMysqlColumnsAttribute(array $data)
{
    $cols = array_keys($data);
    $columns = "";
    foreach ($cols as $col) {
        $columns .= ":{$col},";
    }

    return substr($columns, 0, -1);
}

function getMysqlUpdateAttribute(array $data)
{
    $cols = array_keys($data);
    $columns = "";
    foreach ($cols as $col) {
        $columns .= "`$col`" . " = :{$col},";
    }

    return substr($columns, 0, -1);
}


function handleMysqlError($error_message)
{
    $debug = debug_backtrace();
    $count = count($debug);
    $errorPayload = [
        "message" => $error_message,
        "trace" => $debug[$count - 1]['file'] . ' on line ' . $debug[$count - 1]['line'],
    ];
    return json_encode($errorPayload);
}
