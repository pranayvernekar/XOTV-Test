<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate product object
include_once '../objects/price.php';

$database = new Database();
$db = $database->getConnection();
 
$price = new Price($db);
 
$provider_name = "";
$product_name = "";
$variation_type = "";

if (isset($_GET['provider_name']) && $_GET['provider_name'] != "") {
    $provider_name = $_GET['provider_name'];
} 

if (isset($_GET['product_name']) && $_GET['product_name'] != "") {
    $product_name = $_GET['product_name'];
} 

if (isset($_GET['variation_type']) && $_GET['variation_type'] != "") {
    $variation_type = $_GET['variation_type'];
} 

// get posted data
$data = json_decode(file_get_contents("php://input"));
// make sure data is not empty
if(
    !empty($provider_name) &&
    !empty($product_name) &&
    !empty($variation_type)
){
 
    // set product property values
    $price->provider_name = $provider_name;
    $price->product_name = $product_name;
    $price->variation_type = $variation_type;
    
    $stmt = $price->energy_price_read();
    $num = $stmt->rowCount();
    // check if more than 0 record found
    if($num>0){
     
        // products array
        $categories_arr=array();
        $categories_arr["records"]=array();
     
        // retrieve our table contents
        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract row
            // this will make $row['name'] to
            // just $name only
            $res = $row;
        }
     
        // set response code - 200 OK
        http_response_code(200);
        
        // show categories data in json format
        echo json_encode($res['monthly_price']);
    }
    else{
     
        // set response code - 404 Not found
        http_response_code(404);
     
        // tell the user no categories found
        echo json_encode(
            array("message" => "No price found.")
        );
    }
}
 
// tell the user data is incomplete
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to read price. Data is incomplete."));
}
?>