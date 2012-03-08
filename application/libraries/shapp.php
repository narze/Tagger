<?php
class SHApp {

	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->config('socialhappen');
		$this->required_fields = array('user_id','user_facebook_id','app_install_id','app_install_secret_key','page_id','facebook_page_id','company_id');
	}
	
	/**
	 * Setup app after installed into platform (got app_install_id)
	 */
	function setup_app($data){
		$undefined_fields = array();
		foreach($this->required_fields as $field){
			if(empty($data[$field])){
				$undefined_fields[] = $field;
			}
		}
		if(count($undefined_fields)){
			log_message('error', implode(',', $undefined_fields).' not specified');
			return FALSE;
		} else {
			//1. Add data to setting_model
			$add_data_result = FALSE;
			$this->CI->load->model('setting_model');
			$app_install_id = $data['app_install_id'];
			$app_install_secret_key = $data['app_install_secret_key'];
			$facebook_page_id = $data['facebook_page_id'];
			$setting = array(
				'company_id' => (int) $data['company_id'],
				'app_install_id' => (int) $app_install_id,
				'app_install_secret_key' => $app_install_secret_key,
				'facebook_page_id' => $data['facebook_page_id'],
				'admin_list' => array(
					((string) $data['user_facebook_id']) => array() //preserve empty array for user specific data
				),
				'data' => array() //preserve for app's data
			);

			if($this->CI->setting_model->getOne(array('app_install_id' => $app_install_id))){
				log_message('debug', 'setting with app_install_id='.$app_install_id.' exists, update instead');
				$add_data_result = $this->CI->setting_model->update(
					array('app_install_id' => (int) $app_install_id),
					array('$set' => $setting)
				);
			} else if(($exist_setting = $this->CI->setting_model->get(array('facebook_page_id' => $facebook_page_id))) && !empty($exist_setting['app_install_id'])){
				log_message('debug', 'setting with facebook_page_id='.$facebook_page_id.' exists, update instead');
				$add_data_result = $this->CI->setting_model->update(
					array('facebook_page_id' => (string) $facebook_page_id),
					array('$set' => $setting)
				);
			} else {
				$add_data_result = $this->CI->setting_model->add($setting);
			}
			
			if(!$add_data_result){
				return FALSE;
			}
			
			//2. Add default settings
			if(!$add_default_result = $this->add_default($app_install_id)){
				return FALSE;
			}
			
			return TRUE;
		}
	}

	function add_default($app_install_id){
		//Add default settings here
		return TRUE;
	}
}