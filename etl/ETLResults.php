<?php
class ETLResults{
  private $newRecs;
  private $updatedRecs;
  private $noChange;
  private $invalidRecs;

  function __construct(){
    $this->newRecs     = array();
    $this->updatedRecs = array();
    $this->noChange    = array();
    $this->invalidRecs = array();
  }

  public function addNewRec($record){
    $this->newRecs[] = $record;
  }

  public function addUpdatedRec($record){
    $this->updatedRecs[] = $record;
  }

  public function addNoChange($record){
    $this->noChange[] = $record;
  }

  public function addInvalidRec($record){
    $this->invalidRecs[] = $record;
  }

  public function getTotalRecs(){
    return count($this->newRecs) + count($this->updatedRecs) + count($this->noChange) + count($this->invalidRecs);
  }

  public function __toString(){
  }



}
?>
