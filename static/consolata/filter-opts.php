<?php
header("Access-Control-Allow-Origin: *");

require './includes/connect.php';
require './includes/functions.php';
require "./middleware/methods.php";

$result=new stdClass();

$result->message="Filter Opts";

$res=new stdClass();

$res->categories=GetResource($con,"properties","category");

$res->towns=GetResource($con,"properties","town");


$result->detail=$res;

echo json_encode($result);
