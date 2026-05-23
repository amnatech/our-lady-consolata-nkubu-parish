<?php
require "../includes/connect.php";
require "../includes/functions.php";

// $password="2025";

// echo password_hash($password,PASSWORD_DEFAULT);

// $req=RequestAdapter($_SERVER);

// echo json_encode($req);


CreateCounties($con);
// echo json_encode($_REQUEST);



function CreateCounties($con){

    $pd_con = mysqli_connect("localhost", "root", "","patadose_db");

    $counties=[];

    $loc_type="county";

    $sel="SELECT * FROM Locations WHERE type='$loc_type'";

    $query=mysqli_query($pd_con,$sel);

    while($c=mysqli_fetch_assoc($query)){

        $loc=new stdClass();


        $name=$c['name'];

        $type="county";

        $latitude=$c['lat'];

        $longitude=$c['lng'];

        $created_by="system";


        $add=AddLocation($con,$name,$type,$latitude,$longitude,$created_by);

        echo json_encode($add);

        echo "<br>";

        // array_push($counties,$loc);

    }

    // return $counties;
    // echo json_encode($counties);
}




function AddLocation($con,$name,$type,$latitude,$longitude,$created_by){

    $res=new stdClass();

    if(LocationExists($con,$name)){
        $res->success=false;
        $res->message="location already exists";

        return $res;
    }

    $resource_id=uniqid();

    $slug=slugify($name);

    $status="active";

    $cols="location_id,name,type,latitude,longitude,created_by,slug";

    $values="'$resource_id','$name','$type','$latitude','$longitude','$created_by','$slug'";

    $insert=InsertResource($con,"locations",$cols,$values);

    return $insert;

}



// checks if a cat exists 
function LocationExists($con,$name){

    $sql="SELECT * FROM locations WHERE name='$name'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}

