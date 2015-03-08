<?php
function etlProduct(){
    $banco = \bi\Model\DB::getConnection("bi");

    $handProduct = dbase_open("data/PRODUT0.DBF", 0);

    $numRec  = dbase_numrecords($handProduct);

    $error = array();

    $fornecedores = array();
    $linhas = array();
    $grupos = array();

    for ($i=1; $i <= $numRec; $i++) {
        $oldProduct = dbase_get_record_with_names($handProduct, $i);

        if($oldProduct["deleted"] == 1){
            continue;
        }

        $newProduct = new \bi\Model\Product();
        $newProduct->setProductId($banco->escape_string(trim($oldProduct["CODIGO"])));
        $newProduct->setName     ($banco->escape_string(trim($oldProduct["DESCRI"])));

        if($newProduct->getProductId()=="" || $newProduct->getName() == "")
            continue;

        $codFabricanteTmp = trim($oldProduct["FABRIC"]);
        if($codFabricanteTmp != "" && !isset($fornecedores[$codFabricanteTmp])){
            $fornecedores[$codFabricanteTmp] = $banco->escape_string(buscaFornecedorPorCodigo($codFabricanteTmp));
        }
        if(isset($fornecedores[$codFabricanteTmp])){
            $newProduct->setManufacturer($fornecedores[$codFabricanteTmp]);
        }else{
            $newProduct->setManufacturer("---");
        }

        $codFornecTmp = trim($oldProduct["FORNEC"]);
        if($codFornecTmp != "" && !isset($fornecedores[$codFornecTmp])){
            $fornecedores[$codFornecTmp] = $banco->escape_string(buscaFornecedorPorCodigo($codFornecTmp));
        }
        if(isset($fornecedores[$codFornecTmp])){
            $newProduct->setDistributor($fornecedores[$codFornecTmp]);
        }else{
            $newProduct->setDistributor("---");
        }

        $codLinhaTmp = trim($oldProduct["LINHA"]);
        if($codLinhaTmp != "" && !isset($linhas[$codLinhaTmp])){
            $linhas[$codLinhaTmp] = $banco->escape_string(buscaLinhaPorCodigo($codLinhaTmp));
        }
        if(isset($linhas[$codLinhaTmp])){
            $newProduct->setLine($linhas[$codLinhaTmp]);
        }else{
            $newProduct->setLine("---");
        }

        $codGrupoTmp = trim($oldProduct["GRUPO"]);
        if($codGrupoTmp != "" && !isset($grupos[$codGrupoTmp])){
            $grupos[$codGrupoTmp] = $banco->escape_string(buscaGrupoPorCodigo($codGrupoTmp));
        }
        if(isset($grupos[$codGrupoTmp])){
            $newProduct->setCategory($grupos[$codGrupoTmp]);
        }else{
            $newProduct->setCategory("---");
        }

        $query = "INSERT into d_product (productid, name, manufacturer, distributor, category, line)
        values (".$newProduct->getProductId().",
            '".$newProduct->getName()."',
            '".$newProduct->getManufacturer()."',
            '".$newProduct->getDistributor()."',
            '".$newProduct->getCategory()."',
            '".$newProduct->getLine()."')
        on duplicate key update
        name =  '".$newProduct->getName()."', 
        manufacturer = '".$newProduct->getManufacturer()."', 
        distributor = '".$newProduct->getDistributor()."', 
        category = '".$newProduct->getCategory()."', 
        line = '".$newProduct->getLine()."'";
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

function buscaFornecedorPorCodigo($codigo){
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
function buscaGrupoPorCodigo($codigo){
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
function buscaLinhaPorCodigo($codigo){
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

?>