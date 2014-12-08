<?php

require_once("KiiRequest.php");
require_once("KiiACL.php");
require_once("KiiACLEntry.php");
require_once("KiiBucket.php");
require_once("KiiObject.php");
require_once("KiiUser.php");
require_once("KiiGeoPoint.php");
require_once("KiiQuery.php");
require_once("KiiClause.php");
require_once("KiiTopic.php");
require_once("KiiUtilities.php");

class Kii {

	var $appID = null;
	var $appKey = null;
	var $baseURL = null;
	var $accessToken = null;

	function Kii($appId, $appKey, $baseURL="https://api.kii.com/api") {
		$this->appID = $appId;
		$this->appKey = $appKey;
		$this->baseURL = $baseURL;
	}

	static function bucketWithName($name) {
		return new KiiBucket(null, $name);
	}

};

function KPrint($msg) {
	echo $msg."\n";
}

?>