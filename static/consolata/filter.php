<?php
header("Access-Control-Allow-Origin: *");

require './includes/connect.php';
require './includes/functions.php';
require "./middleware/methods.php";

$result=new stdClass();

$result->message="Filter Search";


$REQ_OBJ=RequestAdapter($_SERVER);

$params=$REQ_OBJ->query_params;

$condition="";

foreach ($params as $key => $value) {
    # code...
    if($key!=="min_price" & $key!=="max_price"){
        $condition=$condition.$key."="."'$value'"." AND ";
    }
}

$clean_condition=substr($condition,0,-4);


$properties=[];

// make sql 
$sel="SELECT * FROM properties WHERE $clean_condition";

$query=mysqli_query($con,$sel);

while($p=mysqli_fetch_assoc($query)){

        $p['featured_image']=featuredImage($p['images_uri']);
        $p['images']=propertyImages($p['images_uri']);

        $p['similar_properties']=similarProperties($con,$p['slug']);
        $p['units']=PropertyUnits($con,$p['property_id']);
        $p['prices']=PropertyPrices($p['units']);
    array_push($properties,$p);
}

$result->detail=$properties;

echo json_encode($result);




