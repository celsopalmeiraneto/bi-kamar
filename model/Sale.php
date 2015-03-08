<?php
namespace bi\Model;
class Sale{
    protected $id;
    protected $externalId;
    protected $quantity;
    protected $total;
    protected $profit;
    protected $discount;


    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getExternalId() {
        return $this->externalId;
    }
    public function setExternalId($externalId) {
        $this->externalId = $externalId;
        return $this;
    }

    public function getQuantity() {
        return $this->quantity;
    }
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
        return $this;
    }

    public function getTotal() {
        return $this->total;
    }
    public function setTotal($total) {
        $this->total = $total;
        return $this;
    }

    public function getProfit() {
        return $this->profit;
    }
    public function setProfit($profit) {
        $this->profit = $profit;
        return $this;
    }

    public function getDiscount() {
        return $this->discount;
    }
    public function setDiscount($discount) {
        $this->discount = $discount;
        return $this;
    }
}
?>