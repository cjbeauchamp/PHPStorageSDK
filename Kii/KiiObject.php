<?php

class KiiObject {
	
	var $uuid = null;
	var $created = null;
	var $modified = null;
	var $objectType = null;
	
	// "hidden"
	var $customInfo = array();
	var $bucket = null;
	
    public function KiiObject($bucket, $type) {
		$this->objectType = $type;
		$this->bucket = $bucket;
	}
	
	public static function objectWithURI($uri) {
	
		$obj = null;
	
		$newURI = substr($uri, strlen("kiicloud://"));
		
		$components = explode("/", $newURI);
		$compLength = count($components);
						
		if($compLength >= 4) {
		
			$bucketIndex = ($compLength == 4) ? 1 : 3;
			$bucketName = $components[$bucketIndex];
									
			$owner = ($compLength == 4) ? null : KiiUser::userWithUUID($components[1]);
						
			$bucket = new KiiBucket($owner, $bucketName);
			
			$obj = new KiiObject($bucket, null);
			$obj->uuid = $components[$compLength-1];
		
		}

		return $obj;
	}
	
	public function refresh() {
	
		$objectRequest = new KiiRequest();
		$objectRequest->method = "GET";

		$objectRequest->path = $this->bucket->getPath()."/objects/".$this->uuid;
		
		// make the request
		$objectResult = $objectRequest->execute();
		
		if($objectResult['statusCode'] >= 200 && $objectResult['statusCode'] < 300) {
		    $this->updateJSON(get_object_vars($objectResult['json']));
		}
		
		return $objectResult['statusCode'];
	}
	
	public function set($key, $value) {
	
		if(is_object($value)) {
			$className = get_class($value);	
			if($className == "KiiObject") {
				$value = $value->objectURI();
			} else if($className == "KiiGeoPoint") {
				$value = $value->toDict();
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
			
			$uri = "kiicloud:/";
			
			$uri .= $this->bucket->getPath()."/objects/".$this->uuid;			
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

				// see if it's a geopoint
				if($value->{'_type'} == "point") {
					$value = KiiGeoPoint::geopoint($value->{'lat'}, $value->{'lon'});
				}

				$this->customInfo[$key] = $value;
			}
		}

	}
	
    public function describe($lite=false) {
        if($lite) {
            KPrint("KiiObject ==> ".$this->uuid." => ".json_encode($this->customInfo));
        } else {
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
    }

    public function toArray() {
		
		$arr = array();
		
		$arr['uuid'] = $this->uuid;
		$arr['created'] = $this->created;
		
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

		$path = $this->bucket->getPath()."/objects/";
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
		$deleteRequest->path = $this->bucket->getPath()."/objects/".$this->uuid;
		$deleteRequest->method = "DELETE";
		
		// make the request
		$deleteResult = $deleteRequest->execute();						
	}
	
};

?>