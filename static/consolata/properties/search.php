<?php
header("Access-Control-Allow-Origin: *");

require '../includes/connect.php';
require '../includes/functions.php';


$results=[];

$q = $_POST['q'];

$sel = "SELECT * FROM properties WHERE name LIKE '%$q%' OR category LIKE '%$q%' OR county LIKE '%$q%' LIMIT 5";
// query
$sel_query = mysqli_query($con, $sel);

while ($row = mysqli_fetch_assoc($sel_query)) {

    $p = new stdClass();

    $p->id = $row['id'];

    $p->name = $row['name'];

    $p->category = $row['category'];

    $p->slug=$row['slug'];

    $units=PropertyUnits($con,$row['property_id']);

    $p->units=sizeof($units);

    $p->prices=PropertyPrices($units);

    array_push($results, $p);
    
}

    
echo json_encode($results);



