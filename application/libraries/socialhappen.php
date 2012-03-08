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
		$this->CI =& get_instance();
		$this->CI->load->config('socialhappen');
		$this->sh_api_url = $this->CI->config->item('api_url');
		$this->app_id = $this->CI->config->item('app_id');
		$this->app_secret_key = $this->CI->config->item('app_secret_key');
		$this->mockuphappen_enable = $this->CI->config->item('mockuphappen_enable');
	}
	
	function request($method = null, $args = array()){
		if($this->CI->config->item('mockuphappen_enable')) {
			$this->CI->load->config('mockuphappen');
			return $this->CI->config->item("mockuphappen_{$method}");
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

	function get_app_install_id($force_app_installed = TRUE) {
		$segments = $this->CI->uri->segment_array();
		foreach($segments as $segment) {
			if(is_numeric($segment)){
				$segment = (int) $segment;
				$this->CI->load->vars('app_install_id', $segment);
				$this->app_install_id = $segment;
				break;
			}
		}
		if(!isset($this->app_install_id)) {
			$this->CI->load->vars('app_install_id', FALSE);
			$this->app_install_id = FALSE; //Not found any numeric segment
		}

		//Try redirect if found app_install_id in mockuphappen config or setting model
		if(!$this->app_install_id) {
			if($this->CI->config->item('mockuphappen_enable')){
				$facebook_page_id = $this->CI->config->item('mockuphappen_facebook_page_id');
			} else if ($signed_request = $this->CI->facebook->getSignedRequest()) {
				//Get from signed_request in facebook page
				if(!isset($signed_request['page']['id'])){
					exit('Not in page tab');
				}
				$facebook_page_id = $signed_request['page']['id'];
			} else {
				exit('Cannot get facebook signed request');
			}
			if($setting = $this->CI->setting_model->getOne(array('facebook_page_id' => $facebook_page_id))){
				redirect($this->CI->uri->uri_string().'/'.$setting['app_install_id']);
			} else {
				if($force_app_installed) {
					exit('App not installed yet');
				}
			}
		} else {
			if(!$setting = $this->CI->setting_model->getOne(array('app_install_id' => $this->app_install_id))){
				if($force_app_installed) {
					exit('App not installed yet');
				}
			}
		}
	}
}