<?php
    
class KiiRequest {

    // the API path that should be requested (ex: /users)
    var $path = null;
    
    // the HTTP method to use (GET|POST|PUT|DELETE)
    var $method = "GET";
    
    // data to pass with a POST request (should be an array, if anything)
    var $data = null;
    
    // set to true if the request doesn't require an access token (ie: authentication/registration)
    var $anonymous = false;
    
    // some requests don't use the app id (specifically authentication). set to false if the path shouldn't include appID
    var $appScope = true;

    var $accept = null;

    // the content type of the request
    var $contentType = "application/json";
    
    // file PUT information
    var $file = null; // should be a file handle (ie: fopen($path, 'r'))
    var $fileSize = -1; // size of the file being put
    
    public function execute() {
    
        global $kii;

        // define the url to call
        $url = $kii->baseURL;
        
        // some requests don't use the app id (specifically authentication), so only omit it if required
        if($this->appScope) {
            $url .= "/apps/".$kii->appID;
        }
        
        // add the passed-in path to the url
        $url .= $this->path;
        
        // encode the data (should be an array, if anything) into json
        $postData = json_encode($this->data);
                
        // set some headers used for every request
        $headers = array();
        $headers[] = "x-kii-appid: ".$kii->appID;
        $headers[] = "x-kii-appkey: ".$kii->appKey;
        $headers[] = "Content-Type: ".$this->contentType;
        
        // not needed for file upload, only for some other APIs
        if($this->accept != null) {
            $headers[] = "Accept: ".$this->accept;
        }
        
        // if the request doesn't require an access token
        if(!$this->anonymous && $kii->accessToken != null) {
            $headers[] = "Authorization: Bearer ".$kii->accessToken;
        }
        
        // set up the request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
        
    	if($this->method == "POST") {
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            $headers[] = "Content-Length: ".strlen($postData);
    	} else if($this->method == "PUT" && $this->file != null) {
    		curl_setopt($ch, CURLOPT_PUT, 1);
    		curl_setopt($ch, CURLOPT_INFILE, $this->file);
    		curl_setopt($ch, CURLOPT_INFILESIZE, $this->fileSize);
    	} 
    	
    	// we're putting json
    	else if($this->method == "PUT") {
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    		$headers[] = "Content-Length: ".strlen($postData);
    	}
    	
    	echo "Making request[".$this->method."] to [".$url."] with data: ".$postData."\n";
    	
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch); 
            
        // get the http status code
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        
        echo "Request complete[".$http_status."] => ".$output."\n\n";
                
        // return status code, json-formatted response and raw output in an associative array
        return array("statusCode"=>$http_status, "json"=>json_decode($output), "raw"=>$output);

    }
    

};
    
?>