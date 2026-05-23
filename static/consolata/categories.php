<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

// Categories interface

// request object
$REQ_OBJ=RequestAdapter($_SERVER);

$response=new stdClass();

$response->message="Categories";

$response->detail=CategoriesInterface($con,$REQ_OBJ);

echo json_encode($response);


function CategoriesInterface($con,$req){
    switch ($req->method) {
        case 'GET':
            // echo $REQ_OBJ->query_params["slug"];
            # code...
            if(isset($req->query_params["category"])){
               return GetCategory($con,$req->query_params["category"]);

            }

            return GetCategories($con);
        
            break;

        case 'POST':
            $rp=$req->request_params;

            $name=$rp["name"];

            // check if parent is available 
            // if not the category is its parent 
            if(isset($rp['parent'])){
                $parent=$rp["parent"];
            }else{
                $parent=$name;
            }


            return AddCategory($con,$name,$parent);

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
            return GetCategories($con);
            break;
    }

}

//creates a category
//returns and object type success/error
function AddCategory($con,$name,$parent){

    $res=new stdClass();

    if(CategoryExists($con,$name)){
        $res->success=false;
        $res->message="Category already exists";

        return $res;
    }

    $cat_id=uniqid();

    $slug=slugify($name);


    $status="active";

    $cols="cat_id,name,parent,slug,status";

    $values="'$cat_id','$name','$parent','$slug','$status'";

    $insert=InsertResource($con,"categories",$cols,$values);

    return $insert;

}

// gets Categories
// returns a list of Categories
function GetCategories($con,$options=null){

    $categories=GetResource($con,"categories","*");

    return $categories;
    
}

//takes in a slug and returns a cat object or null
function GetCategory($con,$slug){

    $condition="WHERE slug='$slug'";

    $cat=new stdClass();

    $categories=GetResource($con,"categories","*",$condition);

    if(isset($categories[0])){

        $cat->ok=true;
        $cat->data=$categories[0];
        $cat->message="category";

        return($cat);
    }

    $cat->ok=false;

    $cat->message="no such category exists";

    return $cat;
}


// checks if a cat exists 
function CategoryExists($con,$name){

    $sql="SELECT * FROM categories WHERE name='$name'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)!=0){
        return true;
    }
        
    return false;
}

