<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST,PUT');
header("Access-Control-Allow-Headers: *");

require "./includes/connect.php";
require "./includes/functions.php";

$res = new stdClass();


// $files ;

$house_id = addslashes($_POST['house_id']);

$name = addslashes($_POST['name']);

$email = addslashes($_POST['email']);

$phone = addslashes($_POST['phone']);

$location = addslashes($_POST['location']);

$created_by=addslashes($_POST['created_by']);


// slug a name
$slug =slugify($name);

$status = 'active';


$dir = upload_gallery_images($_FILES, $name);

if ($dir != false) {

    if(CreateAccount($con,$name,"prayer_house",$email,$phone,$created_by)){
        // insert 
        $insert = "INSERT INTO prayer_houses (house_id,name,email,phone,location,images_uri,slug,status) 
        VALUES('$house_id','$name','$email','$phone','$location','$dir','$slug','$status')";

        if (mysqli_query($con, $insert)) {

            $res->success = true;

            $res->message = "Prayer House Created Successfully";
        } else {
            $res->success = false;

            $res->message = mysqli_error($con);
        }
    }else{
        $res->success = false;

        $res->message = mysqli_error($con);
    }


} else {

    $res->success = false;

    $res->message = "There Was An Error Uploading House Files";
}

$response=new stdClass();

$response->message="Create Prayer House";

$response->detail=$res;

echo json_encode($response);

// get the other details



// upload function 
function upload_gallery_images($files, $name)
{

    $upload_results = [];

    // slugify the name
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    $folder_name = $slug . "-" . uniqid();

    $oldmask = umask(0);
    mkdir('./uploads/houses/' . $folder_name . '/');
    umask($oldmask);

    $base_dir = "uploads/houses/" . $folder_name . '/';

    foreach ($files as $file) {

        // echo $file['name'];

        $image_name = $file['name'];

        $uploadOk = 1;

        $absolute_dir = $base_dir . '/' . $image_name;
        //check file type
        $img_file_type = pathinfo($absolute_dir, PATHINFO_EXTENSION);

        //
        if ($img_file_type != 'png' && $img_file_type != 'jpeg' && $img_file_type != 'jpg' && $img_file_type != 'gif' && $img_file_type != 'webp' && $img_file_type != 'avif') {
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
            if (move_uploaded_file($file['tmp_name'], 'uploads/houses/' . $folder_name . '/' . $image_name)) {

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

