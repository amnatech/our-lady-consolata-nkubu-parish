<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// Amenities interface

// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="Amenities";

$response->detail=AmenitiesInterface($con,$REQ_OBJ);

echo json_encode($response);


function AmenitiesInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["amenity"])){
               return GetAmenity($con,$req->query_params["amenity"]);

            }

            return GetAmenities($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            $name=$rp["name"];

            return AddAmenity($con,$name);

            # code...
            break;

        case 'PUT':
            # code...
            break;

        case 'DELETE':
            # code...
            break;

        default:
            # code...
            return GetAmenities($con);
            break;
    }

}

//creates a amenity
//returns and object type success/error
function AddAmenity($con,$name){

    $res=new stdClass();

    if(AmenityExists($con,$name)){
        $res->success=false;
        $res->message="amenity already exists";

        return $res;
    }

    $resource_id=uniqid();

    $slug=slugify($name);

    $status="active";

    $cols="amenity_id,name,slug";

    $values="'$resource_id','$name','$slug'";

    $insert=InsertResource($con,"amenities",$cols,$values);

    return $insert;

}

// gets Amenities
// returns a list of Amenities
function GetAmenities($con,$options=null){

    $Amenities=GetResource($con,"amenities","*");

    return $Amenities;
    
}

//takes in a slug and returns an amenitiu object or null
function GetAmenity($con,$slug){

    $condition="WHERE slug='$slug'";

    $cat=new stdClass();

    $Amenities=GetResource($con,"amenities","*",$condition);

    if(isset($Amenities[0])){

        $cat->ok=true;
        $cat->data=$Amenities[0];
        $cat->message="amenity";

        return($cat);
    }

    $cat->ok=false;

    $cat->message="no such amenity exists";

    return $cat;
}


// checks if a cat exists 
function AmenityExists($con,$name){

    $sql="SELECT * FROM amenities WHERE name='$name'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}

