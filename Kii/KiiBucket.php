<?php

class KiiBucket {

	var $name = null;
	var $owner = null;
	
	var $acl = null;
	
	public function KiiBucket($owner, $name) {
		$this->name = $name;	
		$this->owner = $owner;
		$this->acl = new KiiACL($this);
	}
	
	public function createObject($type=null) {
		return new KiiObject($this, $type);
	}
	
	public function getPath() {
	    $path = "";
	    
	    if($this->owner != null) {
	        $path .= "/users/".$this->owner->uuid;
	    }
	    
	    $path .= "/buckets/".$this->name;
	    
	    return $path;
	}

	public function delete() {
	
		// set up the request
		$deleteRequest = new KiiRequest();
		$deleteRequest->path = $this->getPath();
		$deleteRequest->method = "DELETE";

		// make the request
		$deleteResult = $deleteRequest->execute();
		
		return ($deleteResult['statusCode'] == 204);
	}	
		
	public function query($query, &$next) {
	
		// set up the request
		$objectRequest = new KiiRequest();
		$objectRequest->path = $this->getPath()."/query";
		$objectRequest->method = "POST";
		$objectRequest->contentType = "application/vnd.kii.QueryRequest+json";
		
		$data = array();
		
		if($query != null) {
			$clauseData = $query->dictValue();
			$data['bestEffortLimit'] = $query->limit;
			
			if($query->paginationKey != null) {
				$data['paginationKey'] = $query->paginationKey;
			}
		} else {
			$query = new KiiQuery();
			$clauseData = array("clause" => KiiQuery::emptyDictValue());
		}
		
		$data['bucketQuery'] = $clauseData;
		
		// make the request
		$objectRequest->data = $data;
		$objectResult = $objectRequest->execute();
		$results = null;
				
		if(is_array($objectResult['json']->{'results'})) {
			$results = array();	
		}
		
		foreach($objectResult['json']->{'results'} as $item) {		
			$obj = $this->createObject(null);
			$obj->updateJSON(get_object_vars($item));
			$results[] = $obj;
		}
		
		if($objectResult['json']->{'nextPaginationKey'} != null) {
			$next = $query;
			$next->paginationKey = $objectResult['json']->{'nextPaginationKey'};
		} else {
			$next = null;
		}
		
		return $results;			
	}

};