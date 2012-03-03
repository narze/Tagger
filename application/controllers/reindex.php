<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reindex extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		$this->load->model('setting_model');
		$this->load->model('user_model');

		$this->setting_model->recreateIndex();
		$this->user_model->recreateIndex();

		redirect();
	}
}