<?php
date_default_timezone_set("America/New_York");

include '../model/DB.php';
include '../model/Client.php';
include '../model/Product.php';
include '../model/Seller.php';
include '../model/Sale.php';
include '../model/Date.php';
include 'etlClient.php';
include 'etlProduct.php';
include 'etlSeller.php';
include 'etlSale.php';


$retClient = etlClient();
if($retClient !== true){
    writeCSVError("client.csv",$retClient);
}
etlProduct();
etlSeller();

function writeCSVError($file,$data){
    $handler = fopen($file,"w");
    foreach ($data as $value) {
        fputcsv($handler,$value);
    }
    fclose($handler);
}

?>