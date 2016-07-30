<?php
namespace bi\Model;
class WeatherStation{
    protected $weatherStationId;
    protected $country;
    protected $wmo;
    protected $icao;
    protected $station;
    protected $latitude;
    protected $longitude;
    protected $height;


    public function getWeatherStationId() {
        return $this->weatherStationId;
    }
    public function setWeatherStationId($weatherStationId) {
        $this->weatherStationId = $weatherStationId;
        return $this;
    }

    public function getCountry() {
        return $this->country;
    }
    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    public function getWmo() {
        return $this->wmo;
    }
    public function setWmo($wmo) {
        $this->wmo = $wmo;
        return $this;
    }

    public function getIcao() {
        return $this->icao;
    }
    public function setIcao($icao) {
        $this->icao = $icao;
        return $this;
    }

    public function getStation() {
        return $this->station;
    }
    public function setStation($station) {
        $this->station = $station;
        return $this;
    }

    public function getLatitude() {
        return $this->latitude;
    }
    public function setLatitude($latitude) {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude() {
        return $this->longitude;
    }
    public function setLongitude($longitude) {
        $this->longitude = $longitude;
        return $this;
    }

    public function getHeight() {
        return $this->height;
    }
    public function setHeight($height) {
        $this->height = $height;
        return $this;
    }
}
?>