<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sh extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->config('socialhappen');
	}
	
	function index(){
	}

	function install()
	{
		$company_id = $this->input->get('company_id');
		$user_id = $this->input->get('user_id');
		$page_id = $this->input->get('page_id');
				
		//init SH object
		$this->load->library('socialhappen', NULL, 'SH');
		
		//User facebook id
		$result = $this->SH->request('request_user_facebook_id', array('user_id' => $user_id));
		$return = array();
		if(isset($result['user_facebook_id'])){
			$user_facebook_id = $result['user_facebook_id'];
		} else {
			log_message('error', 'Cannot get user_facebook_id');
			$return['error'] = 'Cannot get user_facebook_id';
			echo json_encode($return);
			return;
		}
		
		//Face book page id		
		$result = $this->SH->request('request_facebook_page_id', array('page_id' => $page_id));
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
			$request_install_app_result = $this->SH->request('request_install_app',
				array(
					'company_id' => $company_id,
					'user_id' => $user_id
				)
			);
			
			if(!isset($request_install_app_result['app_install_id']) || !isset($request_install_app_result['app_install_secret_key'])){
				$return['error'] = 'Can not install app';
			} else {
				$app_install_id = $request_install_app_result['app_install_id'];
				$app_install_secret_key = $request_install_app_result['app_install_secret_key'];

				$install_page_result = $this->SH->request('request_install_page',
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
			redirect(site_url('admin/dashboard/'.$app_install_id.'/'.$user_id.'/'.$app_install_secret_key));
		}
	}
}

/* End of file sh.php */
/* Location: ./application/controllers/sh.php */