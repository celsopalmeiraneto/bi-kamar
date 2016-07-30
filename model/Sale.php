<?php
namespace bi\Model;
class Sale{
    protected $id;
    protected $externalId;
    protected $quantity;
    protected $total;
    protected $profit;
    protected $discount;

//////Support attributes!
    protected $dateId;
    protected $sellerId;
    protected $productId;
    protected $clientId;


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

//////Support Methods
    public function getDateId() {
        return $this->dateId;
    }
    public function setDateId($dateId) {
        $this->dateId = $dateId;
        return $this;
    }

    public function getSellerId() {
        return $this->sellerId;
    }
    public function setSellerId($sellerId) {
        $this->sellerId = $sellerId;
        return $this;
    }

    public function getProductId() {
        return $this->productId;
    }
    public function setProductId($productId) {
        $this->productId = $productId;
        return $this;
    }

    public function getClientId() {
        return $this->clientId;
    }
    public function setClientId($clientId) {
        $this->clientId = $clientId;
        return $this;
    }
}
?>