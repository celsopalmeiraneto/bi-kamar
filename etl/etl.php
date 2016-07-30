<?php
date_default_timezone_set("America/Sao_Paulo");

include '../model/DB.php';
include '../model/Client.php';
include '../model/Product.php';
include '../model/Seller.php';
include '../model/Sale.php';
include '../model/Inventory.php';
include '../model/Date.php';
include '../model/Synop.php';
include '../model/WeatherStation.php';
include 'ETLBase.php';
include 'ETLClient.php';
include 'ETLProduct.php';
include 'ETLSeller.php';
include 'ETLInventory.php';
include 'ETLSale.php';
include 'ETLSynop.php';


/*
$retClient = etlClient();
if($retClient !== true){
    writeCSVError("client.csv",$retClient);
}
//etlProduct();
*/
//(new ETLSeller())->runETL();
//(new ETLProduct())->runETL();
//(new ETLClient())->runETL();
//(new ETLSale())->runETL();
(new ETLInventory())->runETL();

//(new ETLSynop())->runETL();


function writeCSVError($file,$data){
    $handler = fopen($file,"w");
    foreach ($data as $value) {
        fputcsv($handler,$value);
    }
    fclose($handler);
}


?>
