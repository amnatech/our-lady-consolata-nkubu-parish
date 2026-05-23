<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// Packages interface

// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="Packages";

$response->detail=PackagesInterface($con,$REQ_OBJ);

echo json_encode($response);


function PackagesInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["package"])){
               return GetPackage($con,$req->query_params["package"]);

            }

            return GetPackages($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            $action=$rp['action'];

            $package_id=$rp['package_id'];

            $title=$rp['title'];

        

            if($action=="update"){

                $subtitle=$rp['subtitle'];

                $description=addslashes($rp['description']);

                $price=$rp['price'];

               return Updatepackage($con,$package_id,$title,$subtitle,$description,$price);
            }

            if($action=="delete"){

                $deleted_by=$rp["deleted_by"];
                return DeletePackage($con,$package_id,$title,$deleted_by);
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
            return GetPackages($con);
            break;
    }

}



// gets Packages
// returns a list of Packages
function GetPackages($con,$options=null){

    $condition="WHERE deleted_at IS NULL";

    $Packages=GetResource($con,"packages","*",$condition);

    $Acts=[];

    foreach ($Packages as $key => $package) {
        # code...
       $package['featured_image']=featuredImage($package['images_uri']);
        $package['images']=propertyImages($package['images_uri']);

        array_push($Acts,$package);
    }

    return $Acts;
    
}

//takes in a slug and returns an amenitiu object or null
function GetPackage($con,$slug){

    $condition="WHERE slug='$slug'";

    $cat=new stdClass();

    $Packages=GetResource($con,"packages","*",$condition);

    if(isset($Packages[0])){

        $cat->ok=true;

        $Packages[0]['featured_image']=featuredImage($Packages[0]['images_uri']);
        $Packages[0]['images']=propertyImages($Packages[0]['images_uri']);

        $cat->data=$Packages[0];

        
        $cat->message="package";

        return($cat);
    }

    $cat->ok=false;

    $cat->message="no such package exists";

    return $cat;
}


// checks if a cat exists 
function packageExists($con,$title){

    $sql="SELECT * FROM packages WHERE title='$title'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}

function Updatepackage($con,$package_id,$title,$subtitle,$description,$price){
    $res=new stdClass();

       // insert 
    $update = "UPDATE  packages SET title='$title',subtitle='$subtitle',description='$description',price='$price' WHERE package_id='$package_id'";


    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = "Package Updated Successfully";
    } else {
        $res->success = false;

        $res->message = mysqli_error($con);
    }


    return $res;
}



function DeletePackage($con,$package_id,$title,$deleted_by){

    $res=new stdClass();

    $now=date("Y-m-d H:m:s");

    $update="UPDATE packages SET deleted_at='$now',deleted_by='$deleted_by' WHERE package_id='$package_id'";

    if(mysqli_query($con,$update)){

        $res->success=true;

        $res->message="Package ".$title." deleted";
    }else{
        $res->success=false;
        $res->message=mysqli_error($con);
    }

    return $res;
}