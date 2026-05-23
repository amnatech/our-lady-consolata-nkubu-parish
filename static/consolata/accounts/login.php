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

    //check accountExist
    $checkExists="SELECT * FROM accounts WHERE username='$username'";

    // query
    $checkExistsQuery=mysqli_query($con,$checkExists);

    // fetch
    if($row=mysqli_fetch_assoc($checkExistsQuery)){
        
        $account=new stdClass();
        
        //exists
        $resObj->exists=true;
        // accountid
        $account->name=$row['title'];
        
        $account->email=$row['email'];

        $account->phone=$row['phone'];

        $account->username=$row['username'];

        $account->slug=$row['slug'];

        $account->type=$row['type'];
    
        $account->id=$row['account_id'];

        // stored password
        $hashedPass=$row['password'];
        //verify pass
        if(password_verify($password,$hashedPass)){
            // they match
            $resObj->login=true;
            
            $resObj->account=$account;

            $session_id=uniqid();

            $resObj->token=$account;

            //create transaction
            // createTransaction($con,'Login','account login','sessions',$session_id,json_encode($account),$account->name);
            $resObj->message="Login successful. Redirecting";

        }else{
            // they dont match
            $resObj->login=false;

            $resObj->message="Invalid Credentials. Try Again";

        
        }
    }else{
        //no such account
        $resObj->exists=false;

        $resObj->login=false;

        $resObj->message="Invalid Login. Try Again Later";



    }

    //encode to json and echo the json

    $result=new stdClass();

    $result->message="account Login";

    $result->detail=$resObj;

    echo json_encode($result);