<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// accommodations interface

// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="accommodations";

$response->detail=accommodationsInterface($con,$REQ_OBJ);

echo json_encode($response);


function accommodationsInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["accommodation"])){
               return GetAccommodation($con,$req->query_params["accommodation"]);

            }

            return GetAccommodations($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            $action=$rp['action'];

            $accommodation_id=$rp['accommodation_id'];

            $title=$rp['title'];

        

            if($action=="update"){

                $subtitle=$rp['subtitle'];

                $description=$rp['description'];

                $price=$rp['price'];

               return UpdateAccommodation($con,$accommodation_id,$title,$subtitle,$description,$price);
            }

            if($action=="delete"){

                $deleted_by=$rp["deleted_by"];
                return DeleteAccommodation($con,$accommodation_id,$title,$deleted_by);
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
            return GetAccommodations($con);
            break;
    }

}



// gets accommodations
// returns a list of accommodations
function GetAccommodations($con,$options=null){

    $condition="WHERE deleted_at IS NULL";

    $accommodations=GetResource($con,"accommodations","*",$condition);

    $Acts=[];

    foreach ($accommodations as $key => $accommodation) {
        # code...
       $accommodation['featured_image']=featuredImage($accommodation['images_uri']);
        $accommodation['images']=propertyImages($accommodation['images_uri']);

        array_push($Acts,$accommodation);
    }

    return $Acts;
    
}

//takes in a slug and returns an amenitiu object or null
function GetAccommodation($con,$slug){

    $condition="WHERE slug='$slug'";

    $cat=new stdClass();

    $accommodations=GetResource($con,"accommodations","*",$condition);

    if(isset($accommodations[0])){

        $cat->ok=true;

        $accommodations[0]['featured_image']=featuredImage($accommodations[0]['images_uri']);
        $accommodations[0]['images']=propertyImages($accommodations[0]['images_uri']);

        $cat->data=$accommodations[0];

        
        $cat->message="accommodation";

        return($cat);
    }

    $cat->ok=false;

    $cat->message="no such accommodation exists";

    return $cat;
}


// checks if a cat exists 
function accommodationExists($con,$title){

    $sql="SELECT * FROM accommodations WHERE title='$title'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}

function UpdateAccommodation($con,$accommodation_id,$title,$subtitle,$description,$price){
    $res=new stdClass();

       // insert 
    $update = "UPDATE  accommodations SET title='$title',subtitle='$subtitle',description='$description',price='$price' WHERE accommodation_id='$accommodation_id'";


    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = "Accommodation Updated Successfully";
    } else {
        $res->success = false;

        $res->message = mysqli_error($con);
    }


    return $res;
}



function DeleteAccommodation($con,$accommodation_id,$title,$deleted_by){

    $res=new stdClass();

    $now=date("Y-m-d H:m:s");

    $update="UPDATE accommodations SET deleted_at='$now',deleted_by='$deleted_by' WHERE accommodation_id='$accommodation_id'";

    if(mysqli_query($con,$update)){

        $res->success=true;

        $res->message="accommodation ".$title." deleted";
    }else{
        $res->success=false;
        $res->message=mysqli_error($con);
    }

    return $res;
}