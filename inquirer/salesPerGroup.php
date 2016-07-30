<?php
namespace bi\Inquirer;
include 'loader.php';
if(isset($_GET["year"])){
    $year = $_GET["year"]+0;
    if(is_numeric($year)){

        $db = \bi\model\DB::getConnection('bi');

        $data = array();

        $query = "select 
                    p.category as cat,
                    sum(s.total) as sales 
                    from f_sale s
                    inner join d_date dt   on dt.dateid = s.d_date_dateid
                    inner join d_product p on p.productid = s.d_product_productid
                    where year = $year
                    group by p.category;
                    ";
        $res = $db->query($query);
        if(!$res)
            die($db->error.$query);

        while ($line = $res->fetch_assoc()){
            $data[] = array(utf8_encode(trim($line["cat"])) => $line["sales"]+0);
        }
        echo json_encode($data);
        //echo json_last_error();   
    }
}
?>
