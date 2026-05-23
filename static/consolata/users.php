<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// users interface

// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="Users";

$response->detail=UsersInterface($con,$REQ_OBJ);

echo json_encode($response);


function UsersInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["usr"])){
               return GetUser($con,$req->query_params["usr"]);

            }

            return GetUsers($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            $firstname=$rp["firstname"];

            $lastname=$rp["lastname"];

            $username=$rp["username"];

            $email=$rp["email"];

            $password=$rp["password"];

            $phone=$rp["phone"];

            $group=$rp["group"];

            $role=$rp["role"];

            return AddUser($con,$firstname,$lastname,$username,$email,$phone,$password,$role,$group);

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
            return GetUsers($con);
            break;
    }

}

//creates a user
//returns and object type success/error
function AddUser($con,$firstname,$lastname,$username,$email,$phone,$password,$role,$group){

    $res=new stdClass();

    if(UserExists($con,$email)){
        $res->success=false;
        $res->message="User already exists";

        return $res;
    }

    $user_id=uniqid();


    $address_id="0001";

    $slug=slugify($username);

    $password_hash=password_hash($password,PASSWORD_DEFAULT);

    $status="active";

    $auth_token="00000";

    $cols="user_id,firstname,lastname,username,email,phone,user_group,user_role,password,address_id,auth_token,slug,status";

    $values="'$user_id','$firstname','$lastname','$username','$email','$phone','$group','$role','$password_hash','$address_id','$auth_token','$slug','$status'";

    $insert=InsertResource($con,"users",$cols,$values);

    return $insert;

}

// gets users
// returns a list of users
function GetUsers($con,$options=null){

    $users_data=GetResource($con,"users","*");

    $users=[];

    foreach ($users_data as $key => $user) {
        # code...
        $user["extra_details"]=UserDetails($con,$user['user_id']);

        array_push($users,$user);
    }

    return $users;
    
}

//takes in a slug and returns a user object or null
function GetUser($con,$slug){

    $condition="WHERE slug='$slug'";

    $user=new stdClass();

    $users=GetResource($con,"users","*",$condition);

    if(isset($users[0])){

        $user->ok=true;
        $users[0]->details=UserDetails($con,$users[0]->user_id);

        $user->data=$users[0];
        $user->message="user";

        return($user);
    }

    $user->ok=false;

    $user->message="no such user exists";

    return $user;
}


// checks if a user exists 
function UserExists($con,$email){

    $sql="SELECT * FROM users WHERE email='$email'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}


function UserDetails($con,$user_id){

    $sql="SELECT * FROM user_details WHERE user_id='$user_id'";

    $query=mysqli_query($con,$sql);

 

    if($u=mysqli_fetch_assoc($query) ){
        return $u;
    }
   
    return false;
}
