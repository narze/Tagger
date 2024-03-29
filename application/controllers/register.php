<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {

	function __construct(){
		parent::__construct();
		$segments = $this->uri->segment_array();
		$this->app_install_id = end($segments);
		if(!is_numeric($this->app_install_id)){
			echo json_encode("false");
			exit();
		} else {
			if(!$this->setting = $this->setting_model->getOne(array('app_install_id' => $this->app_install_id))){
				exit('App not installed yet');
			}
			$this->load->vars('app_install_id', $this->app_install_id);
		}
		$this->load->library('fb');
	}

	function index(){
		$this->load->model('user_model');
		if((!$facebook_uid = $this->facebook->getUser()) || $this->user_model->getOne(array(
				'app_install_id' => $this->app_install_id,
				'facebook_uid' => $facebook_uid))){
			redirect('home/'.$this->app_install_id);
		} else {
			$facebook_user = $this->facebook->api('me');
		}
		$this->load->library('form_validation');
		$this->load->helper('url');

		$this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[50]');			
		$this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[50]');			
		$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[255]');			
			
		$this->form_validation->set_error_delimiters('<span class="help-inline">', '</span>');
		$this->load->vars(array(
			'facebook_uid' => $facebook_uid,
			'app_install_id' => $this->app_install_id,
			'facebook_user' => $facebook_user,
			'template_name' => $this->setting['template_name'],
			'template_images' => $this->setting['template_images']
		));

		if ($this->form_validation->run() == FALSE)	{
			$this->load->view('register');
		} else {			
			$form_data = array(
		       	'app_install_id' => $this->app_install_id,
		       	'facebook_uid' => $facebook_uid,
		       	'profile' => array(
		       		'first_name' => set_value('first_name'),
			       	'last_name' => set_value('last_name'),
			       	'email' => set_value('email')
			    	),
			    	'tagged' => FALSE
			);
		
			$this->load->model('user_model');
			$result = $this->user_model->add($form_data);
			if ($result['ok']) {
				redirect('tag/'.$this->app_install_id);  
			} else {
				echo 'An error occurred saving your information. Please try again later';
			}
		}
	}
}
