<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Facebook wrapper class
 * Call this class : $this->FB->...
 * Call fb-php-sdk directly : $this->facebook->...
 */
class FB {

	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->library('fb-php-sdk/facebook',
			array(
			  'appId'  => $this->CI->config->item('facebook_app_id'),
			  'secret' => $this->CI->config->item('facebook_app_secret')
			),
			'facebook'
		);
		$this->facebook = $this->CI->facebook;
		// channel_url can be used with facebook js sdk
		$this->channel_url = base_url().'assets/channel/fb.php';
	}

	/**
	 * Call facebook graph api
	 */
	function api(/* polymorphic */){
		return $this->facebook->api(func_get_args());
	}

	/*
	 * Get fb-root div
	 */
	function getFbRoot(){
		return $this->CI->load->view('fb-root', array(
			'facebook_app_id' => $this->CI->config->item('facebook_app_id'),
			'facebook_channel_url' => $this->channel_url,
			'facebook_app_scope' => $this->CI->config->item('facebook_app_scope')
		), TRUE);
	}
}