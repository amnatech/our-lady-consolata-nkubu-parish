<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// users interface

// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="Users";

$response->detail=PropertiesInterface($con,$REQ_OBJ);

echo json_encode($response);


function PropertiesInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["property"])){
               return GetProperty($con,$req->query_params["property"]);

            }

            return GetProperties($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            //check action
            $action=$rp['action'];

            $name=$rp['name'];

            $property_id=$rp['property_id'];

            $deleted_by=$rp['deleted_by'];

            if($action=="delete"){
                return DeleteProperty($con,$property_id,$name,$deleted_by);
            }

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
            return GetProperties($con);
            break;
    }

}



// gets users
// returns a list of users
function GetProperties($con,$options=null){

    $condition="WHERE deleted_at IS NULL";

    $properties=GetResource($con,"properties","*",$condition);

    $properties=PropertyMainImage($properties);

    $properties=PropertyUnitPrices($con,$properties);

    return $properties;
    
}

//takes in a slug and returns a user object or null
function GetProperty($con,$slug){

    $condition="WHERE slug='$slug'";

    $prop=new stdClass();

    $props=GetResource($con,"properties","*",$condition);

    // make property main image from images 
    
    if(isset($props[0])){

        // add featured image 
        $props[0]['featured_image']=featuredImage($props[0]['images_uri']);
        $props[0]['images']=propertyImages($props[0]['images_uri']);

        $props[0]['similar_properties']=similarProperties($con,$slug);
        $props[0]['units']=PropertyUnits($con,$props[0]['property_id']);
        $props[0]['prices']=PropertyPrices($props[0]['units']);



        $prop->ok=true;
        $prop->data=$props[0];
        $prop->message="property";

        return($prop);
    }

    $prop->ok=false;

    $prop->message="no such property exists";

    return $prop;
}


// checks if a user exists 
function PropertyExists($con,$name){

    $sql="SELECT * FROM properties WHERE name='$name'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}




function DeleteProperty($con,$property_id,$name,$deleted_by){

    $res=new stdClass();

    $now=date("Y-m-d H:m:s");

    $update="UPDATE properties SET deleted_at='$now',deleted_by='$deleted_by' WHERE property_id='$property_id'";

    if(mysqli_query($con,$update)){

        $res->success=true;

        $res->message="Property ".$name." deleted";
    }else{
        $res->success=false;
        $res->message=mysqli_error($con);
    }

    return $res;
}