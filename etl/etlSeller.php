<?php
function etlSeller(){
    $banco = \bi\Model\DB::getConnection("bi");

    $handSeller = dbase_open("data/VENDEDO0.DBF", 0);

    $numRec  = dbase_numrecords($handSeller);

    $error = array();

    for ($i=1; $i <= $numRec; $i++) {
        $oldSeller = dbase_get_record_with_names($handSeller, $i);

        if($oldSeller["deleted"] == 1){
            continue;
        }

        $newSeller = new \bi\Model\Seller();
        $newSeller->setSellerId  ($banco->escape_string(trim($oldSeller["CODIGO"])));
        $newSeller->setExternalId($banco->escape_string(trim($oldSeller["CODIGO"])));
        $newSeller->setName      ($banco->escape_string(trim($oldSeller["NOME"])));

        if($newSeller->getSellerId()=="" || $newSeller->getName() == "")
            continue;


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