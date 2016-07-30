<?php
namespace bi\Inquirer;
include 'loader.php';
if(isset($_GET["years"])){
    $years = $_GET["years"];
    if(is_array($years) && count($years)>0){

        sort($years);

        $data = array_fill(1, 12, array_fill(0,count($years),0));

        $db = \bi\model\DB::getConnection('bi');

        $where = " where ";
        for ($i=0; $i < count($years); $i++) {
            if($i>0)
                $where .= " or "; 
            $where .= " dt.year = ".$years[$i]." ";
        }

        $query = "select 
                    dt.year as year,dt.month as month,
                    sum(s.total) as sales 
                    from f_sale s
                    inner join d_date dt on dt.dateid = s.d_date_dateid
                    $where
                    group by dt.year,dt.month";
        $res = $db->query($query);
        if(!$res)
            die($db->error.$query);
        while ($line = $res->fetch_assoc()){
            $data[$line["month"]][array_search($line["year"],$years)] = $line["sales"]+0;
        }
        $return = array('years' => $years, 'data' => $data);
        echo json_encode($return);        
    }
}
?>
