<?php

// takes in request server GLOBAL and extracts 
// basic request details 
function RequestAdapter($request_server){

    $res=new stdClass();

    $res->method=$request_server["REQUEST_METHOD"];

    $res->query=$request_server["QUERY_STRING"];

    $res->uri=$request_server["REQUEST_URI"];

    $res->query_params=QueryParams($res->query);

    $res->request_params=$_REQUEST;


    return $res;

}



// takes in a query string 
// returns an associative array
function QueryParams($query_string){

    // if no params
    if($query_string==""){
        return [];
    }

    $params_list=explode("&",$query_string);

    $params=[];

    foreach ($params_list as $key => $query_param) {
        # code...
        $qp_list=explode("=",$query_param);

        $qp[$qp_list[0]]= $qp_list[1];

        array_push($params,$qp);
    }

    return $params[sizeof($params)-1];
}