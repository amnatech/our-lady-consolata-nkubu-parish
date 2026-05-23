<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST,PUT');
header("Access-Control-Allow-Headers: *");

require "./includes/connect.php";
require "./includes/functions.php";

$res = new stdClass();


// $files ;

$title = addslashes($_POST['title']);

$description = addslashes($_POST['description']);

$album_date = addslashes($_POST['album_date']);

$created_by = addslashes($_POST['created_by']);


// slug a name
$slug = slugify($title);

$status = 'draft';

$visibility = "public";

$album_id = $_POST['album_id'];

$images_dir = create_album_dir($slug);

$cover_image_dir = $images_dir;


$insert = "INSERT INTO gallery (album_id,title,album_date,description,created_by,images_uri,slug,visibility,updated_by,status) 
    VALUES('$album_id','$title','$album_date','$description','$created_by','$images_dir','$slug','$visibility','$created_by','$status')";

if (mysqli_query($con, $insert)) {

    $res->success = true;

    $res->message = "Gallery Album Created Successfully";
} else {
    $res->success = false;

    $res->message = mysqli_error($con);
}



$response = new stdClass();

$response->message = "Create Gallery Album";

$response->detail = $res;

echo json_encode($response);


function create_album_dir($slug)
{

    $folder_name = $slug . "-" . uniqid();

    $oldmask = umask(0);

    mkdir('./uploads/gallery/' . $folder_name . '/',0777);

    umask($oldmask);

    $base_dir = "uploads/gallery/" . $folder_name . '/';

    return $base_dir;
}


