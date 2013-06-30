<?php

class KiiClause {
	
	var $dictExpression = array();
	var $whereType = null;
	var $whereClauses = array();
	
	function setDictValue($expression) { 
		$this->dictExpression = $expression;
	}
	
	function getDictValue() {
		
		$retDict = array();
		
		if($this->whereClauses != null && $this->whereType != null) {
			
			$clauses = array();
			
			if(count($this->whereClauses) == 1) {
			
				$clause = $this->whereClauses[0];
				
				var_dump($this->whereClauses);
				
				if($this->whereType == "not") {
					$retDict = array("type"=>$this->whereType, "clause"=>$clause->getDictValue());
				} else {
					$retDict = $clause->getDictValue();
				}
				
			} else {
				
				foreach($this->whereClauses as $clause) {
					$clauses[] = $clause->getDictValue();
				}
				
				$retDict = array("type"=>$this->whereType, "clauses"=>$clauses);
				
			}
			
		} else if($this->whereClauses != null) {
			
			if(count($this->whereClauses) > 0) {
				$retDict = $this->whereClauses[0]->getDictValue();
			}
			
		} else if($this->dictExpression != null) {
			$retDict = $this->dictExpression;
		}
		
		if($retDict == null) {
			$retDict = KiiQuery::emptyDictValue();
		}
		
		return $retDict;
		
	}
	
	public static function createWithWhere($whereType, $whereClauses) {		
		$expression = new KiiClause();
		$expression->whereType = $whereType;
		$expression->whereClauses = $whereClauses;
		return $expression;
	}
	
	private function createClause($operator, $key, $value) {
		
		$expression = new KiiClause();
		
		$dict = array();
		
		if($operator == "=") {
			$dict['type'] = "eq";
			$dict['field'] = $key;
			$dict['value'] = $value;
		} else if($operator == "<") {
        	$dict['type'] = "range";
            $dict['field'] = $key;
            $dict['upperLimit'] = $value;
            $dict['upperIncluded'] = false;
		} else if($operator == "<=") {
        	$dict['type'] = "range";
            $dict['field'] = $key;
            $dict['upperLimit'] = $value;
            $dict['upperIncluded'] = true;
		} else if($operator == ">") {
        	$dict['type'] = "range";
            $dict['field'] = $key;
            $dict['lowerLimit'] = $value;
            $dict['lowerIncluded'] = false;
		} else if($operator == ">=") {
        	$dict['type'] = "range";
            $dict['field'] = $key;
            $dict['lowerLimit'] = $value;
            $dict['lowerIncluded'] = true;
		} else if($operator == "in") {
        	$dict['type'] = "in";
            $dict['field'] = $key;
            $dict['lowerLimit'] = $value;
		} else if($operator == "prefix") {
        	$dict['type'] = "prefix";
            $dict['field'] = $key;
            $dict['lowerLimit'] = $value;
		}
		
		$expression->setDictValue($dict);
		
		return $expression;
		
	}
	
	public static function hasField($fieldName, $fieldType) {

		$expression = new KiiClause();
		
		$dict = array();
		$dict['type'] = "hasField";
		$dict['field'] = $fieldName;
		$dict['fieldType'] = $fieldType;
		
		$expression->setDictValue($dict);
		
		return $expression;
	}
	
	public static function andClauses() { 
		KiiClause::createWithWhere("and", func_get_args()); 
	}

	public static function orClauses() { 
		KiiClause::createWithWhere("or", func_get_args()); 
	}
	
	public static function equals($key, $value) {
		if(get_class($value) == "KiiObject") {
			$value = $value->objectURI();
		}
		
		return KiiClause::createClause("=", $key, $value);
	}
	
	public static function notEquals($key, $value) {
		if(get_class($value) == "KiiObject") {
			$value = $value->objectURI();
		}
		
		return KiiClause::createWithWhere("not", KiiClause::equals($key, $value));
	}
	
	public static function greaterThan($key, $value) { 
		return KiiClause::createClause(">", $key, $value); 
	}
	
	public static function greaterThanOrEqual($key, $value) { 
		return KiiClause::createClause(">=", $key, $value); 
	}
	
	public static function lessThan($key, $value) { 
		return KiiClause::createClause("<", $key, $value); 
	}
	
	public static function lessThanOrEqual($key, $value) { 
		return KiiClause::createClause("<=", $key, $value); 
	}
	
	public static function in($key, $values) {
		return KiiClause::createClause("in", $key, $values);
	}
	
	public static function startsWith($key, $value) {
		return KiiClause::createClause("prefix", $key, $value);
	}
	
	

};

?>