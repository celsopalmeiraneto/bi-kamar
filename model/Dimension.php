<?php
namespace bi\Model;

abstract class Dimension implements ITableDataGateway
{
  private $tableName;
  private $columns;
  private $columnsArray = array();
  private $queryableColumns;
  private $queryableColumnsArray = array();

  function __construct($tableName, $columns, $queryableColumns = "")
  {
    $this->tableName = $tableName;

    if ($queryableColumns == "") {
      $queryableColumns = $columns;
    }

    $this->columns = $columns;
    foreach (explode(",", $this->columns) as $key => $value) {
      $this->columnsArray[$key] = trim($value);
    }

    $this->queryableColumns = $queryableColumns;
    foreach (explode(",", $this->queryableColumns) as $key => $value) {
      $this->queryableColumnsArray[$key] = trim($value);
    }
  }

  function getMetadata(){
    $meta = array();
    $meta["table"]     = $this->tableName;
    $meta["columns"]   = $this->columnsArray;
    $meta["queryable"] = $this->queryableColumnsArray;
    return $meta;
  }

  function getDimensionValues($columns){
    if (!is_array($columns) || count($columns) == 0 ) {
      throw new \Exception("Dimensions columns must be filled.", 1);
    }

    $cols = implode(", ", $columns);
    $query = "select $cols from $this->tableName group by $cols order by $cols";

    $conn = DB::getConnection("bi");

    $res = $conn->query($query);
    if(!$res)
      throw new \Exception("Error Processing Request: $conn->error. $query");

    $resTable = $res->fetch_all(MYSQLI_ASSOC);

    return $resTable;
  }

  function getTableName(){
    return $this->tableName;
  }
  function getColumns(){
    return $this->columns;
  }


}

?>
