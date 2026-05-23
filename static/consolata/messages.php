<?php
require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// Messages interface

// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="Messages";

$response->detail=MessagesInterface($con,$REQ_OBJ);

echo json_encode($response);


function MessagesInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["message"])){
               return GetMessage($con,$req->query_params["message"]);

            }

            return GetMessages($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            //check action
            if(isset($rp['action'])){
                $action=$rp['action'];
            }else{
                $action="add";
            }

            $name=addslashes($rp["name"]);

            $email=addslashes($rp['email']);

            $phone=$rp['phone'];

            $message=addslashes($rp['message']);

            $subject=addslashes($rp["subject"]);

            $message_id=$rp['message_id'];

            $status=$rp["status"];

            if($action=="update"){
                return UpdateMessages($con,$name,$email,$phone,$message,$status,$message_id);
            }


            return AddMessage($con,$name,$email,$phone,$message,$subject,$message_id);

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
            return GetMessages($con);
            break;
    }

}

//creates a category
//returns and object type success/error
function AddMessage($con,$name,$email,$phone,$message,$subject,$message_id){

    $res=new stdClass();

    if(MessageExists($con,$email,$subject)){
        $res->success=false;
        $res->message="Message exists. Try Again later";

        return $res;
    }

    $status="pending";

    $cols="message_id,name,email,phone,message,subject,status";

    $values="'$message_id','$name','$email','$phone','$message','$subject','$status'";

    $insert=InsertResource($con,"messages",$cols,$values);

    if($insert->success){
        $res->success=true;

        $res->message="Message Sent. We Shall Contact You Within The Day ";
    }else{
        $res->success=false;

        $res->message=$insert->message;
    }

    return $res;

}

function UpdateMessages($con,$name,$email,$phone,$message,$status,$message_id){

    $res=new stdClass();

   $update="UPDATE Messages SET name='$name',email='$email',phone='$phone',message='$message',status='$status' WHERE Messages_id='$message_id'";


   if(mysqli_query($con,$update)){
        $res->success=true;

        $res->message="Messages updated";
   }else{
        $res->success=false;

        $res->message=mysqli_error($con);
   }

   return $res;

}


// gets Messages
// returns a list of Messages
function GetMessages($con,$options=null){

    $Messages=GetResource($con,"messages","*");

    return $Messages;
    
}

//takes in a slug and returns a cat object or null
function GetMessage($con,$message_id){

    $condition="WHERE message_id='$message_id'";

    $cat=new stdClass();

    $Messages=GetResource($con,"messages","*",$condition);

    if(isset($Messages[0])){

        $cat->ok=true;
        $cat->data=$Messages[0];
        $cat->message="Messages";

        return($cat);
    }

    $cat->ok=false;

    $cat->message="no such Messages exists";

    return $cat;
}


// checks if a cat exists 
function MessageExists($con,$email,$subject){

    $status="pending";

    $sql="SELECT * FROM messages WHERE email='$email' AND subject='$subject' AND status='$status'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}

