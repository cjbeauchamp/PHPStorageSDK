<?php

	require_once("Kii.php");
	
	define("APP_ID", "");
	define("APP_KEY", "");
	define("CLIENT_ID", "");
	define("CLIENT_SECRET", "");
	
	// check to make sure things are entered
	if(empty(APP_ID) || empty(APP_KEY) || empty(CLIENT_ID) || empty(CLIENT_SECRET))
	    exit("Enter your apps credentials first! (lines 5-8)");

    $kii = new Kii(APP_ID, APP_KEY);
	$kii->accessToken = getAppAdminToken(CLIENT_ID, CLIENT_SECRET);
	
    $bucket = new KiiBucket(null, "mybucket");

	echo "== Creating an object\n";
	
	// create an object
	$object = $bucket->createObject(null);
	$object->set("key", "value");
	$object->save();

	echo "== Retrieving the objects\n";

    // query the bucket
    $objects = $bucket->query(null, null);
	foreach($objects as $o) {	
        $o->describe();
	}

	echo "== Deleting the bucket\n";

	$bucket = new KiiBucket(null, BUCKET_NAME);
    $bucket->delete();


	echo "== Querying the deleting the bucket\n";

    // query the bucket
    $objects = $bucket->query(null, null);
	foreach($objects as $o) {	
        $o->describe();
	}
?>