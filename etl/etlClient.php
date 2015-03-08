<?php
function etlClient(){

    $banco = \bi\Model\DB::getConnection("bi");
    $cep   = \bi\Model\DB::getConnection("cep");

    $handler = dbase_open("data/CLIENTE0.DBF", 0);
    $numRec  = dbase_numrecords($handler);

    $error = array();

    for ($i=1; $i <= $numRec; $i++) {
        $x = dbase_get_record_with_names($handler, $i);

        if($x["deleted"] == 1){
            continue;
        }
        $y = new \bi\Model\Client();
        $y->setId(          $banco->escape_string(trim($x["CODIGO"])));
        $y->setName(        $banco->escape_string(trim($x["FANTASIA"])));
        $y->setCity(        $banco->escape_string(trim($x["CIDADE"])));
        $y->setNeighborhood($banco->escape_string(trim($x["BAIRRO"])));
        $y->setZip(         $banco->escape_string(trim($x["CEP"])));
        $y->setCountry(     $banco->escape_string(trim($x["PAIS"])));
        $y->setState(       $banco->escape_string(trim($x["UF"])));

        if(strlen($y->getId())==0){
            continue;
        }

        $cityInfo = getCityByNameAndState($y->getCity(),$y->getState());
        if(!$cityInfo && strlen($cepTmp = $y->getZip(true))>0){
            $cityInfo = getCityByZip($cepTmp);
        }

        if(!$cityInfo){
            $y->setCity("---");
            $y->setState("---");
            $y->setZip("---");
            $y->setDistrict("---");
            $error[] = $x;
        }else{
            if(strlen($cityInfo["cidPai"])>0){
                $y->setCity($cityInfo["cidPai"]);
                $y->setDistrict($cityInfo["cid"]);
            }else{
                $y->setCity($cityInfo["cid"]);
                $y->setDistrict("---");
            }
            if($y->getZip(true)<=0){
                $y->setZip("---");
            }
        }

        $query = "INSERT into d_client (clientid, externalid, city, name, neighborhood, district, zip, country, state)
        values (".$y->getId().", "
            .$y->getId().", '"
            .$y->getCity()."', '"
            .$y->getName()."', '"
            .$y->getNeighborhood()."', '"
            .$y->getDistrict()."', '"
            .$y->getZip()."', '"
            .$y->getCountry()."', '"
            .$y->getState()."') 
        on duplicate key update
        city = '".$y->getCity()."', 
        name =  '".$y->getName()."', 
        neighborhood = '".$y->getNeighborhood()."', 
        district = '".$y->getDistrict()."', 
        zip = '".$y->getZip()."', 
        country = '".$y->getCountry()."', 
        state = '".$y->getState()."'";

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

function getCityByNameAndState($name,$state){
    $cep = bi\Model\DB::getConnection("cep");
    //Searching by City Name and State
    $query = "SELECT l1.loc_nosub as cid,l1.cep as cep,l2.loc_nosub as cidPai
    FROM log_localidade l1
    Left Join log_localidade l2 on l2.loc_nu_sequencial = l1.loc_nu_sequencial_sub
    WHERE l1.loc_nosub = '$name' and l1.ufe_sg='$state' ";

    $res   = $cep->query($query);
    if(!$res){
        die($banco->errno." - ".$banco->error."\n".$query."\n");
    }
    //If found one city...
    if($res->num_rows==1){
        $DNEres = $res->fetch_assoc();
        $cep->escapeObject($DNEres);
        return $DNEres;
    }
    return false;
}


function getCityByZip($zip){

    $cepDB = bi\Model\DB::getConnection("cep");

    $query = "SELECT l1.loc_nosub as cid, l2.loc_nosub as cidPai
    From log_grande_usuario   lgu
    Inner Join log_localidade l1 on l1.loc_nu_sequencial = lgu.loc_nu_sequencial
    Left Join  log_localidade l2 on l2.loc_nu_sequencial = l1.loc_nu_sequencial_sub
    Where lgu.cep = '$zip'
    
    Union All
    SELECT l1.loc_nosub as cid, l2.loc_nosub as cidPai
    From log_localidade  l1
    Left Join  log_localidade l2 on l2.loc_nu_sequencial = l1.loc_nu_sequencial_sub
    Where l1.cep = '$zip'
    
    Union All
    
    SELECT l1.loc_nosub as cid, l2.loc_nosub as cidPai
    From log_logradouro   llg
    Inner Join log_localidade l1 on l1.loc_nu_sequencial = llg.loc_nu_sequencial
    Left Join  log_localidade l2 on l2.loc_nu_sequencial = l1.loc_nu_sequencial_sub
    Where llg.cep = '$zip'
    
    Union All
    
    SELECT l1.loc_nosub as cid, l2.loc_nosub as cidPai
    From log_unid_oper  luo
    Inner Join log_localidade l1 on l1.loc_nu_sequencial = luo.loc_nu_sequencial
    Left Join  log_localidade l2 on l2.loc_nu_sequencial = l1.loc_nu_sequencial_sub
    Where luo.cep = '$zip'";
    $res   = $cepDB->query($query);
    if(!$res){
        die($banco->errno." - ".$banco->error."\n".$query."\n");
    }
    if($res->num_rows>0){
        $DNEres = $res->fetch_assoc();
        return $DNEres;
    }
    return false;
}

?>