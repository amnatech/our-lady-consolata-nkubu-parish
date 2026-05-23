<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// Activities interface

// request object
$REQ_OBJ = RequestAdapter($_SERVER);

$response = new stdClass();

$response->message = "Activities";

$response->detail = ActivitiesInterface($con, $REQ_OBJ);

echo json_encode($response);


function ActivitiesInterface($con, $req)
{
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if (isset($req->query_params["activity"])) {
                return GetActivity($con, $req->query_params["activity"]);
            }

            return GetActivities($con);

            break;

        case 'POST':
            $rp = $req->request_params;

            $action = $rp['action'];

            $activity_id = $rp['activity_id'];

            $title = $rp['title'];


            if ($action == "update") {

                $subtitle = $rp['subtitle'];

                $description = $rp['description'];

                $venue = $rp['venue'];

                $duration = $rp["duration"];

                return UpdateActivity($con, $activity_id, $title, $subtitle, $description, $venue, $duration);
            } elseif ($action == "delete") {

                $deleted_by=$rp['deleted_by'];

                return DeleteActivity($con, $activity_id, $title, $deleted_by);

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
            return GetActivities($con);
            break;
    }
}



// gets Activities
// returns a list of Activities
function GetActivities($con, $options = null)
{

    $condition="WHERE deleted IS NULL";

    $Activities = GetResource($con, "activities", "*",$condition);

    $Acts = [];

    foreach ($Activities as $key => $activity) {
        # code...
        $activity['featured_image'] = featuredImage($activity['images_uri']);
        $activity['images'] = propertyImages($activity['images_uri']);

        array_push($Acts, $activity);
    }

    return $Acts;
}

//takes in a slug and returns an amenitiu object or null
function GetActivity($con, $slug)
{

    $condition = "WHERE slug='$slug'";

    $cat = new stdClass();

    $Activities = GetResource($con, "activities", "*", $condition);

    if (isset($Activities[0])) {

        $cat->ok = true;

        $Activities[0]['featured_image'] = featuredImage($Activities[0]['images_uri']);
        $Activities[0]['images'] = propertyImages($Activities[0]['images_uri']);

        $cat->data = $Activities[0];


        $cat->message = "activity";

        return ($cat);
    }

    $cat->ok = false;

    $cat->message = "no such activity exists";

    return $cat;
}


// checks if a cat exists 
function ActivityExists($con, $title)
{

    $sql = "SELECT * FROM activities WHERE title='$title'";

    $query = mysqli_query($con, $sql);

    if (mysqli_num_rows($query) != 0) {
        return true;
    }

    return false;
}

function UpdateActivity($con, $activity_id, $title, $subtitle, $description, $venue, $duration)
{
    $res = new stdClass();

    // insert 
    $update = "UPDATE  activities SET title='$title',subtitle='$subtitle',description='$description',venue='$venue',duration='$duration' WHERE activity_id='$activity_id'";


    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = "Activity Updated Successfully";
    } else {
        $res->success = false;

        $res->message = mysqli_error($con);
    }


    return $res;
}


function DeleteActivity($con, $activity_id, $title, $deleted_by)
{
    $res = new stdClass();

    $now=date('Y-m-d',time());

    // insert 
    $update = "UPDATE  activities SET deleted='$now',deleted_by='$deleted_by' WHERE activity_id='$activity_id'";


    if (mysqli_query($con, $update)) {

        $res->success = true;

        $res->message = "Activity ".$title." Deleted Successfully";
    } else {
        $res->success = false;

        $res->message = mysqli_error($con);
    }


    return $res;
}
