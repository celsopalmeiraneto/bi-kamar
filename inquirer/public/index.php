<?php
namespace bi\Inquirer;
include '../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



/////Configuring the Application
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;



/////Business Stuff!
$app = new \Slim\App(["settings" => $config]);

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});

$app->get('/dimension/client', function(Request $request, Response $response){
  $clientTDG = new \bi\Model\ClientTDG();
  $data = $clientTDG->getAll();
  return $response->withJSON($data);
});

$app->get('/dimension/client/meta', function(Request $request, Response $response){
  $clientTDG = new \bi\Model\ClientTDG();
  $data = $clientTDG->getMetadata();
  return $response->withJSON($data);
});

$app->get('/dimension/client/group', function(Request $request, Response $response){
  $cols = $request->getQueryParams();
  $clientTDG = new \bi\Model\ClientTDG();
  $data = $clientTDG->getDimensionValues($cols["by"]);
  return $response->withJSON($data);
});

$app->get('/dimension/client/{id}', function(Request $request, Response $response, $args){
  $clientTDG = new \bi\Model\ClientTDG();
  $data = $clientTDG->find($args["id"]);
  return $response->withJSON($data);
});

$app->get('/dimension/product', function(Request $request, Response $response){
  $productTDG = new \bi\Model\ProductTDG();
  $data = $productTDG->getAll();
  return $response->withJSON($data);
});

$app->get('/dimension/product/meta', function(Request $request, Response $response){
  $productTDG = new \bi\Model\ProductTDG();
  $data = $productTDG->getMetadata();
  return $response->withJSON($data);
});

$app->get('/dimension/product/group', function(Request $request, Response $response){
  $cols = $request->getQueryParams();
  $productTDG = new \bi\Model\ProductTDG();
  $data = $productTDG->getDimensionValues($cols["by"]);
  return $response->withJSON($data);
});

$app->get('/dimension/product/{id}', function(Request $request, Response $response, $args){
  $productTDG = new \bi\Model\ProductTDG();
  $data = $productTDG->find($args["id"]);
  return $response->withJSON($data);
});


$app->get('/fact/sale', function(Request $request, Response $response, $args){
  $years = "2013,2014,2015,2015";
  $month = "7";

  $db = \bi\Model\DB::getConnection("bi");

  //Buscando STD DEV
  $query = "SELECT stddev_samp(total) from (select sum(total)  as total
  	from f_sale s
      inner join d_date d on d.dateid = s.d_date_dateid
      where d.year in ($years) and d.month = $month
  	group by externalid) a";

  $res = $db->query($query);
  if (!$res) {
    throw new Exception("Falha ao buscar stddev. ".$query, 1);
  }
  $stddev = $res->fetch_row()[0];

  //AVG
  $query = "SELECT avg(total) from (
  	select externalid, sum(total) as total
  		from f_sale s
  		inner join d_date d on d.dateid = s.d_date_dateid
  		where d.year in ($years) and d.month = $month
  		group by externalid) as a";
  $res = $db->query($query);
  if (!$res) {
    throw new Exception("Falha ao buscar average. ".$query, 1);
  }
  $avg = $res->fetch_row()[0];

  $query = "SELECT externalid, sum(total) as total
    		from f_sale s
    		inner join d_date d on d.dateid = s.d_date_dateid
    		where d.year in ($years) and d.month = $month
    		group by externalid";
  $res = $db->query($query);
  if (!$res) {
    throw new Exception("Falha ao buscar sample. ".$query, 1);
  }

  $sample = $res->fetch_all(MYSQLI_ASSOC);

  foreach ($sample as $key => $value) {
    $zscore = ($value["total"] - $avg) / $stddev;
    $sample[$key]["zscore"] = $zscore;
  }

  return $response->withJSON($sample);
});




$app->run();


?>
