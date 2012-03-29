<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('fb');
		$this->load->model('setting_model');
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
			if($setting = $this->setting_model->getOne(array('facebook_page_id' => $facebook_page_id))){
				redirect($setting['app_install_id']);
			} else {
				exit('App not installed yet');
			}
		} else {
			if(!$this->setting = $this->setting_model->getOne(array('app_install_id' => $this->app_install_id))){
				exit('App not installed yet');
			}
			$this->load->vars('app_install_id', $this->app_install_id);
		}
	}

	function index(){
		date_default_timezone_set('UTC');
 		if(isset($this->setting['data']['start']) &&  date('Y-m-d H:i:s') < $this->setting['data']['start']) {
			$this->load->view('not_started');
		} else if (isset($this->setting['data']['end']) && $this->setting['data']['end'] <= date('Y-m-d H:i:s')) {
			$this->load->view('timeout');
		} else if(!$facebook_uid = $this->facebook->getUser()){
			$this->load->vars(array(
				'fb_root' => $this->fb->getFbRoot(),
				'landing_image_url' => $this->setting['landing_image_url']
			));
			$this->load->view('home');
		} else {
			$this->load->model('user_model');
			if($user = $this->user_model->getOne(array(
				'facebook_uid' => $facebook_uid,
				'app_install_id' => $this->app_install_id))){
				if($user['tagged']){
					echo '<p>1. Now you have tagged '. count($user['image_url_list']).' times</p>';
					echo '<p>2. and you have tagged '. count($user['tagged_list']).' people</p>';
					echo '<p>3. People you have tagged last time :</p>';
					foreach($user['recent_tagged_list'] as $tagged_facebook_id) {
						echo '<img src="https://graph.facebook.com/'.$tagged_facebook_id.'/picture" />';
					}
					echo '<p>4. All people you have tagged so far :</p>';
					foreach($user['tagged_list'] as $tagged_facebook_id) {
						echo '<img src="https://graph.facebook.com/'.$tagged_facebook_id.'/picture" />';
					}
					echo '<p>'.anchor('tag/'.$this->app_install_id,'Tag again').'</p>';
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
