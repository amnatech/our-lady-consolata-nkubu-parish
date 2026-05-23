<?php

require "../includes/connect.php";
require "../includes/functions.php";
require "../middleware/methods.php";

// Units interface

// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="Units";

$response->detail=UnitsInterface($con,$REQ_OBJ);

echo json_encode($response);


function UnitsInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["unit"])){
               return GetUnit($con,$req->query_params["unit"]);

            }

            return GetUnits($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            $action=$rp['action'];

            $units_id=$rp["units_id"];

            $name=$rp["name"];


            // keep delete on to as the other fields wont be set
            // on delete
            if($action=="delete"){

                $deleted_by=$rp['deleted_by'];

                return DeleteUnit($con,$units_id,$name,$deleted_by);

            }


            $size=$rp["size"];

            $price=$rp["price"];

            $size_units=$rp["size_unit"];

            $property_id=$rp["property_id"];

            $qty=$rp["qty"];



            if($action=="update"){

                return UpdateUnit($con,$name,$size,$size_units,$price,$qty,$property_id,$units_id);

            }

            return AddUnit($con,$name,$size,$size_units,$price,$qty,$property_id,$units_id);

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
            return GetUnits($con);
            break;
    }

}

//creates a amenity
//returns and object type success/error
function AddUnit($con,$name,$size,$size_units,$price,$qty,$property_id,$units_id){

    $res=new stdClass();

    if(UnitExists($con,$name)){
        $res->success=false;
        $res->message="Unit already exists";

        return $res;
    }


    $slug=slugify($name);

    $status="active";

    $cols="property_id,unit_id,name,size,size_unit,price,qty,slug,status";

    $values="'$property_id','$units_id','$name','$size','$size_units','$price','$qty','$slug','$status'";

    $insert=InsertResource($con,"units",$cols,$values);

    return $insert;

}

function UpdateUnit($con,$name,$size,$size_units,$price,$qty,$property_id,$units_id){

    $res=new stdClass();

    $update="UPDATE units SET name='$name',size='$size',size_unit='$size_units',price='$price',qty='$qty' WHERE unit_id='$units_id' AND property_id='$property_id'";

    if(mysqli_query($con,$update)){

        $res->success=true;
        $res->message="Unit updated successfully";
    }else{
        $res->success=false;
        $res->message=mysqli_error($con);
    }

    return $res;

}

// gets Units
// returns a list of Units
function GetUnits($con,$options=null){

    $Units=GetResource($con,"units","*");

    return $Units;
    
}

//takes in a slug and returns an amenitiu object or null
function GetUnit($con,$slug){

    $condition="WHERE slug='$slug'";

    $cat=new stdClass();

    $Units=GetResource($con,"units","*",$condition);

    if(isset($Units[0])){

        $cat->ok=true;
        $cat->data=$Units[0];
        $cat->message="unit";

        return($cat);
    }

    $cat->ok=false;

    $cat->message="no such unit exists";

    return $cat;
}

function DeleteUnit($con,$units_id,$name,$deleted_by){

    $res=new stdClass();
    $delete="DELETE FROM units WHERE unit_id='$units_id'";

    if(mysqli_query($con,$delete)){

        $res->success=true;

        $res->message="Unit ".$name." deleted";
    }else{
        $res->success=false;
        $res->message=mysqli_error($con);
    }

    return $res;
}

// checks if a cat exists 
function UnitExists($con,$name){

    $sql="SELECT * FROM units WHERE name='$name'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}

