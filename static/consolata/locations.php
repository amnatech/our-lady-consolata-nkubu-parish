<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// Locations interface

// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="Locations";

$response->detail=LocationsInterface($con,$REQ_OBJ);

echo json_encode($response);


function LocationsInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["location"])){
               return GetLocation($con,$req->query_params["location"]);

            }

            // if filter type exists 
            if(isset($req->query_params["type"])){
               return FilterLocations($con,'type',$req->query_params["type"]);
            }

            return GetLocations($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            $name=$rp["name"];

            $type=$rp['type'];

            $created_by=$rp['created_by'];

            // ifparent is set 
              if(isset($rp['parent'])){
               
                $parent=$rp['parent'];

            }else{
                $parent=$name;

            }


            // check if lat , long are set
            if(isset($rp['latitude'])){
                $latitude=$rp['latitude'];
            }else{
                $latitude=-1.286021;
            }

            if(isset($rp['longitude'])){
                $longitude=$rp['longitude'];
            }else{
                $longitude=36.816410;
            }


            return AddLocation($con,$name,$type,$parent,$latitude,$longitude,$created_by);

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
            return GetLocations($con);
            break;
    }

}

//creates a location
//returns and object type success/error
function AddLocation($con,$name,$type,$parent,$latitude,$longitude,$created_by){

    $res=new stdClass();

    $filter="WHERE name='$name' AND type='$type'";
    if(LocationExists($con,$filter)){
        $res->success=false;
        $res->message="location already exists";

        return $res;
    }

    $resource_id=uniqid();

    $slug=slugify($name);

    $status="active";

    $cols="location_id,name,type,parent,latitude,longitude,created_by,slug";

    $values="'$resource_id','$name','$type','$parent','$latitude','$longitude','$created_by','$slug'";

    $insert=InsertResource($con,"locations",$cols,$values);

    return $insert;

}

// gets Locations
// returns a list of Locations
function GetLocations($con,$options=null){

    $Locations=GetResource($con,"locations","*");

    return $Locations;
    
}

//takes in a slug and returns an amenitiu object or null
function GetLocation($con,$slug){

    $condition="WHERE slug='$slug'";

    $cat=new stdClass();

    $Locations=GetResource($con,"locations","*",$condition);

    if(isset($Locations[0])){

        $cat->ok=true;
        $cat->data=$Locations[0];
        $cat->message="location";

        return($cat);
    }

    $cat->ok=false;

    $cat->message="no such location exists";

    return $cat;
}

function FilterLocations($con,$filter_prop,$filter_val){

    $condition="WHERE $filter_prop='$filter_val'";
  
    return GetResource($con,"locations","*",$condition);
}


// checks if a cat exists 
function LocationExists($con,$filter){

    $sql="SELECT * FROM locations $filter";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}

// function add town
function AddTown($con,$county,$name){
    
}