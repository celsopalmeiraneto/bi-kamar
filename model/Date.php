<?php
namespace bi\Model;
class Date extends \DateTime{
    function __construct($time = "now", \DateTimeZone $timezone = NULL){
        parent::__construct($time,$timezone);
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    function getSemester(){
        return ceil($this->format("m")/6);
    }
    function getTrimester(){
        return ceil($this->format("m")/3);
    }
    function getYear(){
        return $this->format("Y")*1;
    }
    function getMonth(){
        return $this->format("m")*1;
    }
    function getDay(){
        return $this->format("d")*1;
    }
    function getDayOfWeek(){
        $julianDay = cal_to_jd(CAL_GREGORIAN, $this->getMonth(), $this->getDay(), $this->getYear());
        return JDDayOfWeek($julianDay,0)+1;
    }
    function getDateISO8601(){
        return $this->format(DateTime::ISO8601);
    }
}
?>