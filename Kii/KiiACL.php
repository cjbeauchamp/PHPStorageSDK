<?php

class KiiAnyAuthenticatedUser { };
class KiiAnonymousUser { };

class KiiACL {

	var $entries = array();
	var $parentObject = null;
	
	public function KiiACL($parent) {
		$this->parentObject = $parent;
	}
	
	private function aclPath() {
	
		$object = null;
		$user = null;
		$group = null;
		$bucketName = null;
		$objectID = null;
	
		$class = get_class($this->parentObject);
		
		if($class == "KiiObject") {
			$object = $this->parentObject;
			
			$user = $object->bucket->user;
			$group = $object->bucket->group;
			
			$bucketName = $object->bucket->name;
			$objectID = $object->uuid;

		} else if($class == "KiiBucket") {
			
			$bucket = $this->parentObject;
			
			$user = $bucket->user;
			$group = $bucket->group;
			
			$bucketName = $bucket->name;
			
		} else {
			// error: "Invalid ACL parent. Must belong to a KiiObject"
		}
		
		$path = "/";
		
		if($group != null) {
			$path .= "groups/".$group->uuid."/";
		} else if($user != null) {
			$path .= "users/".$user->uuid."/";
		}
		
		if($objectID != null) {
			$path .= "buckets/".$bucketName."/objects/".$objectID."/acl";
		} else {
			$path .= "buckets/".$bucketName."/acl";
		}
		
		return $path;
	}
	
	public function putACLEntry($entry) {
		if(!in_array($entry, $this->entries)) {
			$this->entries[] = $entry;
		}
	}
	
	private function saveSingle($entry) {
		
        $path = $this->aclPath()."/".$entry->getActionString()."/".$entry->getEntityString();

		$aclRequest = new KiiRequest();
		$aclRequest->path = $path;
		$aclRequest->method = ($entry->grant) ? "PUT" : "DELETE";
		
		// make the request
		$aclRequest->execute();
		
	}
	
	public function save() {
		
		foreach($this->entries as $entry) {
			$this->saveSingle($entry);
		}
		
	}
	
};

?>