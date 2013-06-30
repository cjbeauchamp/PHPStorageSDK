<?php

class KiiACLAction {
    const KiiACLBucketActionCreateObjects = 0;
    const KiiACLBucketActionQueryObjects = 1;
    const KiiACLBucketActionDropBucket = 2;
    const KiiACLObjectActionRead = 3;
    const KiiACLObjectActionWrite = 4;
};


class KiiACLEntry {

	var $action = -1;
	var $subject = null;
	var $grant = true;
	
	public function setAction($value) {
		if($value >= KiiACLAction::KiiACLBucketActionCreateObjects && $value <= KiiACLAction::KiiACLObjectActionWrite) {
			$this->action = $value;
		} else {
			KPrint("Invalid ACL Action");
		}
	}

	public function setSubject($subject) {
		
		$className = get_class($subject);
		$validClasses = array("KiiGroup", "KiiUser", "KiiAnyAuthenticatedUser", "KiiAnonymousUser");
		
		if(in_array($className, $validClasses)) {
			$this->subject = $subject;
		} else {
			KPrint("Invalid ACL Subject");
		}
	}
	
	public function setGrant($grant) {
		if(is_bool($grant)) {
			$this->grant = $grant;
		} else {
			KPrint("Invalid ACL Grant");
		}
	}
	
	public static function entryWithSubject($subject, $action) {
				
		$entry = new KiiACLEntry();
		$entry->setSubject($subject);
		$entry->setAction($action);
		return $entry;
		
	}
	
	public function getActionString() {
	
		$ret = "";
	
        switch($this->action) {
        	case KiiACLAction::KiiACLBucketActionCreateObjects:
        		$ret = "CREATE_OBJECTS_IN_BUCKET";
        		break;
        	case KiiACLAction::KiiACLBucketActionQueryObjects:
        		$ret = "QUERY_OBJECTS_IN_BUCKET";
        		break;
        	case KiiACLAction::KiiACLBucketActionDropBucket:
        		$ret = "DROP_BUCKET_WITH_ALL_CONTENT";
        		break;
        	case KiiACLAction::KiiACLObjectActionRead:
        		$ret = "READ_EXISTING_OBJECT";
        		break;
        	case KiiACLAction::KiiACLObjectActionWrite:
        		$ret = "WRITE_EXISTING_OBJECT";
        		break;
        	default: 
        		break;
        }

		return $ret;
	}
	
	public function getEntityString() {
	
		$subjectType = get_class($this->subject);
		$type = "";
		$entityID = "";
		
        if($subjectType == "KiiGroup") {
            $entityID = $this->subject->uuid;
            $type = "GroupID";
        } else if($subjectType == "KiiUser") {
            $entityID = $this->subject->uuid;
            $type = "UserID";
        } else if($subjectType == "KiiAnyAuthenticatedUser") {
            $type = "UserID";
            $entityID = "ANY_AUTHENTICATED_USER";
        } else if($subjectType == "KiiAnonymousUser") {
            $type = "UserID";
            $entityID = "ANONYMOUS_USER";
        }

		return $type.":".$entityID;	
	}

};

?>