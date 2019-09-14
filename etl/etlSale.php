<?php
class ETLSale extends ETLBase{
    function runETL(){
        $banco = \bi\Model\DB::getConnection("bi");

        $handSale = dbase_open("data/MOVIME0.DBF", 0);
        $numRec   = dbase_numrecords($handSale);

        $error = array();

        $dateTmp = "";
        $dateSale = new \bi\Model\Date();

        $clientIdTmp  = null;
        $clientSale   = 0;

        $productIdTmp = null;
        $productSale  = 0;

        $sellerIdTmp  = null;
        $sellerSale   = 0;

        $query = "delete from f_sale";
        if(!$banco->query($query))
            die($banco->error.$query);

        for ($i=1; $i <= $numRec; $i++) {

            if($i%1000==0)
                echo $i." / ".$numRec."\n";

            $oldSale = dbase_get_record_with_names($handSale, $i);

            //Basic conditions for ETL
            if($oldSale["deleted"] == 1 || trim($oldSale["DATA"])=="" || trim($oldSale["CODIGO"])==""){
                continue;
            }

            //Importing only sales and returns.
            if(strpos("DV, VE", trim($oldSale["TIPOMOV"]))===false){
              continue;
            }

            $newSale = new \bi\Model\Sale();
            $newSale->setId(0);
            $newSale->setExternalId($banco->escape_string(trim($oldSale["NOTA"])));
            $newSale->setQuantity  ($oldSale["QUANTI"]*1);
            $newSale->setTotal     ($newSale->getQuantity()*$oldSale["PVENDA"]);
            $newSale->setProfit    ($newSale->getTotal()-($newSale->getQuantity()*$oldSale["PCUSTO"]));
            $newSale->setIsReturn  ($oldSale["TIPOMOV"]=="DV"?true:false);
            if($oldSale["PRECOLISTA"]>0)
                $newSale->setDiscount  ($oldSale["PRECOLISTA"]-$oldSale["PVENDA"]);
            else
                $newSale->setDiscount(0);

            if($newSale->getIsReturn()){
              $newSale->setQuantity($newSale->getQuantity()*-1);
              $newSale->setTotal($newSale->getTotal()*-1);
              $newSale->setProfit($newSale->getProfit()*-1);
              $newSale->setDiscount($newSale->getDiscount()*-1);
            }


            if($oldSale["DATA"]!=$dateTmp){
                $dateTmp = $oldSale["DATA"];
                $dateSale->setTime(0,0,0);
                $dateSale->setDate(substr($dateTmp,0,4),substr($dateTmp,4,2),substr($dateTmp,6,2));
                $query = "SELECT dateid From d_date where year = ".$dateSale->getYear()." and month = ".$dateSale->getMonth()." and day = ".$dateSale->getDay();
                $res = $banco->query($query);
                if(!$res)
                    die($banco->error.$query);
                if($res->num_rows>0){
                    $idTmp = $res->fetch_row();
                    $idTmp = $idTmp[0]*1;
                    $dateSale->setId($idTmp);
                }else{
                    $query = "Insert into d_date (dateid, day, month, year, dayOfWeek, trimester, semester) values
                        (0,
                        ".$dateSale->getDay().",
                        ".$dateSale->getMonth().",
                        ".$dateSale->getYear().",
                        ".$dateSale->getDayOfWeek().",
                        ".$dateSale->getTrimester().",
                        ".$dateSale->getSemester().")";
                    $res = $banco->query($query);
                    if(!$res)
                        die($banco->error.$query);
                    $dateSale->setId($banco->insert_id*1);
                }
            }
            $newSale->setDateId($dateSale->getId());

            if($oldSale["CLIENTE"] != $clientIdTmp){
                $clientIdTmp = $oldSale["CLIENTE"];
                $clientSale  = $this->searchClientId($clientIdTmp);
            }
            $newSale->setClientId($clientSale);

            if($oldSale["CODIGO"] != $productIdTmp){
                $productIdTmp = $oldSale["CODIGO"];
                $productSale  = $this->searchProductId($productIdTmp);
            }
            $newSale->setProductId($productSale);

            if($oldSale["VENDEDOR"] != $sellerIdTmp){
                $sellerIdTmp = $oldSale["VENDEDOR"];
                $sellerSale  = $this->searchSellerId($sellerIdTmp);
            }
            $newSale->setSellerId($sellerSale);

            $query = "INSERT INTO `f_sale`
            (`salesid`,
            `externalid`,
            `quantity`,
            `total`,
            `profit`,
            `discount`,
            is_return,
            `d_date_dateid`,
            `d_seller_sellerid`,
            `d_product_productid`,
            `d_client_clientid`)
            VALUES
            (0,
            '".$newSale->getExternalId()."',
            ".$newSale->getQuantity().",
            ".$newSale->getTotal().",
            ".$newSale->getProfit().",
            ".$newSale->getDiscount().",
            ".($newSale->getIsReturn()?1:0).",
            ".$newSale->getDateId().",
            ".$newSale->getSellerId().",
            ".$newSale->getProductId().",
            ".$newSale->getClientId().")";

            $res = $banco->query($query);
            if(!$res)
                die($banco->error.$query);



        }
        if(count($error)>0)
            return $error;
        else
            return true;
    }
    private function searchClientId($externalId){
        $banco = \bi\Model\DB::getConnection("bi");
        if(trim($externalId)=="")
            return ETLBase::$NF_CLIENT;
        $query = "select d_client_clientid from mgr_d_client where source = '".ETLBase::$SRC_SERVINN."' and externalid = ".$banco->escape_string($externalId);
        $res = $banco->query($query);
        if(!$res)
            die($banco->error.$query);
        if($res->num_rows==1){
            $res = $res->fetch_row();
            $clientSale = $res[0];
        }else{
            $clientSale = ETLBase::$NF_CLIENT;
        }
        return $clientSale;
    }
    private function searchProductId($externalId){
        $banco = \bi\Model\DB::getConnection("bi");
        if(trim($externalId)=="")
            return ETLBase::$NF_PRODUCT;
        $query = "select d_product_productid from mgr_d_product where source = '".ETLBase::$SRC_SERVINN."' and externalid = ".$banco->escape_string($externalId);
        $res = $banco->query($query);
        if(!$res)
            die($banco->error.$query);
        if($res->num_rows==1){
            $res = $res->fetch_row();
            $productSale = $res[0];
        }else{
            $productSale = ETLBase::$NF_PRODUCT;
        }
        return $productSale;
    }
    private function searchSellerId($externalId){
        $banco = \bi\Model\DB::getConnection("bi");
        if(trim($externalId)=="")
            return ETLBase::$NF_SELLER;
        $query = "select d_seller_sellerid from mgr_d_seller where source = '".ETLBase::$SRC_SERVINN."' and  externalid = '".$banco->escape_string($externalId)."'";
        $res = $banco->query($query);
        if(!$res)
            die($banco->error.$query);
        if($res->num_rows==1){
            $res = $res->fetch_row();
            $sellerSale = $res[0];
        }else{
            $sellerSale = ETLBase::$NF_SELLER;
        }
        return $sellerSale;
    }
}
?>
