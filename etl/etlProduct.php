<?php
class ETLProduct extends ETLBase{
    function runETL(){
        $banco = \bi\Model\DB::getConnection("bi");

        $handProduct = dbase_open("data/PRODUT0.DBF", 0);

        $numRec  = dbase_numrecords($handProduct);

        $error = array();

        $fornecedores = array();
        $linhas = array();
        $grupos = array();

        $totalNewRec     = 0;
        $totalUpdateRec  = 0;
        $totalInvalidRec = 0;
        $totalNoChange   = 0;
        $totalRecs       = 0;


        for ($i=1; $i <= $numRec; $i++) {
            $totalRecs++;
            $oldProduct = dbase_get_record_with_names($handProduct, $i);

            if($oldProduct["deleted"] == 1){
                $totalInvalidRec++;
                continue;
            }

            $newProduct = new \bi\Model\Product();
            $newProduct->setProductId(0);
            $newProduct->setExternalId($banco->escape_string(trim($oldProduct["CODIGO"])));
            $newProduct->setName     ($banco->escape_string(trim($oldProduct["DESCRI"])));

            if($newProduct->getExternalId()=="" || $newProduct->getName() == ""){
                $totalInvalidRec++;
                continue;
            }

            $codFabricanteTmp = trim($oldProduct["FABRIC"]);
            if($codFabricanteTmp != "" && !isset($fornecedores[$codFabricanteTmp])){
                $fornecedores[$codFabricanteTmp] = $banco->escape_string($this->buscaFornecedorPorCodigo($codFabricanteTmp));
            }
            if(isset($fornecedores[$codFabricanteTmp])){
                $newProduct->setManufacturer($fornecedores[$codFabricanteTmp]);
            }else{
                $newProduct->setManufacturer("---");
            }

            $codFornecTmp = trim($oldProduct["FORNEC"]);
            if($codFornecTmp != "" && !isset($fornecedores[$codFornecTmp])){
                $fornecedores[$codFornecTmp] = $banco->escape_string($this->buscaFornecedorPorCodigo($codFornecTmp));
            }
            if(isset($fornecedores[$codFornecTmp])){
                $newProduct->setDistributor($fornecedores[$codFornecTmp]);
            }else{
                $newProduct->setDistributor("---");
            }

            $codLinhaTmp = trim($oldProduct["LINHA"]);
            if($codLinhaTmp != "" && !isset($linhas[$codLinhaTmp])){
                $linhas[$codLinhaTmp] = $banco->escape_string($this->buscaLinhaPorCodigo($codLinhaTmp));
            }
            if(isset($linhas[$codLinhaTmp])){
                $newProduct->setLine($linhas[$codLinhaTmp]);
            }else{
                $newProduct->setLine("---");
            }

            $codGrupoTmp = trim($oldProduct["GRUPO"]);
            if($codGrupoTmp != "" && !isset($grupos[$codGrupoTmp])){
                $grupos[$codGrupoTmp] = $banco->escape_string($this->buscaGrupoPorCodigo($codGrupoTmp));
            }
            if(isset($grupos[$codGrupoTmp])){
                $newProduct->setCategory($grupos[$codGrupoTmp]);
            }else{
                $newProduct->setCategory("---");
            }

            $query = "SELECT d_product_productid,crc32 from mgr_d_product where source = '".ETLBase::$SRC_SERVINN."' and externalid = '".$newProduct->getExternalId()."'";
            $res = $banco->query($query);
            if(!$res)
                die($banco->error.$query);
            if($res->num_rows>0){ //This record is already in the database.
                $res = $res->fetch_row();
                if($res[1]!=$newProduct->getCRC32()){ //If the record is different.
                    $newProduct->setProductId($res[0]);
                    $query = "update d_product set 
                    name =  '".$newProduct->getName()."', 
                    manufacturer = '".$newProduct->getManufacturer()."', 
                    distributor = '".$newProduct->getDistributor()."', 
                    category = '".$newProduct->getCategory()."', 
                    line = '".$newProduct->getLine()."'
                    where productid = ".$newProduct->getProductId();
                    $res = $banco->query($query);
                    if(!$res){
                        die($banco->error."\n".$query."\n");
                    }
                    $query = "update mgr_d_product set crc32 = '".$newProduct->getCRC32()."' where d_product_productid=".$newProduct->getProductId();
                    $res = $banco->query($query);
                    if(!$res){
                        die($banco->error."\n".$query."\n");
                    }
                    $totalUpdateRec++;
                }else{
                    $totalNoChange++;
                }
            }else{//This record is not in the database
                $query = "INSERT into d_product (productid, externalid, name, manufacturer, distributor, category, line)
                values (0,
                ".$newProduct->getExternalId().",
                '".$newProduct->getName()."',
                '".$newProduct->getManufacturer()."',
                '".$newProduct->getDistributor()."',
                '".$newProduct->getCategory()."',
                '".$newProduct->getLine()."')";
                $res = $banco->query($query);
                if(!$res){
                    die($banco->error."\n".$query."\n");
                }
                $query = "INSERT into mgr_d_product (d_product_productid, externalid, source, crc32)
                values (".$banco->insert_id.",
                    ".$newProduct->getExternalId().",
                    '".ETLBase::$SRC_SERVINN."',
                    '".$newProduct->getCRC32()."')";
                $res = $banco->query($query);
                if(!$res){
                    die($banco->error."\n".$query."\n");
                }
                $totalNewRec++;
            }
        }

        echo "Product:\n";
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

    private function buscaFornecedorPorCodigo($codigo){
        $retorno = "";
        $handFornec  = dbase_open("data/FORNEC0.DBF", 0);
        $numRec  = dbase_numrecords($handFornec);
        for ($i=0; $i < $numRec; $i++) {
            $fornecedor = dbase_get_record_with_names($handFornec, $i);
            if($fornecedor["CODIGO"]==$codigo){
                $retorno = $fornecedor["FANTASIA"];
            }
        }
        dbase_close($handFornec);
        return $retorno;
    }
    private function buscaGrupoPorCodigo($codigo){
        $retorno = "";
        $handGrupo  = dbase_open("data/GRUPOS0.DBF", 0);
        $numRec  = dbase_numrecords($handGrupo);
        for ($i=0; $i < $numRec; $i++) {
            $grupo = dbase_get_record_with_names($handGrupo, $i);
            if($grupo["CODIGO"]==$codigo){
                $retorno = $grupo["NOME"];
            }
        }
        dbase_close($handGrupo);
        return $retorno;
    }
    private function buscaLinhaPorCodigo($codigo){
        $retorno = "";
        $handLinha  = dbase_open("data/LINHAS0.DBF", 0);
        $numRec  = dbase_numrecords($handLinha);
        for ($i=0; $i < $numRec; $i++) {
            $linha = dbase_get_record_with_names($handLinha, $i);
            if($linha["CODIGO"]==$codigo){
                $retorno = $linha["NOME"];
            }
        }
        dbase_close($handLinha);
        return $retorno;
    }
}
?>