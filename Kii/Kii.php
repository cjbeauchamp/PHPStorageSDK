<?php

require_once("KiiRequest.php");
require_once("KiiACL.php");
require_once("KiiACLEntry.php");
require_once("KiiBucket.php");
require_once("KiiObject.php");
require_once("KiiQuery.php");
require_once("KiiClause.php");

class Kii {

	var $appID = null;
	var $appKey = null;
	var $baseURL = "https://api.kii.com/api";
	var $accessToken = null;

	function Kii($appId, $appKey) {
		$this->appID = $appId;
		$this->appKey = $appKey;
	}

};

function KPrint($msg) {
	echo $msg."\n";
}

?>