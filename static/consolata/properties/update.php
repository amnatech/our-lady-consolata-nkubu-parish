<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST,PUT');
header("Access-Control-Allow-Headers: *");

require "../includes/connect.php";
require "../includes/functions.php";

$res = new stdClass();

// $files ;
$name = addslashes($_POST['name']);

$town = addslashes($_POST['town']);

$suburb = addslashes($_POST['suburb']);

$address = addslashes($_POST['address']);

$location = addslashes($_POST['area']);

$category = addslashes($_POST['category']);

$area = addslashes($_POST['area']);

$amenities=addslashes($_POST['amenities']);

$nearby=addslashes($_POST['nearby']);

$description=addslashes($_POST['description']);

$county=GetCounty($con,$town);

$updated_by=addslashes($_POST['updated_by']);


$access_type = $_POST['access_type'];
$use_type = $_POST['use_type'];

$status = 'active';

$property_id = $_POST['property_id'];

$update="UPDATE properties SET name='$name',county='$county',town='$town',suburb='$suburb',area='$area',address='$address',description='$description',
        category='$category',use_type='$use_type',access_type='$access_type',amenities='$amenities',nearby='$nearby' WHERE property_id='$property_id'";

if (mysqli_query($con, $update)) {

    $res->success = true;

    $res->message = "Property Updated Successfully";
} else {
    $res->success = false;

    $res->message = mysqli_error($con);
}

$response=new stdClass();

$response->message="Update Property";

$response->detail=$res;

echo json_encode($response);


function GetCounty($con,$town){
    $type="town";
    $sql="SELECT * FROM locations WHERE name='$town' AND type='$type'";

    $query=mysqli_query($con,$sql);

    if($t=mysqli_fetch_assoc($query)){
        return $t['parent'];
    }else{
        return "";
    }
}