<?php
namespace bi\Model;
class Seller{
    protected $sellerId;
    protected $externalId;
    protected $name;

    public function getSellerId() {
        return $this->sellerId;
    }
    public function setSellerId($sellerId) {
        $this->sellerId = $sellerId;
        return $this;
    }

    public function getExternalId() {
        return $this->externalId;
    }
    public function setExternalId($externalId) {
        $this->externalId = $externalId;
        return $this;
    }

    public function getName() {
        return $this->name;
    }
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getCRC32(){
        $string = "";
        $string .= trim($this->externalId).trim($this->name);
        return hash("crc32", $string);
    }
}
?>