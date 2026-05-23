<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// NewsAndEvents interface
// @@ NORE (News OR Event)
// @@ 
// @@ 


// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="News And Events";

$response->detail=NewsAndEventsInterface($con,$REQ_OBJ);

echo json_encode($response);


function NewsAndEventsInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["noe"])){
               return GetNoe($con,$req->query_params["noe"]);

            }

            return GetNoes($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            $action=$rp['action'];

            $noe_id=$rp['noe_id'];

            $title=$rp['title'];

            if($action=="delete"){

                $deleted_by=$rp['deleted_by'];

               return DeleteNoe($con,$noe_id,$title,$deleted_by);

            }


            $subtitle=$rp['subtitle'];

            $description=$rp['description'];

            $venue=$rp['venue'];

            $duration=$rp["duration"];


            if($action=="update"){
               return UpdateNoe($con,$noe_id,$title,$subtitle,$description,$venue,$duration);
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
            return GetNoes($con);
            break;
    }

}



// gets NewsAndEvents
// returns a list of NewsAndEvents
function GetNoes($con,$options=null){

    $condition="WHERE deleted_at IS NULL";

    $Noes=GetResource($con,"news_and_events","*",$condition);

    $Acts=[];

    foreach ($Noes as $key => $noe) {
        # code...
       $noe['featured_image']=featuredImage($noe['images_uri']);
        $noe['images']=propertyImages($noe['images_uri']);

        array_push($Acts,$noe);
    }

    return $Acts;
    
}

//takes in a slug and returns an amenitiu object or null
function GetNoe($con,$slug){

    $condition="WHERE slug='$slug'";

    $cat=new stdClass();

    $Noes=GetResource($con,"news_and_events","*",$condition);

    if(isset($Noes[0])){

        $cat->ok=true;

        $Noes[0]['featured_image']=featuredImage($Noes[0]['images_uri']);
        $Noes[0]['images']=propertyImages($Noes[0]['images_uri']);

        $cat->data=$Noes[0];

        
        $cat->message="NOE";

        return($cat);
    }

    $cat->ok=false;

    $cat->message="no such NOE exists";

    return $cat;
}


// checks if a cat exists 
function noeExists($con,$title){

    $sql="SELECT * FROM news_and_events WHERE title='$title'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}

function UpdateNoe($con,$noe_id,$title,$subtitle,$description,$venue,$duration){
    $res=new stdClass();

       // insert 
    $update = "UPDATE  news_and_events SET title='$title',subtitle='$subtitle',description='$description',venue='$venue',duration='$duration' WHERE noe_id='$noe_id'";


    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = "Noe Updated Successfully";
    } else {
        $res->success = false;

        $res->message = mysqli_error($con);
    }


    return $res;
}


function DeleteNoe($con,$noe_id,$title,$deleted_by){
    $res=new stdClass();

    $now=date('Y-m-d H:m:s',time());

       // insert 
    $update = "UPDATE  news_and_events SET deleted_at='$now',deleted_by='$deleted_by' WHERE noe_id='$noe_id'";


    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = $title." Deleted Successfully";
    } else {
        $res->success = false;

        $res->message = mysqli_error($con);
    }


    return $res;
}