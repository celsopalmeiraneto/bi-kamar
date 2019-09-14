<?php
namespace bi\Model;

class DB extends \mysqli{

    private static $param  = array('bi' =>
                                    array("schema" => 'bi_kamar',
                                        "port"     => "3306",
                                        "host"     => "127.0.0.1",
                                        "user"     => "root",
                                        "password" => "root",
                                        "codePage" => "utf8")
                                ,"cep" =>
                                    array("schema" => 'cep',
                                        "port"     => "3306",
                                        "host"     => "127.0.0.1",
                                        "user"     => "root",
                                        "password" => "root",
                                        "codePage" => "utf8"));

    private static $connectionPool = array();

    function __construct($id,$saveOnPool = true){
        parent::__construct(self::$param[$id]["host"],
                            self::$param[$id]["user"],
                            self::$param[$id]["password"],
                            self::$param[$id]["schema"],
                            self::$param[$id]["port"]);
        if($this->connect_error){
            die($this->connect_error);
        }
        $this->set_charset(self::$param[$id]["codePage"]);

        if(isset(self::$connectionPool[$id])){
            self::$connectionPool[$id]->close();
        }
        self::$connectionPool[$id] = $this;
    }

    public static function getConnection($id){
        if(isset(self::$connectionPool[$id])){
            return self::$connectionPool[$id];
        }else{
            return (new DB($id));
        }
    }

    public function escapeObject(&$objeto){
        if(is_array($objeto) || is_object($objeto)){
            foreach ($objeto as $key => $value) {
                if(is_array($objeto)){
                    if(is_string($objeto[$key]))
                        $objeto[$key] = $this->escape_string($value);
                }else{
                    if(is_string($objeto->$key))
                        $objeto->$key = $this->escape_string($value);
                }
            }
        }
    }

}
?>
