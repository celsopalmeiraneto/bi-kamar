<?php
class ETLSynop extends ETLBase{
    function runETL(){
        $banco = \bi\Model\DB::getConnection("bi");

        $weatherPath = "data/weather/";

        $error = array();

        $dateTmp   = "";
        $dateSynop = new \bi\Model\Date();

        $csvList = scandir($weatherPath);
        foreach ($csvList as $fileName){
            if(strlen($fileName)>4 &&  strtoupper(substr($fileName, 0,4))=="SBPS" && strtoupper(substr($fileName,-4))==".CSV"){
                $handler     = fopen($weatherPath.$fileName,"r");
                $lineCounter = 0;

                $observationsDate = 0;
                $precipitation    = 0;
                $windDirection    = 0;
                $windSpeed        = 0;
                $dryBulb          = 0;
                $dewPoint         = 0;

                while($oldSynop = fgetcsv($handler)){
                    $lineCounter++;
                    if($lineCounter==1)
                        $dateTmp = trim(substr($oldSynop[0],0,10));


                    if(trim(substr($oldSynop[0],0,10))!=$dateTmp){
                        $dateSynop->setTime(0,0,0);
                        $dateSynop->setDate(substr($dateTmp,6,4),substr($dateTmp,3,2),substr($dateTmp,0,2));
                        $query = "SELECT dateid From d_date where year = ".$dateSynop->getYear()." and month = ".$dateSynop->getMonth()." and day = ".$dateSynop->getDay();
                        $res = $banco->query($query);
                        if(!$res)
                            die($banco->error.$query);
                        if($res->num_rows>0){
                            $idTmp = $res->fetch_row();
                            $idTmp = $idTmp[0]*1;
                            $dateSynop->setId($idTmp);
                        }else{
                            $query = "Insert into d_date (dateid, day, month, year, dayOfWeek, trimester, semester) values 
                                (0, 
                                ".$dateSynop->getDay().", 
                                ".$dateSynop->getMonth().", 
                                ".$dateSynop->getYear().",
                                ".$dateSynop->getDayOfWeek().",
                                ".$dateSynop->getTrimester().",                        
                                ".$dateSynop->getSemester().")";
                            $res = $banco->query($query);
                            if(!$res)
                                die($banco->error.$query);
                            $dateSynop->setId($banco->insert_id*1);
                        }

                        $newSynop = new \bi\Model\Synop();
                        $newSynop->setPrecipitation($precipitation);
                        $newSynop->setWindDirection($windDirection/$observationsDate);
                        $newSynop->setWindSpeed($windSpeed/$observationsDate);
                        $newSynop->setDryBulbC($dryBulb/$observationsDate);
                        $newSynop->setDewPointC($dewPoint/$observationsDate);
                        $newSynop->setDateId($dateSynop->getId());
                        $newSynop->setStationId(ETLBase::$DEF_STATION);

                        $query = "SELECT synopid From f_synop 
                        where d_date_dateid = ".$dateSynop->getId()." and d_wstation_wstationid = ".ETLBase::$DEF_STATION;
                        $res = $banco->query($query);
                        if(!$res)
                            die($banco->error.$query);
                        $synopToDeleteList = "";
                        while($synopToDelete = $res->fetch_row()){
                            if(strlen($synopToDeleteList>0))
                                $synopToDeleteList .=", ";
                            $synopToDeleteList .= $synopToDelete;
                        }
                        if(strlen($synopToDeleteList)>0){
                            $query = "Delete from f_synop where synopid in ($synopToDeleteList)";
                            $res = $banco->query($query);
                            if(!$res)
                                die($banco->error.$query);
                        }
                        $query = "Insert into f_synop (synopid, wind_dir, wind_speed, precipitation, dryBulbC, dewPointC, d_wstation_wstationid, d_date_dateid) values 
                            (0,
                            ".$newSynop->getWindDirection().",
                            ".$newSynop->getWindSpeed().",
                            ".$newSynop->getPrecipitation().",
                            ".$newSynop->getDryBulbC().",
                            ".$newSynop->getDewPointC().",
                            ".$newSynop->getStationId().",
                            ".$newSynop->getDateId().")";
                        $res = $banco->query($query);
                        if(!$res)
                            die($banco->error.$query);

                        $dateTmp = trim(substr($oldSynop[0],0,10));
                        $observationsDate = 0;
                        $precipitation    = 0;
                        $windDirection    = 0;
                        $windSpeed        = 0;
                        $dryBulb          = 0;
                        $dewPoint         = 0;

                    }

                    $observationsDate++;
                    $precipitation += $oldSynop[12];
                    $windDirection += $oldSynop[5];
                    $windSpeed     += $oldSynop[6];
                    $dryBulb       += $oldSynop[13];
                    $dewPoint      += $oldSynop[14];
                }
            }
        }
        if(count($error)>0)
            return $error;
        else
            return true;
    }
}
?>