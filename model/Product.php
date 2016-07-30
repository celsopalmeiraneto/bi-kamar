<?php
namespace bi\Model;

class Product{
    protected $productId;
    protected $externalId;
    protected $name;
    protected $manufacturer;
    protected $distributor;
    protected $category;
    protected $line;


    public function getProductId() {
        return $this->productId;
    }
    public function setProductId($productId) {
        $this->productId = $productId;
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

    public function getManufacturer() {
        return $this->manufacturer;
    }
    public function setManufacturer($manufacturer) {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    public function getDistributor() {
        return $this->distributor;
    }
    public function setDistributor($distributor) {
        $this->distributor = $distributor;
        return $this;
    }

    public function getCategory() {
        return $this->category;
    }
    public function setCategory($category) {
        $this->category = $category;
        return $this;
    }

    public function getLine() {
        return $this->line;
    }
    public function setLine($line) {
        $this->line = $line;
        return $this;
    }

    public function getCRC32(){
        $string = "";
        $string .= $this->externalId.$this->name.$this->manufacturer.$this->distributor.$this->category.$this->line;
        return hash("crc32", $string);
    }

}
?>