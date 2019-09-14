<?php
namespace bi\Model;

class COA{
  public $id;
  public $account;
  public $parent;
  public $externalId;

  public function __construct(){

  }

  public function getCRC32(){
    return hash("crc32", $this->id . $this->account . $this->parent . $this->externalId);
  }

}
?>
