<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST,PUT');
header("Access-Control-Allow-Headers: *");

require "./includes/connect.php";
require "./includes/functions.php";

$res = new stdClass();

// get sent data
$property = file_get_contents('php://input');


// $files ;
$name = addslashes($_POST['name']);

$town = addslashes($_POST['town']);

$suburb = addslashes($_POST['suburb']);

$address = addslashes($_POST['address']);

$location = addslashes($_POST['area']);

$category = addslashes($_POST['category']);

$area = addslashes($_POST['area']);

$amenities=addslashes($_POST['amenities']);

$nearby=addslashes($_POST['nearby']);

$description=addslashes($_POST['description']);

$county=GetCounty($con,$town);

$created_by=addslashes($_POST['created_by']);



// slug a name
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name'] . '-' . substr(str_shuffle('qwertyuioplkjhgfdsazxcvbnm'), 0, 4))));


$access_type = $_POST['access_type'];
$use_type = $_POST['use_type'];



$status = 'active';

$property_id = $_POST['property_id'];


$dir = upload_property_image($_FILES, $name);

if ($dir != false) {

    // insert 
    $insert = "INSERT INTO properties (property_id,name,county,town,suburb,area,address,description,category,use_type,access_type,amenities,nearby,created_by,images_uri,slug,status) 
    VALUES('$property_id','$name','$county','$town','$suburb','$area','$address','$description','$category','$use_type','$access_type','$amenities','$nearby','$created_by','$dir','$slug','$status')";

    if (mysqli_query($con, $insert)) {

        $res->success = true;

        $res->message = "Property Created Successfully";
    } else {
        $res->success = false;

        $res->message = mysqli_error($con);
    }
} else {

    $res->success = false;

    $res->message = "There Was An Error Uploading Property Files";
}

$response=new stdClass();

$response->message="Create Property";

$response->detail=$res;

echo json_encode($response);

// get the other details



// upload function 
function upload_property_image($files, $name)
{

    $upload_results = [];

    // slugify the name
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    $folder_name = $slug . "-" . uniqid();

    $oldmask = umask(0);
    mkdir('./uploads/property-images/' . $folder_name . '/');
    umask($oldmask);

    $base_dir = "uploads/property-images/" . $folder_name . '/';

    foreach ($files as $file) {

        // echo $file['name'];

        $image_name = $file['name'];

        $uploadOk = 1;

        $absolute_dir = $base_dir . '/' . $image_name;
        //check file type
        $img_file_type = pathinfo($absolute_dir, PATHINFO_EXTENSION);

        //
        if ($img_file_type != 'png' && $img_file_type != 'jpeg' && $img_file_type != 'jpg' && $img_file_type != 'gif' && $img_file_type != 'webp') {
            echo 'only images can be uploaded';
            $uploadOk = 0;
        }

        // 
        //check if file exis
        if (file_exists($absolute_dir)) {

            $nameArray = explode('.', $image_name);

            $newName = $nameArray[0] . uniqid() . "." . $nameArray[1];

            // echo 'The image already exists, Please rename the image if its another product';

            $absolute_dir = $base_dir . $newName;

            // $uploadOk = 0;
        }


        if ($uploadOk == 0) {
            //there was an error
            //echo "unkown error occured";
        } else {

            // print_r($file);
            if (move_uploaded_file($file['tmp_name'], 'uploads/property-images/' . $folder_name . '/' . $image_name)) {

                // echo "upload successfull";
                array_push($upload_results, 'success');
            } else {

                // echo "there was an error";
                array_push($upload_results, 'error');
            }
        }
    }

    // return path 
    if (in_array('success', $upload_results)) {

        return $base_dir;
    } else {

        return false;
    }
}


function GetCounty($con,$town){
    $type="town";
    $sql="SELECT * FROM locations WHERE name='$town' AND type='$type'";

    $query=mysqli_query($con,$sql);

    if($t=mysqli_fetch_assoc($query)){
        return $t['parent'];
    }else{
        return "";
    }
}