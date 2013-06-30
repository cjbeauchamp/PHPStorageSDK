<?php

	require_once("Kii/Kii.php");
	
	define("APP_ID", "");
	define("APP_KEY", "");
	define("CLIENT_ID", "");
	define("CLIENT_SECRET", "");
	
	// check to make sure things are entered
	if(APP_ID=="" || APP_KEY=="" || CLIENT_ID=="" || CLIENT_SECRET=="") {
	    exit("\nERROR: Enter your apps credentials first! (lines 5-8)\n\n");
    }
    
    $kii = new Kii(APP_ID, APP_KEY);
	$kii->accessToken = getAppAdminToken(CLIENT_ID, CLIENT_SECRET);
	
    $bucket = new KiiBucket(null, "mybucket");

	echo "== Creating an object\n";
	
	// create an object
	$object = $bucket->createObject(null);
	$object->set("key", "value");
	$object->save();
	
	echo "== Object created\n";
	$object->describe(true);

	echo "== Retrieving the objects\n";

    // query the bucket
    $objects = $bucket->query(null, $next);
	foreach($objects as $o) {	
        $o->describe(true);
	}

	echo "== Deleting the bucket\n";

    $bucket->delete();


	echo "== Querying the deleting the bucket\n";

    // query the bucket
    $objects = $bucket->query(null, $next);
	foreach($objects as $o) {	
        $o->describe(true);
	}

?>