<?php
date_default_timezone_set("America/New_York");
?>
<html>
    <head>
        <style type="text/css">
            table{
                border: 1px black solid;
            }
            td{
                border: 1px black solid;
            }
        </style>
    </head>
    <body>
        <?php
            $page = 1;
            $recPerPage = 100;
            $file = "";
            $condition = 'return is_array($line);';
            if(isset($_POST["page"])){
                $page   = $_POST["page"];
                $recPerPage = $_POST["recPerPage"];
                $condition = $_POST["condition"];
                $file = $_POST["fileName"];
            }
        ?>
        <form method="POST">
            Type the file path: <input type="text" name="fileName" value="<?php echo $file; ?>"><br>
            Number of Recs: <input type="text" name="recPerPage" value="<?php echo $recPerPage; ?>"><br>
            Condition: <input type="text" name="condition" value="<?php echo htmlentities($condition); ?>"><br>
            Page: <input type="text" name="page" value="<?php echo $page; ?>">
            <input type="submit" value="Go">
        </form>
        <?php
        if(isset($_POST["fileName"])){
            if($page==1)
                $offSet = 1;
            else
                $offSet = (($page-1)*$recPerPage)+1;
            $limit  = $offSet+$recPerPage;

            $fileName = $_POST["fileName"];
            $handler = dbase_open($fileName, 0);
            $numRec  = dbase_numrecords($handler);

            $fileContent = array();

            $fileHeader = dbase_get_header_info($handler);
            $columns = array();
            foreach ($fileHeader as $value) {
                $columns[] = $value["name"];
            }
            echo ((new DateTime())->format(DateTime::ISO8601)  )."<br>";
            echo "<table>";
            echo "<tr>";
            echo "<th>RecNo</th>";
            foreach ($columns as $value) {
                echo "<th>$value</th>";
            }
            echo "<th>deleted</th>";
            echo "</tr>";
            unset($fileHeader);
            unset($columns);

            $shownLines = 0;

            for ($i=$offSet; ($i<$numRec && $i < $limit); $i++) {
                $line = dbase_get_record_with_names($handler, $i);
                //echo var_dump($line);
                //exit;
                if(eval($condition)){
                    $shownLines++;
                    echo "<tr>";
                    echo "<td>$i</td>";
                    foreach ($line as $name => $column) {
                        echo "<td>$column</td>";
                    }
                    echo "</tr>";

                }
            }
            echo "</table>";
            echo "$numRec records. $shownLines shown.";
        }
        ?>
    </body>
</html>