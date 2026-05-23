<?php
require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// Inquiry interface

// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="Tithes";

$response->detail=TitheInterface($con,$REQ_OBJ);

echo json_encode($response);


function TitheInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["tithe"])){
               return GetTithe($con,$req->query_params["tithe"]);
            }elseif (isset($req->query_params['stats'])) {
                # code...
                return GetTitheStats($con);
            }

            return GetTithes($con);
        
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

            $user_id=$rp['user_id'];

            $tithe_id=$rp['tithe_id'];

            $serial=addslashes($rp['serial_no']);

            $transaction_ref=addslashes($rp['transaction_ref']);

            $prayer_house=$rp["house"];

            $amount=$rp["amount"];

            $month=$rp["month"];

            $created_by=$rp["created_by"];


            // if($action=="update"){
            //     return UpdateInquiry($con,$name,$email,$phone,$message,$status,$tithe_id);
            // }


            return AddTithe($con,$tithe_id,$transaction_ref,$serial,$user_id,$name,$amount,$month,$prayer_house,$created_by);

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
            return GetTithes($con);
            break;
    }

}

//creates a category
//returns and object type success/error
function AddTithe($con,$tithe_id,$transaction_ref,$serial,$user_id,$name,$amount,$month,$prayer_house,$created_by){

    $res=new stdClass();

    if(TitheExists($con,$user_id,$amount,$month,$prayer_house)){
        $res->success=false;
        $res->message="Entry Exists.  Try Selecting Another User Or Month";

        return $res;
    }

    $tithe_id=uniqid();


    $status="complete";

    $cols="tithe_id,transaction_ref,serial_no,user_id,name,amount,month,house,created_by,status";

    $values="'$tithe_id','$transaction_ref','$serial','$user_id','$name','$amount','$month','$prayer_house','$created_by','$status'";

    $insert=InsertResource($con,"tithes",$cols,$values);

    return $insert;

}

function UpdateInquiry($con,$name,$email,$phone,$message,$status,$tithe_id){

    $res=new stdClass();

   $update="UPDATE tithes SET name='$name',email='$email',phone='$phone',message='$message',status='$status' WHERE tithe_id='$tithe_id'";


   if(mysqli_query($con,$update)){
        $res->success=true;

        $res->message="Tithe updated";
   }else{
        $res->success=false;

        $res->message=mysqli_error($con);
   }

   return $res;

}


// gets Inquiry
// returns a list of Inquiry
function GetTithes($con,$options=null){

    $tithes=GetResource($con,"tithes","*");

    $tithes_arr=[];

    foreach ($tithes as $key => $tithe) {
        # code...
        $user_id=$tithe["user_id"];

        $condition="WHERE user_id='$user_id'";

        $tithe['user']=GetResource($con,"users","*",$condition)[0];

        array_push( $tithes_arr,$tithe);
    }


    return $tithes_arr;
    
}

//takes in a slug and returns a cat object or null
function GetTithe($con,$tithe_id){

    $condition="WHERE tithe_id='$tithe_id'";

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
function TitheExists($con,$user_id,$amount,$month,$prayer_house){

    $sql="SELECT * FROM tithes WHERE user_id='$user_id' AND month='$month'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}


function GetTitheStats($con,$condition=null){

    $stats=[];

    $sql="SELECT SUM(amount) AS total,COUNT(id) AS entries FROM tithes $condition" ;

    $query=mysqli_query($con,$sql);

    if($t=mysqli_fetch_assoc($query)){

        $stat=new stdClass();

        $stat->value=$t['total'];

        $stat->name="Total Value";

        $stat->url="";

        $stat->change=0;

        $stat->type="currency";


        array_push($stats,$stat);

        $stat=new stdClass();

        $stat->value=$t['entries'];

        $stat->name="Total Tithes";

        $stat->url="";

        $stat->change=0;

        array_push($stats,$stat);

    }

    $current_month=date('Y-m',time());

    $sql="SELECT SUM(amount) AS total,COUNT(id) AS entries FROM tithes WHERE month LIKE '%$current_month%'" ;

    $query=mysqli_query($con,$sql);

    if($t=mysqli_fetch_assoc($query)){
        $stat=new stdClass();

      
        $stat->value=$t['total'];

        if(!$t['total']){
            $stat->value=0;
        }

        $stat->name="Value This Month";

        $stat->url="";

        $stat->change=0;

        $stat->type="currency";


        array_push($stats,$stat);

        $stat=new stdClass();

        $stat->value=$t['entries'];

        $stat->name="Tithes This Month";

        $stat->url="";

        $stat->change=0;

        array_push($stats,$stat);
    }

    return $stats;

}