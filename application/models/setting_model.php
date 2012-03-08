<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting_model extends CI_Model {

	function __construct() {
		$this->load->helper('mongodb_helper');
		$this->collection = mongodb_load('setting');
		$this->int_values = array('app_install_id');
	}

	//Basic functions (reindex & CRUD)
	function recreateIndex() {
		return $this->collection->deleteIndexes() 
			&& $this->collection->ensureIndex(array('app_install_id' => 1), array('unique' => 1));
	}
        
	function add($data)
	{
		$data = array_cast_int($data, $this->int_values);
		try	{
			$result = $this->collection->insert($data, array('safe' => TRUE));
			return ''.$data['_id'];
		} catch(MongoCursorException $e){
			log_message('error', 'Mongodb error : '. $e);
			return FALSE;
		}
	}
	
	function get($query){
		$query = array_cast_int($query, $this->int_values);
		$result = $this->collection->find($query);
		return cursor2array($result);
	}

	function getOne($query){
		$query = array_cast_int($query, $this->int_values);
		$result = $this->collection->findOne($query);
		return obj2array($result);
	}
		
	function update($query, $data)
	{
		$query = array_cast_int($query, $this->int_values);
		$data = array_cast_int($data, $this->int_values);
		try	{
			return $this->collection->update($query, $data, array('safe' => TRUE));
		} catch(MongoCursorException $e){
			log_message('error', 'Mongodb error : '. $e);
			return FALSE;
		}
	}

	function delete($query){
		$query = array_cast_int($query, $this->int_values);
		return $this->collection->remove($query, array('$atomic' => TRUE));
	}
	//End of basic functions
}