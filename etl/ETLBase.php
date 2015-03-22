<?php
abstract class ETLBase{
    protected static $NF_CLIENT   = 7884;
    protected static $NF_PRODUCT  = 304202;
    protected static $NF_SELLER   = 15;

    protected static $DEF_STATION = 1;

    protected static $SRC_SERVINN = "SERV";

    abstract function runETL();
}
?>