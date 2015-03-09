<?php
class ETLSeller extends ETLBase{
    function runETL(){
        $banco = \bi\Model\DB::getConnection("bi");

        $handSeller = dbase_open("data/VENDEDO0.DBF", 0);

        $numRec  = dbase_numrecords($handSeller);

        $error = array();

        $totalNewRec     = 0;
        $totalUpdateRec  = 0;
        $totalInvalidRec = 0;
        $totalNoChange   = 0;
        $totalRecs       = 0;

        for ($i=1; $i <= $numRec; $i++) {
            $oldSeller = dbase_get_record_with_names($handSeller, $i);
            $totalRecs++;

            if($oldSeller["deleted"] == 1){
                $totalInvalidRec++;
                continue;
            }

            $newSeller = new \bi\Model\Seller();
            $newSeller->setSellerId  (0);
            $newSeller->setExternalId($banco->escape_string(trim($oldSeller["CODIGO"])));
            $newSeller->setName      ($banco->escape_string(trim($oldSeller["NOME"])));

            if($newSeller->getExternalId()=="" || $newSeller->getName() == ""){
                $totalInvalidRec++;
                continue;
            }

            $query = "SELECT d_seller_sellerid,crc32 from mgr_d_seller where source = '".ETLBase::$SRC_SERVINN."' and externalid = '".$newSeller->getExternalId()."'";
            $res = $banco->query($query);
            if(!$res)
                die($banco->error.$query);
            if($res->num_rows>0){ //This record is already on the database.
                $res = $res->fetch_row();
                if($res[1]!=$newSeller->getCRC32()){ //If the record is different.
                    $newSeller->setSellerId($res[0]);
                    $query = "update d_seller set name = '".$newSeller->getName()."' where sellerid = ".$newSeller->getSellerId();
                    $res = $banco->query($query);
                    if(!$res){
                        die($banco->error."\n".$query."\n");
                    }
                    $query = "update mgr_d_seller set crc32 = '".$newSeller->getCRC32()."' where d_seller_sellerid=".$newSeller->getSellerId();
                    $res = $banco->query($query);
                    if(!$res){
                        die($banco->error."\n".$query."\n");
                    }
                    $totalUpdateRec++;
                }else{
                    $totalNoChange++;
                }
            }else{ //This record is not on the database.
                $query = "INSERT into d_Seller (sellerid, externalid, name)
                values (0,
                    '".$newSeller->getExternalId()."',
                    '".$newSeller->getName()."')";
                $res = $banco->query($query);
                if(!$res){
                    die($banco->error."\n".$query."\n");
                }
                $query = "INSERT into mgr_d_seller (d_seller_sellerid, externalid, source, crc32)
                values (".$banco->insert_id.",
                    ".$newSeller->getExternalId().",
                    '".ETLBase::$SRC_SERVINN."',
                    '".$newSeller->getCRC32()."')";
                $res = $banco->query($query);
                if(!$res){
                    die($banco->error."\n".$query."\n");
                }
                $totalNewRec++;
            }
        }

        echo "Seller:\n";
        echo "New Rec: $totalNewRec\n";
        echo "Upd Rec: $totalUpdateRec\n";
        echo "Inv Rec: $totalInvalidRec\n";
        echo "NoC Rec: $totalNoChange\n";
        echo "Total Recs: $totalRecs\n";

        if(count($error)>0)
            return $error;
        else
            return true;
    }

}
?>