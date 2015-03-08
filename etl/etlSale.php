<?php
function etlSeller(){
    $banco = \bi\Model\DB::getConnection("bi");

    $handSale = dbase_open("data/MOVIME0.DBF", 0);
    $numRec   = dbase_numrecords($handSeller);

    $error = array();

    $dateTmp = "";
    $dateSale = new \bi\Model\Date();

    $query = "delete from f_sale";
    if(!$banco->query($query))
        die($banco->error.$query);


    for ($i=1; $i <= $numRec; $i++) {
        $oldSale = dbase_get_record_with_names($handSale, $i);

        if($oldSale["deleted"] == 1 || trim($oldSale["DATA"])=="" || trim($oldSale["TIPOMOV"]=="VE")){
            continue;
        }

        if($oldSale["DATA"]!=$dateTmp){
            $dateTmp = $oldSale["DATA"];
            $dateSale->setTime(0,0,0);
            $dateSale->setDate(substr($dateTmp,0,4),substr($dateTmp,4,2),substr($dateTmp,6,2));
            $query = "SELECT dateid From d_date where year = ".$dateSale->getYear()." and month = ".$dateSale->getMonth()." and day = ".$dateSale->getDay();
            $res = $banco->query($query);
            if(!$res)
                die($banco->error.$query);
            if($res->num_rows()>0){
                $idTmp = ($res->fetch_row())[0]*1;
                $dateSale->setId($idTmp);
            }else{
                $query = "Insert into d_date (dateid, day, month, year, trimester, semester) values 
                (0, ".$dateSale->getDay().", 
                    ".$dateSale->getMonth().", 
                    ".$dateSale->getYear().",
                    ".$dateSale->getTrimester().",
                    ".$dateSale->getSemester().")";
                $res = $banco->query($query);
                if(!$res)
                    die($banco->error.$query);
                $dateSale->setId($banco->insert_id*1);
            }
        }

        $newSale = new \bi\Model\Sale();
        $newSale->setId(0);
        $newSale->setExternalId($banco->escape_string(trim($oldSale["NOTA"])));
        $newSale->setQuantity  ($oldSale["QUANTI"]*1);
        $newSale->setTotal     ($newSale->getQuantity()*$oldSale["PVENDA"]);
        $newSale->setProfit    ($newSale->getTotal()-($newSale->getQuantity()*$oldSale["PCUSTO"]));
        if($oldSale["PRECOLISTA"]>0)
            $newSale->setDiscount  ($oldSale["PRECOLISTA"]-$oldSale["PVENDA"]);
        else
            $newSale->setDiscount(0);

        $query = "INSERT into d_Seller (sellerid, externalid, name)
        values (".$newSeller->getSellerId().",
            '".$newSeller->getExternalId()."',
            '".$newSeller->getName()."')
        on duplicate key update
        name = '".$newSeller->getName()."'";
        $res = $banco->query($query);
        if(!$res){
            die($banco->error."\n".$query."\n");
        }
    }

    if(count($error)>0)
        return $error;
    else
        return true;
}
?>
?>