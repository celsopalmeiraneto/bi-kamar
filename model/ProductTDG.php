<?php
namespace bi\Model;

/**
 * Product Table Data Gateway
 */
class ProductTDG extends Dimension
{

  function __construct()
  {
    $tableName = "d_product";
    $columns   = "productid, externalid, name, manufacturer, distributor, category, line";
    $queryableColumns = "name, manufacturer, distributor, category, line";
    parent::__construct($tableName, $columns, $queryableColumns);
  }


  public function find($id){
    $query = "select ".$this->getColumns()."
              from d_product where productid = ".$id;
    $conn = DB::getConnection("bi");


    $res = $conn->query($query);


    if(!$res)
      throw new \Exception("Error Processing Request. ".$conn->error);

    if($res->num_rows == 0)
      throw new \Exception("Product not found");


    $row = $res->fetch_row();

    $product = new Product();
    $product->setProductId($row[0]);
    $product->setExternalId($row[1]);
    $product->setName($row[2]);
    $product->setManufacturer($row[3]);
    $product->setDistributor($row[4]);
    $product->setCategory($row[5]);
    $product->setLine($row[6]);

    return $product;
  }

  public function getAll(){
    $query = "select ".$this->getColumns()."
              from d_product";
    $conn = DB::getConnection("bi");

    $res = $conn->query($query);
    if(!$res)
      throw new \Exception("Error Processing Request. ".$conn->error);

    $products = array();
    while ($row = $res->fetch_row()) {
      $product = new Product();
      $product->setProductId($row[0]);
      $product->setExternalId($row[1]);
      $product->setName($row[2]);
      $product->setManufacturer($row[3]);
      $product->setDistributor($row[4]);
      $product->setCategory($row[5]);
      $product->setLine($row[6]);
      $products[] = $product;
    }
    return $products;
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
