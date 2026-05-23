<?php
require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// Inquiry interface

// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="Inquiries";

$response->detail=InquiryInterface($con,$REQ_OBJ);

echo json_encode($response);


function InquiryInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["category"])){
               return GetCategory($con,$req->query_params["category"]);

            }

            return GetInquiries($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            //check action
            if(isset($rp['action'])){
                $action=$rp['action'];
            }else{
                $action="add";
            }

            $name=$rp["name"];

            $email=$rp['email'];

            $phone=$rp['phone'];

            $message=addslashes($rp['message']);

            $property_id=$rp["property_id"];

            $inquiry_id=$rp['inquiry_id'];

            $status=$rp["status"];


            if($action=="update"){
                return UpdateInquiry($con,$name,$email,$phone,$message,$status,$inquiry_id);
            }


            return AddInquiry($con,$name,$email,$phone,$message,$property_id);

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
            return GetInquiries($con);
            break;
    }

}

//creates a category
//returns and object type success/error
function AddInquiry($con,$name,$email,$phone,$message,$property_id){

    $res=new stdClass();

    if(InquiryExists($con,$email,$property_id)){
        $res->success=false;
        $res->message="Inquiry exists.  Try checking another property";

        return $res;
    }

    $inquiry_id=uniqid();


    $status="pending";

    $cols="inquiry_id,name,email,phone,message,property_id,status";

    $values="'$inquiry_id','$name','$email','$phone','$message','$property_id','$status'";

    $insert=InsertResource($con,"inquiries",$cols,$values);

    return $insert;

}

function UpdateInquiry($con,$name,$email,$phone,$message,$status,$inquiry_id){

    $res=new stdClass();

   $update="UPDATE inquiries SET name='$name',email='$email',phone='$phone',message='$message',status='$status' WHERE inquiry_id='$inquiry_id'";


   if(mysqli_query($con,$update)){
        $res->success=true;

        $res->message="Inquiry updated";
   }else{
        $res->success=false;

        $res->message=mysqli_error($con);
   }

   return $res;

}


// gets Inquiry
// returns a list of Inquiry
function GetInquiries($con,$options=null){

    $inquiries=GetResource($con,"inquiries","*");

    return $inquiries;
    
}

//takes in a slug and returns a cat object or null
function GetInquiry($con,$inquiry_id){

    $condition="WHERE inquiry_id='$inquiry_id'";

    $cat=new stdClass();

    $Inquiry=GetResource($con,"Inquiry","*",$condition);

    if(isset($Inquiry[0])){

        $cat->ok=true;
        $cat->data=$Inquiry[0];
        $cat->message="inquiry";

        return($cat);
    }

    $cat->ok=false;

    $cat->message="no such inquiry exists";

    return $cat;
}


// checks if a cat exists 
function InquiryExists($con,$email,$property_id){

    $sql="SELECT * FROM inquiries WHERE email='$email' AND property_id='$property_id'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}

