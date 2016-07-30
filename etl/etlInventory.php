<?php
class ETLInventory extends ETLBase{
    function runETL(){
        $banco = \bi\Model\DB::getConnection("bi");

        $handInventory = dbase_open("data/ESTOQUE0.DBF", 0);

        $numRec  = dbase_numrecords($handInventory);

        $error = array();

        $totalNewRec     = 0;
        $totalUpdateRec  = 0;
        $totalInvalidRec = 0;
        $totalNoChange   = 0;
        $totalRecs       = 0;

        for ($i=1; $i <= $numRec; $i++) {
            $dbfEstoque = dbase_get_record_with_names($handInventory, $i);
            $totalRecs++;

            if($dbfEstoque["deleted"] == 1){
                $totalInvalidRec++;
                continue;
            }


            $newInventory = new \bi\Model\Inventory();

            $query  = "SELECT d_product_productid from mgr_d_product where source = '".ETLBase::$SRC_SERVINN."' and externalid = '".$dbfEstoque["CODIGO"]."'";
            $res = $banco->query($query);
            if(!$res)
                die($banco->error.$query);

            if ($res->num_rows > 0) {
              $res = $res->fetch_row();
              $newInventory->setProductId($res[0]);
              $newInventory->setAmount($dbfEstoque["ESTOREAL"]);

              $query = "INSERT INTO f_inventory(d_product_productid, amount)  VALUES (".$newInventory->getProductId().", ".$newInventory->getAmount().") ON DUPLICATE KEY UPDATE amount = ".$newInventory->getAmount();
              $res = $banco->query($query);
              if(!$res){
                  die($banco->error."\n".$query."\n");
              }
              $totalUpdateRec++;
            }else{
              $totalInvalidRec++;
            }
        }

        echo "Inventory:\n";
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
