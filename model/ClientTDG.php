<?php
namespace bi\Model;

/**
 * Client Table Data Gateway
 */
class ClientTDG extends Dimension
{



  function __construct()
  {
    $tableName = "d_client";
    $columns   = "clientid, externalid, name, city, neighborhood, zip, country, state, district, ibge, mesoregion, microregion";
    $queryableColumns = "name, city, neighborhood, zip, country, state, district, mesoregion, microregion";
    parent::__construct($tableName, $columns, $queryableColumns);
  }


  public function find($id){
    $query = "select ".$this->getColumns()."
              from ".$this->getTableName()." where clientid = $id";
    $conn = DB::getConnection("bi");

    $res = $conn->query($query);


    if(!$res)
      throw new \Exception("Error Processing Request. $conn->error - $query");

    if($res->num_rows == 0)
      throw new \Exception("Client not found");


    $row = $res->fetch_row();

    $client = new Client();
    $client->setId($row[0]);
    $client->setExternalId($row[1]);
    $client->setName($row[2]);
    $client->setCity($row[3]);
    $client->setNeighborhood($row[4]);
    $client->setZip($row[5]);
    $client->setCountry($row[6]);
    $client->setDistrict($row[7]);

    return $client;
  }

  public function getAll(){
    $query = "select ".$this->getColumns()."
              from ".$this->getTableName();
    $conn = DB::getConnection("bi");

    $res = $conn->query($query);
    if(!$res)
      throw new \Exception("Error Processing Request. ".$conn->error);

    $clients = array();
    while ($row = $res->fetch_row()) {
      $client = new Client();
      $client->setId($row[0]);
      $client->setExternalId($row[1]);
      $client->setName($row[2]);
      $client->setCity($row[3]);
      $client->setNeighborhood($row[4]);
      $client->setZip($row[5]);
      $client->setCountry($row[6]);
      $client->setDistrict($row[7]);
      $clients[] = $client;
    }
    return $clients;
  }


  public function delete($id){
    throw new Exception("Method not yet implemented.", 1);
  }
  public function insert($object){
    throw new Exception("Method not yet implemented.", 1);
  }
  public function update($id, $object){
    throw new Exception("Method not yet implemented.", 1);
  }


}


?>
