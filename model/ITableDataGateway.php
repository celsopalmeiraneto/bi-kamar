<?php
namespace bi\Model;

interface ITableDataGateway{
  public function find($id);
  public function delete($id);
  public function insert($object);
  public function update($id, $object);
}
?>
