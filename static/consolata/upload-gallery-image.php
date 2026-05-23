<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST,PUT');
header("Access-Control-Allow-Headers: *");

require "./includes/connect.php";
require "./includes/functions.php";

$res = new stdClass();

$album_id = $_POST['album_id'];

$dir=get_album_dir($con,$album_id);

$response=new stdClass();

if(upload_gallery_images($_FILES,$dir)){

    $res->success=true;

    $res->message="Album Image(s) Uploaded";
}else{
    $res->success=false;

    $res->message="There Was An Error Uploading Files. Try Again Later";
}

$response->detail=$res;

$response->message="Albums";

echo json_encode($response);

function get_album_dir($con, $album_id)
{

    $sql = "SELECT * FROM gallery WHERE album_id='$album_id'";

    $query = mysqli_query($con, $sql);

    if ($a = mysqli_fetch_assoc($query)) {
        return $a["images_uri"];
    }
}


// upload function 
function upload_gallery_images($files,$dir)
{

    $upload_results = [];

    foreach ($files as $file) {

        //  print_r($file);

        $image_name = $file['name'];

        $uploadOk = 1;

        $absolute_dir = $dir . $image_name;
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

            $absolute_dir = $dir . $newName;

            // $uploadOk = 0;
        }


        if ($uploadOk == 0) {
            //there was an error
            //echo "unkown error occured";
        } else {

            // print_r($file);
            if (move_uploaded_file($file['tmp_name'], $absolute_dir)) {

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

        return $dir;
    } else {

        return false;
    }
}



