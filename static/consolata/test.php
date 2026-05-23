<?php
require "./includes/connect.php";
require "./includes/functions.php";

// $password="2025";
$dir="uploads/property-images/lower-mpingazi-1-4-acres-68c8c5c0e6008/";

$images=propertyImages($dir);

echo json_encode($images);