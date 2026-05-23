<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// Groups interface

// request object
$REQ_OBJ = RequestAdapter($_SERVER);

$response = new stdClass();

$response->message = "Groups";

$response->detail = GroupsInterface($con, $REQ_OBJ);

echo json_encode($response);


function GroupsInterface($con, $req)
{
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if (isset($req->query_params["group"])) {
                return GetGroup($con, $req->query_params["group"]);
            }

            return GetGroups($con);

            break;

        case 'POST':
            $rp = $req->request_params;

            $action = $rp['action'];

            $group_id = $rp['group_id'];

            $title = $rp['title'];


            if ($action == "update") {

                $subtitle = $rp['subtitle'];

                $description = $rp['description'];

                $venue = $rp['venue'];

                $duration = $rp["duration"];

                return Updategroup($con, $group_id, $title, $subtitle, $description, $venue, $duration);
            } elseif ($action == "delete") {

                $deleted_by=$rp['deleted_by'];

                return Deletegroup($con, $group_id, $title, $deleted_by);

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
            return GetGroups($con);
            break;
    }
}



// gets Groups
// returns a list of Groups
function GetGroups($con, $options = null)
{

    $condition="WHERE deleted IS NULL";

    $Groups = GetResource($con, "groups_hd", "*",$condition);

    $Acts = [];

    foreach ($Groups as $key => $group) {
        # code...
        $group['featured_image'] = featuredImage($group['images_uri']);
        $group['images'] = propertyImages($group['images_uri']);

        array_push($Acts, $group);
    }

    return $Acts;
}

//takes in a slug and returns an amenitiu object or null
function GetGroup($con, $slug)
{

    $condition = "WHERE slug='$slug'";

    $cat = new stdClass();

    $Groups = GetResource($con, "groups_hd", "*", $condition);

    if (isset($Groups[0])) {

        $cat->ok = true;

        $Groups[0]['featured_image'] = featuredImage($Groups[0]['images_uri']);
        $Groups[0]['images'] = propertyImages($Groups[0]['images_uri']);

        $cat->data = $Groups[0];


        $cat->message = "group";

        return ($cat);
    }

    $cat->ok = false;

    $cat->message = "no such group exists";

    return $cat;
}


// checks if a cat exists 
function groupExists($con, $title)
{

    $sql = "SELECT * FROM groups_hd WHERE title='$title'";

    $query = mysqli_query($con, $sql);

    if (mysqli_num_rows($query) != 0) {
        return true;
    }

    return false;
}

function Updategroup($con, $group_id, $title, $subtitle, $description, $venue, $duration)
{
    $res = new stdClass();

    // insert 
    $update = "UPDATE  groups_hd SET title='$title',subtitle='$subtitle',description='$description',venue='$venue',duration='$duration' WHERE group_id='$group_id'";


    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = "Group Updated Successfully";
    } else {
        $res->success = false;

        $res->message = mysqli_error($con);
    }


    return $res;
}


function Deletegroup($con, $group_id, $title, $deleted_by)
{
    $res = new stdClass();

    $now=date('Y-m-d',time());

    // insert 
    $update = "UPDATE  groups_hd SET deleted='$now',deleted_by='$deleted_by' WHERE group_id='$group_id'";


    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = "group ".$title." Deleted Successfully";
    } else {
        $res->success = false;

        $res->message = mysqli_error($con);
    }


    return $res;
}
