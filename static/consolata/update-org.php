<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST,PUT');
header("Access-Control-Allow-Headers: *");

require "./includes/connect.php";
require "./includes/functions.php";

$res = new stdClass();

$org = file_get_contents('php://input');

$dir="./model/org.json";


if(file_put_contents($dir,$org)){
    $res->success=true;
    $res->message="Organisation Updated Successfully";
}else{
    $res->success=false;

    $res->message="There was an error updating details. Try again later";
}

$result=new stdClass();

$result->message="Organisation";

$result->detail=$res;

echo json_encode($result);