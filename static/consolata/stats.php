<?php
require "./includes/connect.php";
require "./includes/functions.php";

// $password="2025";

$result=new stdClass();

$result->message="Stats";

$result->detail=GenealStats($con);

echo json_encode($result);

function GenealStats($con){

    $stats=new stdClass();

    $stats->properties=GetRecords($con,"properties","Properties","*");

    $stats->users=GetRecords($con,"users","Users","*");

    $stats->inquiries=GetRecords($con,"inquiries","All Inquiries","*");

    $filter="WHERE status='pending'";

    $stats->pending_inquiries=GetRecords($con,"inquiries","Pending Inquiries","*",$filter);


    return $stats;
}

function GetRecords($con,$model,$title,$filter,$condition=""){

    $res=new stdClass();

    $sql="SELECT $filter FROM $model $condition";

    $query=mysqli_query($con,$sql);

    $res->name=$model;

    $res->title=$title;

    $res->qty= mysqli_num_rows($query);

    return $res;
}

