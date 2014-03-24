<?php

class KiiUser {
	
	var $uuid = null;
	var $username = null;
	var $emailAddress = null;
	var $phoneNumber = null;
	
	// "hidden"
	var $customInfo = array();
	
	public static function userWithUUID($uuid) {
	
	    $obj = new KiiUser();
	    $obj->uuid = $uuid;

		return $obj;
	}
	
	public static function userWithUsername($username) {
	
	    $obj = new KiiUser();
	    $obj->uuid = $uuid;

		return $obj;
	}
	
	public function refresh() {
	
		$objectRequest = new KiiRequest();
		$objectRequest->method = "GET";

		$objectRequest->path = "/buckets/".$this->bucket->name."/objects/".$this->uuid;
		
		// make the request
		$objectResult = $objectRequest->execute();
		
		$this->updateJSON(get_object_vars($objectResult['json']));
		
		return $objectResult['statusCode'];
	}
	
	public function set($key, $value) {
	
		if(is_object($value)) {
			$className = get_class($value);	
			if($className == "KiiObject") {
				$value = $value->objectURI();
			} else if(is_array($value)) {
			    $value = json_encode($value);
			}
		}
			
		$this->customInfo[$key] = $value;
	}
	
	public function get($key) {
		return $this->customInfo[$key];
	}
	
	public function objectURI() {
	
		$uri = null;
		
		if($this->bucket && $this->uuid) {
			
			$uri = "kiicloud://";
			
			if($this->bucket->group) {
			
			} else if($this->bucket->user) {
			
			}
			
			$uri .= "buckets/".$this->bucket->name."/objects/".$this->uuid;			
		}
		
		return $uri;

	}
	
	public function updateJSON($json) {
	
		foreach($json as $key=>$value) {
			
			if($key == "objectID" || $key == "_id" || $key == "uuid") {
				$this->uuid = $value;
			} else if($key == "createdAt" || $key == "created" || $key == "_created") {
				$this->created = $value;
			} else if($key == "modifiedAt" || $key == "modified" || $key == "_modified") {
				$this->created = $value;
			} else if($key == "_owner") {
				// TODO: make KiiUser.userWithID($value);
			} else if($key == "_dataType") {
				$this->objectType = $value;
			} else {
				$this->customInfo[$key] = $value;
			}
		}

	}
	
	public function describe() {
	
		KPrint("==== KiiObject ====");
		
		KPrint("UUID: ".$this->uuid);
		KPrint("URI: ".$this->objectURI());
		KPrint("Type: ".$this->objectType);
		KPrint("Created: ".$this->created);
		KPrint("Modified: ".$this->modified);
		
		foreach($this->customInfo as $key=>$value) {
			if(is_string($value)) {
				KPrint($key.": ".$value);		
			} else {
				KPrint($key.": ".json_encode($value));
			}
		}
		
		KPrint("===================");
	}
	
	public function toArray() {
		
		$arr = array();
		
		$arr['uuid'] = $this->uuid;
// 		$arr['uri'] = $this->objectURI();
// 		$arr['type'] = $this->objectType;
		$arr['created'] = $this->created;
// 		$arr['modified'] = $this->modified;
		
		foreach($this->customInfo as $key=>$value) {
		
			if($key == "key") continue;
		
			if(is_string($value)) {
				$arr[$key] = $value;
			} else {
				$arr[$key] = json_encode($value);
			}
		}
		
		return $arr;
		
	}
	
	public function sqlPrint($attrs) {
	
		$ndx = count($attrs);
		
		$attrs[$ndx]['uuid'] = $this->uuid;
		$attrs[$ndx]['uri'] = $this->objectURI();
		$attrs[$ndx]['type'] = $this->objectType;
		$attrs[$ndx]['created'] = $this->created;
		$attrs[$ndx]['modified'] = $this->modified;
		
		foreach($this->customInfo as $key=>$value) {
			if(is_string($value)) {
				$attrs[$ndx][$key] = $value;
			} else {
				$attrs[$ndx][$key] = json_encode($value);
			}
		}

		return $attrs;
	}
	
	public function save() {
	
		$objectRequest = new KiiRequest();

		$objectRequest->method = ($this->uuid != null) ? "PUT" : "POST";

		$path = "/buckets/".$this->bucket->name."/objects/";
		if($this->uuid != null) {
			$path .= $this->uuid;
		}
		
		$objectRequest->path = $path;
		$objectRequest->data = $this->customInfo;
		
		// make the request
		$objectResult = $objectRequest->execute();
		
		$this->updateJSON(get_object_vars($objectResult['json']));
		
		return $objectResult['statusCode'];
	}
	
	public function delete() {
	
		$deleteRequest = new KiiRequest();
		$deleteRequest->path = "/buckets/".$this->bucket->name."/objects/".$this->uuid;
		$deleteRequest->method = "DELETE";
		
		// make the request
		$deleteResult = $deleteRequest->execute();						
	}
	
};

?>