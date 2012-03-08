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
		$this->form_validation->set_rules('background_image_url', 'Background Image Url', '');
		$this->form_validation->set_rules('facebook_page_id', 'Facebook Page ID', '');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		$this->load->vars(array(
			'success' => $success,
			'app_install_id' => $this->app_install_id,
			'setting' => $setting['data'],
			'facebook_page_id' => $setting['facebook_page_id']
		));
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('setting');
		}
		else
		{
			$form_data = array(
		       	'photo_message' => set_value('photo_message'),
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
		       	'background_image_url' => set_value('background_image_url')
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
}