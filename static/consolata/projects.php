<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// Projects interface
// @@ NORE (News OR Event)
// @@ 
// @@ 


// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="Projects";

$response->detail=PeojectsInterface($con,$REQ_OBJ);

echo json_encode($response);


function PeojectsInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["project"])){
               return GetProject($con,$req->query_params["project"]);

            }

            return GetProjects($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            $action=$rp['action'];

            $project_id=$rp['project_id'];

            $title=$rp['title'];

            if($action=="delete"){

                $deleted_by=$rp['deleted_by'];

               return DeleteProject($con,$project_id,$title,$deleted_by);

            }

            $subtitle=$rp['subtitle'];

            $description=$rp['description'];

            $location=$rp['location'];

            $project_year=$rp["project_year"];

            $start_date=$rp["start_date"];

            $end_date=$rp["end_date"];



            if($action=="update"){
               return UpdateProject($con,$project_id,$title,$subtitle,$description,$location,$project_year,$start_date,$end_date);
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
            return GetProjects($con);
            break;
    }

}



// gets Peojects
// returns a list of Peojects
function GetProjects($con,$options=null){

    $condition="WHERE deleted_at IS NULL";

    $projects=GetResource($con,"projects","*",$condition);

    $Acts=[];

    foreach ($projects as $key => $project) {
        # code...
       $project['featured_image']=featuredImage($project['images_uri']);
        $project['images']=propertyImages($project['images_uri']);

        array_push($Acts,$project);
    }

    return $Acts;
    
}

//takes in a slug and returns an amenitiu object or null
function GetProject($con,$slug){

    $condition="WHERE slug='$slug'";

    $cat=new stdClass();

    $projects=GetResource($con,"projects","*",$condition);

    if(isset($projects[0])){

        $cat->ok=true;

        $projects[0]['featured_image']=featuredImage($projects[0]['images_uri']);
        $projects[0]['images']=propertyImages($projects[0]['images_uri']);

        $cat->data=$projects[0];

        
        $cat->message="project";

        return($cat);
    }

    $cat->ok=false;

    $cat->message="no such project exists";

    return $cat;
}


// checks if a cat exists 
function projectExists($con,$title){

    $sql="SELECT * FROM projects WHERE title='$title'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}

function UpdateProject($con,$project_id,$title,$subtitle,$description,$location,$project_year,$start_date,$end_date){
    $res=new stdClass();

       // insert 
    $update = "UPDATE  projects SET title='$title',subtitle='$subtitle',description='$description',location='$location',project_year='$project_year',
                start_date='$start_date',end_date='$end_date' WHERE project_id='$project_id'";


    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message ="Project ".$title." Updated Successfully";
    } else {
        $res->success = false;

        $res->message = mysqli_error($con);
    }


    return $res;
}

function DeleteProject($con,$project_id,$title,$deleted_by){
    $res=new stdClass();

    $now=date('Y-m-d H:m:s',time());

       // insert 
    $update = "UPDATE  projects SET deleted_at='$now',deleted_by='$deleted_by' WHERE project_id='$project_id'";


    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = $title." Deleted Successfully";
    } else {
        $res->success = false;

        $res->message = mysqli_error($con);
    }


    return $res;
}