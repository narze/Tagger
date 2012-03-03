<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('fb');
		$segments = $this->uri->segment_array();
		$this->app_install_id = end($segments);
		if(!is_numeric($this->app_install_id)){
			
			if($this->config->item('mockuphappen_enable')){
				$this->load->config('mockuphappen');
				$facebook_page_id = $this->config->item('mockuphappen_facebook_page_id');
			} else { //Get from signed_request
				$signed_request = $this->facebook->getSignedRequest();
				if(!isset($signed_request['page']['id'])){
					exit('Not in page tab');
				}
				$facebook_page_id = $signed_request['page']['id'];
			}
			$this->load->model('setting_model');
			if($setting = $this->setting_model->getOne(array('facebook_page_id' => $facebook_page_id))){
				redirect($setting['app_install_id']);
			} else {
				exit('App not installed yet');
			}
		} else {
			$this->load->vars('app_install_id', $this->app_install_id);
		}
	}

	function index(){
		if(!$facebook_uid = $this->facebook->getUser()){
			$this->load->vars('fb_root', $this->fb->getFbRoot());
			$this->load->view('home');
		} else {
			$this->load->model('user_model');
			if($user = $this->user_model->getOne(array(
				'facebook_uid' => $facebook_uid,
				'app_install_id' => $this->app_install_id))){
				if(isset($user['tagged']) && $user['tagged']){
					echo 'You have tagged people, please wait for the end of this campaign';
					echo '<pre>';
					var_dump($user['tagged_list']);
					echo '</pre>';
				} else {
					redirect('tag/'.$this->app_install_id);
				}
			} else {
				redirect('register/'.$this->app_install_id);
			}
		}
	}
	//Install app to page tab
	//echo 'https://www.facebook.com/dialog/pagetab?app_id=308560122540425&next='.urlencode('https://apps.localhost.com/tagger');
}