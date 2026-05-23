<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// albums interface

// request object
$REQ_OBJ = RequestAdapter($_SERVER);

$response = new stdClass();

$response->message = "Gallery";

$response->detail = albumsInterface($con, $REQ_OBJ);

echo json_encode($response);


function albumsInterface($con, $req)
{
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if (isset($req->query_params["album"])) {
                return GetAlbum($con, $req->query_params["album"]);
            }

            return GetAlbums($con);

            break;

        case 'POST':
            $rp = $req->request_params;

            $action = $rp['action'];

            if ($action == "publish") {
                $album_id = $rp['album_id'];

                return PublishAlbum($con, $album_id);
            } elseif ($action == "update_album") {

                $album_id = $rp['album_id'];

                $title=$rp['title'];

                $description=$rp['description'];

                $updated_by=$rp['updated_by'];

                return UpdateAlbum($con, $album_id,$title,$description,$updated_by);
                

            } elseif ($action == "delete_album_image") {

                $album_id = $rp['album_id'];

                $image_url = $rp['image_url'];

                return DeleteAlbumImage($con, $album_id, $image_url);

            } elseif ($action == "delete_album") {
                # code...
                $album_id = $rp['album_id'];

                $deleted_by=$rp['deleted_by'];

                $title=$rp['title'];


                return DeleteAlbum($con, $album_id,$title,$deleted_by);
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
            return GetAlbums($con);
            break;
    }
}


// gets albums
// returns a list of albums
function GetAlbums($con, $options = null)
{

    $condition = "WHERE deleted_at IS NULL ORDER BY id DESC";

    $albums = GetResource($con, "gallery", "*", $condition);

    $albums = AddAlbumImages($albums);


    return $albums;
}

//takes in a slug and returns a album object or null
function GetAlbum($con, $slug)
{

    $condition = "WHERE slug='$slug'";

    $album = new stdClass();

    $albums = GetResource($con, "gallery", "*", $condition);
    // add images 
    $albums = AddAlbumImages($albums);


    if (isset($albums[0])) {

        $album->ok = true;
        $album->data = $albums[0];
        $album->message = "album";

        return ($album);
    }

    $album->ok = false;

    $album->message = "no such album exists";

    return $album;
}


// checks if a album exists 
function albumExists($con, $slug)
{

    $sql = "SELECT * FROM gallery WHERE slug='$slug'";

    $query = mysqli_query($con, $sql);

    if (mysqli_num_rows($query) != 0) {
        return true;
    }

    return false;
}


function AddAlbumImages($list)
{

    $albums = [];

    foreach ($list as $key => $album) {
        # code...
        $album["images"] = ResourceImages($album["images_uri"]);

        array_push($albums, $album);
    }

    return $albums;
}

function DeleteAlbum($con, $album_id,$title,$deleted_by)
{

    $res = new stdClass();

    $now = date("Y-m-d h:m:s", time());

    $status = "deleted";

    $update = "UPDATE gallery SET deleted_at='$now',deleted_by='$deleted_by',status='$status' WHERE album_id='$album_id'";

    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = "Album ".$title." Deleted Successfully";
    } else {

        $res->success = false;

        $res->message = mysqli_error($con);
    }

    return $res;
}

function DeleteAlbumImage($con, $album_id, $image_url)
{

    $res = new stdClass();

    $now = date("Y-m-d h:m:s", time());

    $path = realpath($image_url);

    $res->path = $path;



    // check if writable 
    if (!is_writable($image_url)) {

        $res->success = false;

        $res->message = "File Not Writable";

        return $res;
    }


    //unlink
    if (unlink($image_url)) {
        $res->success = true;

        $res->message = "Album Image Deleted";
    } else {
        $res->success = false;

        $res->message = "Error Deleting Image";
    }


    //create transaction 


    return $res;
}


function PublishAlbum($con, $album_id)
{

    $res = new stdClass();

    $status = "published";

    $visibility = "public";

    $update = "UPDATE gallery SET visibility='$visibility',status='$status' WHERE album_id='$album_id'";

    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = "Album Published";
    } else {

        $res->success = false;

        $res->message = mysqli_error($con);
    }

    return $res;
}


function UpdateAlbum($con, $album_id,$title,$description,$updated_by)
{

    $res = new stdClass();

    $now=date('Y-m-d H:m:s',time());


    $update = "UPDATE gallery SET title='$title',description='$description',updated_by='$updated_by' WHERE album_id='$album_id'";

    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = "Album ".$title." Updated Successfully";
    } else {

        $res->success = false;

        $res->message = mysqli_error($con);
    }

    return $res;
}
