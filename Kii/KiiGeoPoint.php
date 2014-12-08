<?php

class KiiGeoPoint {

	var $latitude = 0;
	var $longitude = 0;

	public static function geopoint($latitude, $longitude) {
		$pt = new KiiGeoPoint();
		$pt->latitude = $latitude;
		$pt->longitude = $longitude;
		return $pt;
	}

	public function toDict() {
		return array(
			"_type" => "point",
			"lat" => $this->latitude,
			"lon" => $this->longitude
		);
	}
};

