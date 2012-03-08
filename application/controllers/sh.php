<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sh extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->config('socialhappen');
		$this->mockuphappen_enabled = $this->config->item('mockuphappen_enable');
	}
	
	function index(){
	}

	function mockuphappen_install() {
		if($this->mockuphappen_enabled){
			echo anchor('sh/install?company_id=1&user_id=1&page_id=1', "Install");
		} else {
			redirect();
		}
	}

	function install()
	{
		$company_id = $this->input->get('company_id');
		$user_id = $this->input->get('user_id');
		$page_id = $this->input->get('page_id');
		
		//User facebook id
		$result = $this->socialhappen->request('request_user_facebook_id', array('user_id' => $user_id));
		$return = array();
		if(isset($result['user_facebook_id'])){
			$user_facebook_id = $result['user_facebook_id'];
		} else {
			log_message('error', 'Cannot get user_facebook_id');
			$return['error'] = 'Cannot get user_facebook_id';
			echo json_encode($return);
			return;
		}
		
		//Facebook page id		
		$result = $this->socialhappen->request('request_facebook_page_id', array('page_id' => $page_id));
		if(isset($result['facebook_page_id'])){
			$facebook_page_id = $result['facebook_page_id'];
		} else {
			log_message('error', 'Cannot get facebook_page_id');
			$return['error'] = 'Cannot get facebook_page_id';
			echo json_encode($return);
			return;
		}
	
		if(!$company_id || !$user_facebook_id){
			log_message('error', 'Insufficient parameters');
			$return['error'] = 'Insufficient parameters';
		} else {
			if($this->mockuphappen_enabled){
				$app_install_id = $this->config->item('mockuphappen_app_install_id');
				$facebook_page_id = $this->config->item('mockuphappen_facebook_page_id');
				$user_facebook_id = $this->facebook->getUser();
				if(!$user_facebook_id) {
					exit('No facebook session, please connect facebook.');
				}
				if($this->setting_model->getOne(array('app_install_id' => $app_install_id))){
					log_message('error', 'mockuphappen restrict user to install again, please remove this app setting first');
				} else if(($exist_setting = $this->setting_model->get(array('facebook_page_id' => $facebook_page_id))) && !empty($exist_setting['app_install_id'])){
					log_message('error', 'mockuphappen restrict user to install again, please remove this app setting first');
				} else {
					$request_install_app_result = array(
						'app_install_id' => $this->config->item('mockuphappen_app_install_id'),
						'app_install_secret_key' => $this->config->item('mockuphappen_app_install_secret_key')
					);
				}
			} else {
				$request_install_app_result = $this->socialhappen->request('request_install_app',
					array(
						'company_id' => $company_id,
						'user_id' => $user_id
					)
				);
			}
			
			if(!isset($request_install_app_result['app_install_id']) || !isset($request_install_app_result['app_install_secret_key'])){
				$return['error'] = 'Can not install app';
			} else {
				$app_install_id = $request_install_app_result['app_install_id'];
				$app_install_secret_key = $request_install_app_result['app_install_secret_key'];
				if($this->mockuphappen_enabled){
					$app_install_secret_key = $this->config->item('mockuphappen_app_install_secret_key');
				}
				$install_page_result = $this->socialhappen->request('request_install_page',
					array(
						'app_install_id' => $app_install_id,
						'app_install_secret_key' => $app_install_secret_key,
						'page_id' => $page_id,
						'user_id' => $user_id,
					)
				);
				
				if(isset($install_page_result['error'])){
					log_message('debug', 'install page platform error '.$install_page_result['error']);
					$return['error'] = 'Cannot install into page';
				} else {
					
					$this->load->library('shapp');
					if(!$this->shapp->setup_app(
						compact('user_id','user_facebook_id','page_id','facebook_page_id',
							'company_id','app_install_id','app_install_secret_key'))){
						log_message('error', 'Setup app failed');
						$return['error'] = 'Setup app failed';
					} else {
						$return['status'] = 'OK';
						$return['app_install_id'] = $app_install_id;
						if($this->mockuphappen_enabled){
							redirect('sh/config?app_install_id='.$app_install_id.'&user_id='.$user_id.'&app_install_secret_key='.$app_install_secret_key);
						}
					}

					//3. Add actions, achievements (config/socialhappen_sctions.php)
					$this->load->config('socialhappen_actions');
					$achievement_infos = $this->config->item('achievement_infos');

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
						
						$this->app_id = $this->config->item('app_id');
						$this->app_secret_key = $this->config->item('app_secret_key');
						$this->load->library('socialhappen', NULL, 'SH');
						$add_achievement_infos_result = $this->socialhappen->request('request_add_achievement_infos', 
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
							$return['error'] = 'Add achievements failed';
						}
					}
				}
			}
		}
		echo json_encode($return);
	}

	function config()
	{
		$app_install_id = $this->input->get('app_install_id');
		$user_id = $this->input->get('user_id');
		$app_install_secret_key = $this->input->get('app_install_secret_key');
		
		if(!$app_install_id || !$user_id || !$app_install_secret_key ){
			show_error('Config parameter incorrect');
		} else {
			redirect('sh/setting/'.$app_install_id.'/'.$user_id.'/'.$app_install_secret_key);
		}
	}

	function setting($app_install_id = NULL, $user_id = NULL, $app_install_secret_key = NULL){
		if(!$app_install_id || !$user_id || !$app_install_secret_key ){
			exit('Insufficient Parameters');
		} else {
			$user_facebook_id = $this->facebook->getUser();
			if(!$user_facebook_id) {
				exit('No facebook session, please connect facebook.');
			}
			$this->load->model('setting_model');
			$setting = $this->setting_model->getOne(compact('app_install_id', 'app_install_secret_key'));
			if(!$setting) {
				exit('Setting not found');
			} else if (!isset($setting['admin_list'][$user_facebook_id])){
				exit('Permission Denied');
			}

			redirect('setting/'.$app_install_id);
			// $this->load->vars(compact('app_install_id', 'user_id', 'app_install_secret_key'));
			
			// $success = $this->input->get('success');

			// //Start form
			// $this->load->library('form_validation');

			// //Form validations
			// $this->form_validation->set_rules('example_field', 'Example Field', 'trim|xss_clean');			
			// $this->form_validation->set_rules('admin_list', 'Admin List', 'trim|xss_clean');			
					
			// $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			// $this->load->vars(array(
			// 	'success' => $success,
			// 	'data' => $setting['data'],
			// 	'admin_list' => implode(',', array_keys($setting['admin_list']))
			// ));
			// if ($this->form_validation->run() == FALSE)
			// {
			// 	$this->load->view('sh_setting');
			// }
			// else
			// {
			// 	//$setting['data']
			// 	$data = array(
			//        	'example_field' => set_value('example_field')
			// 	);
			// 	//$setting['admin_list']
			// 	$admins = explode(',', set_value('admin_list'));
			// 	$admin_list = array();
			// 	foreach($admins as $admin) {
			// 		$admin_list[$admin] = array(); //If each user has data, edit here
			// 	}

			// 	$update_data = array(
			// 		'$set' => array(
			// 			'data' => $data,
			// 			'admin_list' => $admin_list
			// 		)
			// 	);
			
			// 	if ($this->setting_model->update(array('app_install_id' => $app_install_id), $update_data) == TRUE)
			// 	{
			// 		redirect('sh/setting/'.$app_install_id.'/'.$user_id.'/'.$app_install_secret_key.'?success=1');
			// 	}
			// 	else
			// 	{
			// 		exit('Update Error');
			// 	}
			// }

		}
	}
}

/* End of file sh.php */
/* Location: ./application/controllers/sh.php */