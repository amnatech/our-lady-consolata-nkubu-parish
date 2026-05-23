<?php
    header("Access-Control-Allow-Origin: *");
    
    // require connection
    require '../includes/connect.php';

    require '../includes/functions.php';
    
    //result object
    $resObj=new stdClass();

    // echo 'success';
    $username=$_POST['username'];
    $password=$_POST['password'];

    //check userExist
    $checkExists="SELECT * FROM users WHERE username='$username'";

    // query
    $checkExistsQuery=mysqli_query($con,$checkExists);

    // fetch
    if($row=mysqli_fetch_assoc($checkExistsQuery)){
        
        $user=new stdClass();
        
        //exists
        $resObj->exists=true;
        // userid
        $user->name=$row['firstname']." ".$row['lastname'];
        
        $user->email=$row['email'];

        $user->phone=$row['phone'];

        $user->username=$row['username'];

        $user->slug=$row['slug'];

        
        $user->id=$row['username'];

        // stored password
        $hashedPass=$row['password'];
        //verify pass
        if(password_verify($password,$hashedPass)){
            // they match
            $resObj->login=true;
            
            $resObj->user=$user;

            $session_id=uniqid();

            $resObj->token=$user;

            //create transaction
            // createTransaction($con,'Login','User login','sessions',$session_id,json_encode($user),$user->name);
            $resObj->message="Login successful. Redirecting";

        }else{
            // they dont match
            $resObj->login=false;

            $resObj->message="Invalid Credentials. Try Again";

        
        }
    }else{
        //no such user
        $resObj->exists=false;

        $resObj->login=false;

        $resObj->message="Invalid Login. Try Again Later";



    }

    //encode to json and echo the json

    $result=new stdClass();

    $result->message="User Login";

    $result->detail=$resObj;

    echo json_encode($result);