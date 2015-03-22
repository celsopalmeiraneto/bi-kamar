<?php
namespace bi\Model;
class Synop{
    protected $synopId;
    protected $windDirection;
    protected $windSpeed;
    protected $precipitation;
    protected $dryBulbC;
    protected $dewPointC;

////////////Support Attributes
    protected $dateId;
    protected $stationId;


    public function getSynopId() {
        return $this->synopId;
    }
    public function setSynopId($synopId) {
        $this->synopId = $synopId;
        return $this;
    }

    public function getWindDirection() {
        return $this->windDirection;
    }
    public function setWindDirection($windDirection) {
        $this->windDirection = $windDirection;
        return $this;
    }

    public function getWindSpeed() {
        return $this->windSpeed;
    }
    public function setWindSpeed($windSpeed) {
        $this->windSpeed = $windSpeed;
        return $this;
    }

    public function getPrecipitation() {
        return $this->precipitation;
    }
    public function setPrecipitation($precipitation) {
        $this->precipitation = $precipitation;
        return $this;
    }

    public function getDryBulbC() {
        return $this->dryBulbC;
    }
    public function setDryBulbC($dryBulbC) {
        $this->dryBulbC = $dryBulbC;
        return $this;
    }

    public function getDewPointC() {
        return $this->dewPoint;
    }
    public function setDewPointC($dewPoint) {
        $this->dewPoint = $dewPoint;
        return $this;
    }

    public function getDateId() {
        return $this->dateId;
    }
    public function setDateId($dateId) {
        $this->dateId = $dateId;
        return $this;
    }

    public function getStationId() {
        return $this->stationId;
    }
    public function setStationId($stationId) {
        $this->stationId = $stationId;
        return $this;
    }
}
?>