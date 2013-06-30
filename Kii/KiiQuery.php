<?php

class KiiQuery {

	var $sortString = null;
	var $cursor = null;
	
	var $paginationKey = null;
	
	var $sortDescending = false;
	var $sortField = null;
	
	var $limit = 200;
	var $clause = null;

	public static function queryWithClause($clause) {
		$query = new KiiQuery();
		$query->clause = $clause;
		return $query;
	}

	public function sortByDesc($field) {
		$this->sortField = $field;
		$this->sortDescending = true;
	}

	public function sortByAsc($field) {
		$this->sortField = $field;
		$this->sortDescending = false;
	}
	
	public function emptyDictValue() {
		return array("type"=>"all");
	}
	
	public function dictValue() {
		
		$data = array("descending"=>$this->sortDescending);
				
		if($this->clause != null) {
			$data['clause'] = $this->clause->getDictValue();
		} else {
			$data['clause'] = KiiQuery::emptyDictValue();
		}
		
		if($this->sortField != null) {
			$data['orderBy'] = $this->sortField;
		}
		
		return $data;
	}

};

?>