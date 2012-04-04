<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends CI_Controller {

	function __construct(){
		parent::__construct();
		$segments = $this->uri->segment_array();
		$this->app_install_id = end($segments);
		if(!is_numeric($this->app_install_id)){
			echo json_encode("false");
			exit();
		} else {
			$this->load->vars('app_install_id', $this->app_install_id);
		}
		$this->load->library('fb');
		if(!$this->facebook_uid = $this->facebook->getUser()){
			redirect($this->app_install_id);
		}
	}

	function index(){
		$success = $this->input->get('success');
		$this->load->model('setting_model');
		$setting = $this->setting_model->getOne(array('app_install_id' => (string) $this->app_install_id));
		if(!$setting) {
			exit('Setting not found');
		} else if (!isset($setting['admin_list'][$this->facebook_uid])){
			exit('Permission Denied');
		}
		$this->load->library('form_validation');

		$this->form_validation->set_rules('photo_message', 'Photo Message', 'trim|xss_clean');			
		$this->form_validation->set_rules('tag_1_x', 'Tag 1 (x)', 'required|trim|xss_clean|is_numeric|max_length[4]');			
		$this->form_validation->set_rules('tag_1_y', 'Tag 1 (y)', 'required|trim|xss_clean|is_numeric|max_length[4]');			
		$this->form_validation->set_rules('tag_2_x', 'Tag 2 (x)', 'required|trim|xss_clean|is_numeric|max_length[4]');			
		$this->form_validation->set_rules('tag_2_y', 'Tag 2 (y)', 'required|trim|xss_clean|is_numeric|max_length[4]');			
		$this->form_validation->set_rules('tag_3_x', 'Tag 3 (x)', 'required|trim|xss_clean|is_numeric|max_length[4]');			
		$this->form_validation->set_rules('tag_3_y', 'Tag 3 (y)', 'required|trim|xss_clean|is_numeric|max_length[4]');			
		$this->form_validation->set_rules('tag_4_x', 'Tag 4 (x)', 'required|trim|xss_clean|is_numeric|max_length[4]');			
		$this->form_validation->set_rules('tag_4_y', 'Tag 4 (y)', 'required|trim|xss_clean|is_numeric|max_length[4]');			
		$this->form_validation->set_rules('tag_5_x', 'Tag 5 (x)', 'required|trim|xss_clean|is_numeric|max_length[4]');			
		$this->form_validation->set_rules('tag_5_y', 'Tag 5 (y)', 'required|trim|xss_clean|is_numeric|max_length[4]');		
		$this->form_validation->set_rules('template_name', 'Template name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('template_main', 'Template : Background image', 'required|trim|xss_clean');
		$this->form_validation->set_rules('template_register', 'Template : Main page image', 'required|trim|xss_clean');
		$this->form_validation->set_rules('template_background', 'Template : Register page image', 'required|trim|xss_clean');
		$this->form_validation->set_rules('template_success_popup', 'Template : Success popup image', 'required|trim|xss_clean');
		$this->form_validation->set_rules('facebook_page_id', 'Facebook Page ID', '');
		$this->form_validation->set_rules('start', 'Start time', '');
		$this->form_validation->set_rules('end', 'End time', '');
		$this->form_validation->set_rules('thumbnail_size', 'Thumbnail size', '');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		$form_url = 'setting/'.$this->app_install_id;
		$facebook_redirect = urlencode(base_url($form_url));
		$facebook_app_id = $this->config->item('facebook_app_id');
		$this->load->vars(array(
			'success' => $success,
			'app_install_id' => $this->app_install_id,
			'setting' => $setting['data'],
			'facebook_page_id' => $setting['facebook_page_id'],
			'facebook_add_page_app_url' => "https://www.facebook.com/dialog/pagetab?app_id={$facebook_app_id}
&display=page&next={$facebook_redirect}"
		));
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('setting');
		}
		else
		{
			$form_data = array(
		       	'photo_message' => htmlspecialchars_decode(set_value('photo_message')),
		       	'tag_1_x' => set_value('tag_1_x'),
		       	'tag_1_y' => set_value('tag_1_y'),
		       	'tag_2_x' => set_value('tag_2_x'),
		       	'tag_2_y' => set_value('tag_2_y'),
		       	'tag_3_x' => set_value('tag_3_x'),
		       	'tag_3_y' => set_value('tag_3_y'),
		       	'tag_4_x' => set_value('tag_4_x'),
		       	'tag_4_y' => set_value('tag_4_y'),
		       	'tag_5_x' => set_value('tag_5_x'),
		       	'tag_5_y' => set_value('tag_5_y'),
		       	'start' => set_value('start'),
		       	'end' => set_value('end'),
		       	'thumbnail_size' => set_value('thumbnail_size'),
		       	'template_name' => set_value('template_name'),
		       	'template_images' => array(
		       		'main' => set_value('template_main'),
		       		'register' => set_value('template_register'),
		       		'background' => set_value('template_background'),
		       		'success_popup' => set_value('template_success_popup'),
		       	)

			);

			$data = array(
				'$set' => array(
					'data' => $form_data,
					'facebook_page_id' => set_value('facebook_page_id')
				)
			);
		
			if ($this->setting_model->update(array('app_install_id' => $this->app_install_id), $data) == TRUE)
			{
				redirect('setting/'.$this->app_install_id.'?success=1');
			}
			else
			{
				echo 'An error occurred saving your information. Please try again later';
			}
		}
	}

	function report_csv() {
		$this->load->model('setting_model');
		$setting = $this->setting_model->getOne(array('app_install_id' => (string) $this->app_install_id));
		if(!$setting) {
			exit('Setting not found');
		} else if (!isset($setting['admin_list'][$this->facebook_uid])){
			exit('Permission Denied');
		}
		$this->load->model('user_model');
		$users = $this->user_model->get(array('app_install_id' => (string) $this->app_install_id));

		$this->load->vars('users', $users);
		$this->load->view('report_csv');
	}

	function report_list() {
		$this->load->model('setting_model');
		$setting = $this->setting_model->getOne(array('app_install_id' => (string) $this->app_install_id));
		if(!$setting) {
			exit('Setting not found');
		} else if (!isset($setting['admin_list'][$this->facebook_uid])){
			exit('Permission Denied');
		}
		$this->load->model('user_model');
		$users = $this->user_model->get(array('app_install_id' => (string) $this->app_install_id));

		$this->load->vars('users', $users);
		$this->load->view('report_list');
	}

	function report() {
		$this->load->model('setting_model');
		$facebook_uid = $this->input->get('facebook_uid');
		$setting = $this->setting_model->getOne(array('app_install_id' => (string) $this->app_install_id));
		if(!$setting) {
			exit('Setting not found');
		} else if (!isset($setting['admin_list'][$this->facebook_uid])){
			exit('Permission Denied');
		}
		$this->load->model('user_model');
		$user = $this->user_model->getOne(array(
			'app_install_id' => (string) $this->app_install_id,
			'facebook_uid' => $facebook_uid
		));

		// echo '<pre>';
		// var_dump($user);
		// echo '</pre>';
		$facebook_user = $this->facebook->api($user['facebook_uid']);

		$csv = array();
		$csv['first_name'] = isset($user['profile']['first_name']) ? $user['profile']['first_name'] : '-';
		$csv['last_name'] = isset($user['profile']['last_name']) ? $user['profile']['last_name'] : '-';
		$csv['email'] = isset($user['profile']['email']) ? $user['profile']['email'] : '-';
		$csv['facebook_name'] = isset($facebook_user['name']) ? $facebook_user['name'] : '-';
		$csv['facebook_gender'] = isset($facebook_user['gender']) ? $facebook_user['gender'] : '-';
		$csv['facebook_link'] = isset($facebook_user['link']) ? $facebook_user['link'] : '-';
		$csv['facebook_uid'] = $user['facebook_uid'];
		$csv['facebook_name_1'] = '-';
		$csv['facebook_gender_1'] = '-';
		$csv['facebook_link_1'] = '-';
		$csv['facebook_name_2'] = '-';
		$csv['facebook_gender_2'] = '-';
		$csv['facebook_link_2'] = '-';
		$csv['facebook_name_3'] = '-';
		$csv['facebook_gender_3'] = '-';
		$csv['facebook_link_3'] = '-';
		$csv['facebook_name_4'] = '-';
		$csv['facebook_gender_4'] = '-';
		$csv['facebook_link_4'] = '-';
		$csv['facebook_name_5'] = '-';
		$csv['facebook_gender_5'] = '-';
		$csv['facebook_link_5'] = '-';

		if(isset($user['tagged_list'])) {
			$key = 1;
			foreach($user['tagged_list'] as $tagged_facebook_uid){
				$tagged_facebook_user = $this->facebook->api($tagged_facebook_uid);
				$csv['facebook_name_'.$key] = isset($tagged_facebook_user['name']) ? $tagged_facebook_user['name'] : '-';
				$csv['facebook_gender_'.$key] = isset($tagged_facebook_user['gender']) ? $tagged_facebook_user['gender'] : '-';
				$csv['facebook_link_'.$key] = isset($tagged_facebook_user['link']) ? $tagged_facebook_user['link'] : '-';
				$key++;
			}
		}

		$csv =  implode(', ', $csv);

		$this->load->vars('user', $user);
		$this->load->vars('csv', $csv);
		$this->load->view('report');
	}
}