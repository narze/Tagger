<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('mongodb_load')){
	function mongodb_load($collection = NULL) {
		if(!$collection){
			show_error('Collection not specified');
			exit();
		}

		$CI =& get_instance();

		$CI->config->load('socialhappen');
		$mongodb_username = $CI->config->item('mongodb_username');
		$mongodb_password = $CI->config->item('mongodb_password');
		$mongodb_host = $CI->config->item('mongodb_host');
		$mongodb_port = $CI->config->item('mongodb_port');
		$mongodb_database = $CI->config->item('mongodb_database');
		
		try{
			// connect to database
			$CI->connection = new Mongo("mongodb://".$mongodb_username.":"
			.$mongodb_password
			."@".$mongodb_host.":".$mongodb_port);
			
			// select database
			$CI->mongodb = $CI->connection->{$mongodb_database};

			// return collection
			return $CI->mongodb->{$collection};
			
		} catch (Exception $e){
			show_error('Cannot connect to database');
			return FALSE;
		}
	}
}

if(!function_exists('obj2array'))
{
	function obj2array($object){
		if(!$object){
			return FALSE;
		}
		return json_decode(json_encode($object), TRUE);
	}
}

if(!function_exists('cursor2array'))
{
	function cursor2array($cursor){
		if(!$cursor){
			return FALSE;
		}
		$array = array();
		foreach($cursor as $one){
			$array[] = $one;
		}
		return $array;
	}
}

if(!function_exists('array_cast_int'))
{
	function array_cast_int($array, $indexes = NULL) {
		if(!is_array($array)) {
			return FALSE;
		}
		if(is_array($indexes)) {
			foreach($indexes as $index) {
				if(isset($array[$index]) && is_numeric($array[$index])) {
					$array[$index] = (int) $array[$index];
				}
			}
		} else {
			foreach($array as &$value) {
				if(is_numeric($value)) {
					$value = (int) $value;
				}
			} unset($value);
		}

		return $array;
	}
}