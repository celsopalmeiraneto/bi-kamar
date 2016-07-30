<?php
namespace bi\Model;
class Inventory{
    protected $productId;
    protected $amount;


    public function getProductId() {
        return $this->productId;
    }
    public function setProductId($productId) {
        $this->productId = $productId;
        return $this;
    }

    public function getAmount() {
        return $this->amount;
    }
    public function setAmount($amount) {
        $this->amount = $amount;
        return $this;
    }


}
?>
