<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

$dir="./model/org.json";

$response=new stdClass();

$response->message="Org Details";

$res=new stdClass();

$response->success=true;

$response->detail=OrgDetails($dir);

echo json_encode($response);

function OrgDetails($dir){
    $dets=file_get_contents($dir,true);

    return json_decode($dets,false);
}