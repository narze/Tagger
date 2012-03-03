<?php
/**
 *  SocialHappen Platform Library
 *  Last update 24 Feb 2012
 *  @author Manassarn M., Wachiraph C.
 */

class SocialHappen {
	private $api_method;
	private $app_id;
	private $app_secret_key;
	
	function __construct(){
		$CI =& get_instance();
		$CI->load->config('socialhappen');
		$this->sh_api_url = $CI->config->item('api_url');
		$this->app_id = $CI->config->item('app_id');
		$this->app_secret_key = $CI->config->item('app_secret_key');
		$this->mockuphappen_enable = $CI->config->item('mockuphappen_enable');
	}
	
	function request($method = null, $args = array()){
		if($this->mockuphappen_enable) {
			$CI->load->config('mockuphappen');
			return $CI->config->item("mockuphappen_{$method}");
		} else {
			if($method == null && sizeof($args)){
			return array(
					'error' => '',
					'message' => 'Parameters '
				);
			}
			$this->set_api_method($method);
			$response = $this->sh_curl($args);			
			return $this->check_response($response);
		}
	}
	
	function sh_curl($args = array()) {
		if(sizeof($args)>0) {
			$args = array_merge(
								array(
									'APP_ID'=>$this->app_id,
									'APP_SECRET_KEY'=> $this->app_secret_key
									),
								$args);
			$args = array_change_key_case($args,CASE_LOWER);
			$postfix = http_build_query($args);
			
			$url = $this->sh_api_url . $this->api_method . '/?' . $postfix;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); //Remove this when certified
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //Remove this when certified
			$response = curl_exec($ch);
			curl_close($ch);
			//log_message('debug', 'curl : '.$response);
			return json_decode($response,TRUE); 
		} else {
			return FALSE;
		}
	}
	
	function check_response($response){
		if(!$response) {
			log_message('error','Response error : '.$response);
			return array(
						'error'=>'',
						'message' => 'Connection Error'
					);
		} else if ( $this->bad_response($response) ){
			return $response;
		} else {
			return $response; //process response
		}
	}

	function set_api_method($method) {
		$this->api_method = $method;
	}
	
	function bad_response($response) {
		if(isset($response['status']) && $response['status'] == 'OK') return FALSE;
		else return TRUE;
	}
}