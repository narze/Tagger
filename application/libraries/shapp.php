<?php
class SHApp {

	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->config('socialhappen');
		$this->sh_api_url = $this->CI->config->item('api_url');
		$this->app_id = $this->CI->config->item('app_id');
		$this->app_secret_key = $this->CI->config->item('app_secret_key');
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
			//1. Add data to app_users model
			$add_data_result = FALSE;
			$this->CI->load->model('app_users');
			$app_install_id = $data['app_install_id'];
			$app_install_secret_key = $data['app_install_secret_key'];
			$facebook_page_id = $data['facebook_page_id'];
			$app_user = array(
				'company_id' => $data['company_id'],
				'app_install_id' => $app_install_id,
				'app_install_secret_key' => $app_install_secret_key,
				'facebook_page_id' => $data['facebook_page_id'],
				'user_facebook_id' => $data['user_facebook_id'],
				'access_token' => 'none'
			);
			if($this->CI->app_users->get(array('app_install_id' => $app_install_id))){
				log_message('debug', 'app_user with app_install_id='.$app_install_id.' exists, update instead');
				$add_data_result = $this->CI->app_users->update(
					array('app_install_id' => $app_install_id),
					$app_user
				);
			} else if(($app_install_data = $this->CI->app_users->get(array('facebook_page_id' => $facebook_page_id))) && !empty($app_install_data['app_install_id'])){
				log_message('debug', 'app_user with facebook_page_id='.$facebook_page_id.' exists, update instead');
				$add_data_result = $this->CI->app_users->update(
					array('facebook_page_id' => $facebook_page_id),
					$app_user
				);
			} else {
				$add_data_result = $this->CI->app_users->add($app_user);
			}

			if(!$add_data_result){
				return FALSE;
			}
			
			//2. Add default settings
			if(!$add_default_result = $this->add_default($app_install_id)){
				return FALSE;
			}
			
			//3. Add actions, achievements (config/socialhappen_sctions.php)
			$this->CI->load->config('socialhappen_actions');
			$achievement_infos = $this->CI->config->item('achievement_infos');

			//Replace achievement criterias {app_install_id}
			if(is_array($achievement_infos)){
				foreach($achievement_infos as &$info){
					foreach($info['criteria'] as $key => $value){
						$new_key = str_replace('{app_install_id}', $app_install_id, $key);
						if($new_key != $key){
							$info['criteria'][$new_key] = $info['criteria'][$key];
							unset($info['criteria'][$key]);
						}
					}
				}
				unset($info);

				$achievement_infos = base64_encode(json_encode($achievement_infos));
				
				$this->CI->load->library('socialhappen', NULL, 'SH');
				$add_achievement_infos_result = $this->CI->SH->request('request_add_achievement_infos', 
					array(
						'app_id' => $this->app_id,
						'app_install_id' => $app_install_id,
						'app_secret_key' => $this->app_secret_key,
						'app_install_secret_key' => $app_install_secret_key,
						'achievement_infos' => $achievement_infos
					)
				);
				if(isset($add_achievement_infos_result['error'])){
					log_message('error', 'Add achievements failed');
					return FALSE;
				}
			}
			
			return TRUE;
		}
	}

	function add_default($app_install_id){
		//Add default settings here
		return TRUE;
	}
}