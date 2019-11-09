<?php

function issetWhere($dbInstance, $array, $column, $operator = '='){
	if(is_array($array) && array_key_exists($column, $array)){
		if(isset($array[$column]) && !empty($array[$column]) || is_numeric($array[$column])) $dbInstance->where($column.$operator ,$array[$column]);
	}
	//return false;
}

function issetSet($dbInstance, $array, $column){
	if(is_array($array) && array_key_exists($column, $array)) {
		if (!empty($array[$column]) || is_numeric($array[$column])) $dbInstance->set($column, $array[$column]);
	}
}
