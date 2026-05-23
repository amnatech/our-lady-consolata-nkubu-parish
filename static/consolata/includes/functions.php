<?php

    function createTransaction($con,$name,$description,$resource,$resource_id,$payload,$init){

        //create transaction

        $transaction_id=uniqid();

        $sql="INSERT INTO transactions (transaction_id,name,description,resource,resource_id,payload,created_by,status) 
                VALUES('$transaction_id','$name','$description','$resource','$resource_id','$payload','$init','complete')";

        if(mysqli_query($con,$sql)){
            return true;
        }else{
            return mysqli_error($con);
        }
    }


    function slugify($name){

        $str="qwertyuiopasdfghjklzxcvbnnm";

        return str_replace(' ','-',strtolower($name)).'-'.substr(str_shuffle($str),0,5);
    }

    function generateProductCode($name){

        //split the name 
        $name_arr=explode(" ",$name,3);

        $code_arr=[];

        foreach ($name_arr as $key => $value) {
            # code...
            array_push($code_arr,substr($value,0,1));
        }

        //implode the code
        $code_letters=implode("",$code_arr);

        $str="12345678900987654321";

        return strtoupper($code_letters.substr(str_shuffle($str),0,3));
    }


    function getRecordEntriesNo($con,$query){

        $sql_query=mysqli_query($con,$query);

        return mysqli_num_rows($sql_query);

    }


    function capitalize($str){
        return strtoupper(substr($str,0,1)).strtolower(substr($str,1));
    }


    function topCategories($con,$limit=5){

        $categories=[];
    
        //
        $sql="SELECT DISTINCT category, COUNT(category) as qty FROM properties GROUP BY category ORDER BY qty DESC LIMIT $limit";
    
        $query=mysqli_query($con,$sql);
    
        while($c=mysqli_fetch_assoc($query)){
    
            $cat=new stdClass();
    
            $cat->name=$c['category'];
    
            $cat->qty=$c['qty'];
    
            array_push($categories,$cat);
        }
    
        return $categories;
    }

    function productSetting($con,$key,$target){


        $sql="SELECT * FROM settings WHERE setting_key='$key' AND setting_target='$target'";

        $query=mysqli_query($con,$sql);

        if ($s=mysqli_fetch_assoc($query)) {
            return $s['setting_value'];
        }
    }

    function batchNumber(){

        $date=date('Ymd');

        $nos="0123456789987456213";

        $rand=substr(str_shuffle($nos),0,3);

        return $date.$rand;
    }

    function filterDate($days){

        $then=date('Y-m-d',strtotime("-$days days"));
    
        return $then;
    }


function featuredImage($dir){

    if(is_dir($dir)){
        // echo "yes";
        return $dir.scandir($dir)[2];
    }else{
        $images=scandir("uploads/property-images/placeholders/");

        return "uploads/property-images/placeholders/".$images[2];
    }

}


function propertyImages($dir){

    if(is_dir($dir)){
        // echo "yes";
        $files=scandir($dir);

        $images=[];

        for ($i=0; $i < sizeof($files); $i++) { 
            # code...
            if($i>1){
                array_push($images,$dir.$files[$i]);
            }
        }

        return $images;
    }else{
        $images=scandir("uploads/property-images/placeholders/");

        return ["uploads/property-images/placeholders/".$images[2]];
    }

}


function ResourceImages($dir){

    if(is_dir($dir)){
        // echo "yes";
        $files=scandir($dir);

        $images=[];

        for ($i=0; $i < sizeof($files); $i++) { 
            # code...
            if($i>1){
                array_push($images,$dir.$files[$i]);
            }
        }

        return $images;
    }else{
        $images=scandir("uploads/placeholders/");

        return ["uploads/placeholders/".$images[2]];
    }

}



function remove_special_characters($str){

    return preg_replace('/[^A-Za-z0-9\-]/','',$str);

}


function days_between_dates($start,$end){

    $day_in_seconds=86400;

    return (strtotime($end)-strtotime($start))/$day_in_seconds;
}


// gets a number of months starting from a starting one 
function get_months($start=0,$size=11){

    $months='[{"name":"January","no":"01"},{"name":"February","no":"02"},{"name":"March","no":"03"},{"name":"April","no":"04"},{"name":"May","no":"05"},{"name":"June","no":"06"},{"name":"July","no":"07"},{"name":"August","no":"08"},{"name":"September","no":"09"},{"name":"October","no":"10"},{"name":"November","no":"11"},{"name":"December","no":"12"}]';

    return array_slice(json_decode($months),$start,$size);
}



/////
// takes in db connection, slug and optional options 
function property($con,$slug,$options=null){

    $property=new stdClass();

    $sql="SELECT * FROM properties WHERE slug='$slug'";

    $query=mysqli_query($con,$sql);

    if($p=mysqli_fetch_assoc($query)){

        $property->exists=true;

        $p['mainImage']=featuredImage($p['imagesUrl']);

        $p['otherImages']=[];

        $p['similar']=similarProperties($con,$slug);

        $property->data=$p;
    }else{
        $property->exists=false;
    }

    return $property;
}

function similarProperties($con,$slug){

    $similar=[];

    $sql="SELECT * FROM properties WHERE NOT slug='$slug' LIMIT 4";

    $query=mysqli_query($con,$sql);

    while($p=mysqli_fetch_assoc($query)){

        $sim=new stdClass();

        $sim->name=$p['name'];

        $sim->town=$p['town'];

        $sim->suburb=$p['suburb'];

        $sim->address=$p['address'];

        $sim->price=0;

        $sim->size=0;

        $sim->bedrooms=0;

        $sim->bathrooms=0;

        $sim->slug=$p['slug'];


        $sim->image=featuredImage($p['images_uri']);

        array_push($similar,$sim);
    }


    return $similar;
}



//check if resource  exists
// model = table name 
// prop= colum to search 
// needle the value to search
function resExists($con,$model,$prop,$needle){

    $sql="SELECT $prop FROM $model WHERE $prop='$needle'";

    $query=mysqli_query($con,$sql);

    if(mysqli_num_rows($query)>0){
        return true;
    }else{
        return false;
    }
}


// insert
function InsertResource($con,$model,$cols,$values){

    $res=new stdClass();

    $sql="INSERT INTO $model ($cols) VALUES($values)";

    if(mysqli_query($con,$sql)){
        $res->success=true;
        $res->message="insert successful";

    }else{
        $res->success=false;
        $res->message=mysqli_error($con);
    }

    return $res;


}

//gets resource from a db
function GetResource($con,$model,$filter,$condition=""){

    $data=[];

    $sql="SELECT $filter FROM $model $condition ";

    $query=mysqli_query($con,$sql);

    while($r=mysqli_fetch_assoc($query)){

        array_push($data,$r);
    }

    return $data;

}


function PropertyMainImage($props){

    $properties=[];

    foreach ($props as $key => $p) {
        # code...
        $p['featured_image']=featuredImage($p['images_uri']);

        array_push($properties,$p);
    }

    return $properties;
}

function PropertyUnits($con,$property_id){

    $condition="WHERE property_id='$property_id'";

    $units= GetResource($con,"units","*",$condition);

    return $units;
}

function PropertyUnitPrices($con,$props){
        $properties=[];

    foreach ($props as $key => $p) {
        # code...
        $units=PropertyUnits($con,$p['property_id']);

        $p['prices']=PropertyPrices($units);

        array_push($properties,$p);
    }

    return $properties;
}

function PropertyPrices($units){

    $prices=new stdClass();


    if(sizeof($units)==0){

        $prices->min_price=10;

        $prices->max_price=10;

        return $prices;
    }

    $min=0;

    $max=0;

    for ($i=0; $i < sizeof($units); $i++) { 
        # code...
        $unit=$units[$i];

        if($i==0){
            $min=$unit['price'];
        }

        if($unit['price']>=$max){
            $max=$unit['price'];
        }

        if($unit['price']<=$min){
            $min=$unit['price'];
        }
    }

    $prices->min_price=$min;

    $prices->max_price=$max;

    return $prices;
}


function CreateAccount($con,$title,$type,$email,$phone,$created_by){

    $status="pending";

    $account_id=uniqid();

    $password=password_hash('2026',PASSWORD_DEFAULT);

    $username=strtolower($title);

    $slug=slugify($title);

    $sql="INSERT INTO accounts (account_id,title,type,username,email,phone,password,created_by,slug,status)
     VALUES('$account_id','$title','$type','$username','$email','$phone','$password','$created_by','$slug','$status')";


    if(mysqli_query($con,$sql)){
        return true;
    }else{
        return false;
    }
}