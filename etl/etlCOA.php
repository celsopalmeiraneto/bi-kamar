<?php
class ETLClient extends ETLBase{

    function runETL(){

        $banco = \bi\Model\DB::getConnection("bi");

        $handlerPlanoDeContas = dbase_open("data/OPERACO0.DBF", 0);
        $handlerGrupoCaixa    = dbase_open("data/GRUCAI0.DBF", 0);
        $numRecPlanoContas    = dbase_numrecords($handlerPlanoDeContas);

        $results = new ETLResults();

        for ($i=1; $i <= $numRecPlanoContas; $i++) {
            $recPlanoContas = dbase_get_record_with_names($handler, $i);

            $codGrupoCaixa = $this->existsGrupoDeCaixa($recPlanoContas["GRUPO"]);
            if($codGrupoCaixa){
              updateGrupoDeCaixa($codGrupoCaixa);
            }else{
              $codGrupoCaixa = insertGrupoDeCaixa($codGrupoCaixa);
            }

            $newCOA = new \bi\Model\COA();
            $newCOA->parent     = $codGrupoCaixa;
            $newCOA->account    = $recPlanoContas["DESCRICAO"];
            $newCOA->externalId = $recPlanoContas["CODIGO"];

            if(existsPlanoDeContas($newCOA)){
              try {
                $results->addUpdatedRec(updatePlanoDeContas($newCOA));
              } catch (\Exception $e) {
                $results->addInvalidRec(array($newCOA, $e));
              }
            }else{
              try {
                $results->addNewRec(insertPlanoDeContas($newCOA));
              } catch (Exception $e) {
                $results->addInvalidRec(array($newCOA, $e));
              }
            }
        }

        echo "COA:\n";
        echo "New Rec: $totalNewRec\n";
        echo "Upd Rec: $totalUpdateRec\n";
        echo "Inv Rec: $totalInvalidRec\n";
        echo "NoC Rec: $totalNoChange\n";
        echo "Total Recs: $totalRecs\n";

    }

    private function existsGrupoDeCaixa($codGrupo){
      $banco = \bi\Model\DB::getConnection("bi");
      $res = $banco->query("select coaid from d_coa where externalid = 'G$codGrupo'");

      if(!$res || $res->num_rows == 0)
        return false;

      return ($res->fetch_row())[0];
    }

}
?>
