<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_users extends CI_Model {
		
	function __construct() {
		parent::__construct();
		$this->load->database();
		// check if database exists
		$query = $this->db->query("
			CREATE DATABASE IF NOT EXISTS {$this->db->database} 
			CHARACTER SET utf8 COLLATE utf8_general_ci;
		");

		// check if table exists
		$query = $this->db->query("
			CREATE TABLE IF NOT EXISTS `{$this->db->dbprefix}app_users` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`company_id` bigint(20) unsigned NOT NULL,
				`app_install_id` bigint(20) unsigned NOT NULL,
				`app_install_secret_key` varchar(255) NOT NULL,
				`facebook_page_id` bigint(20) unsigned NOT NULL,
				`user_facebook_id` bigint(20) unsigned NOT NULL,
				`connected` tinyint(1) NOT NULL DEFAULT '0',
				`access_token` varchar(255) NOT NULL,
				PRIMARY KEY (`id`,`app_install_id`,`facebook_page_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
		");
	}
	
	function get($where){
		return $this->db->get_where('app_users', $where)->row_array();
	}
        
	function add($data)
	{
		return $this->db->insert('app_users', $data);
	}
		
	function update($where, $data)
	{
		$this->db->set($data);
		$this->db->where($where);
		return $this->db->update('app_users');
	}
}