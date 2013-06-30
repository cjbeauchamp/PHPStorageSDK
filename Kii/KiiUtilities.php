<?php

function getAppAdminToken($clientID, $clientSecret) {

    // set up the request
    $tokenRequest = new KiiRequest();
    $tokenRequest->path = "/oauth2/token";
    $tokenRequest->method = "POST";
    $tokenRequest->anonymous = true; // don't have an access token yet, so make 'anonymous'
    $tokenRequest->appScope = false; // we don't want the app id in the url, so set the app scope to false
    $tokenRequest->data = array("client_id"=>$clientID, "client_secret"=>$clientSecret);
    
    // make the request
    $tokenResult = $tokenRequest->execute();
    
    return $tokenResult['json']->{'access_token'};
}

?>