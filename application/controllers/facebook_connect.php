<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Facebook_connect extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->app_install_id = $this->socialhappen->get_app_install_id(FALSE);
	}

	function index() {
		if(!$facebook_uid = $this->facebook->getUser()){
			$this->load->vars('fb_root', $this->fb->getFbRoot());
			$this->load->view('facebook_connect');
			echo 'did not connect';
		} else {
			echo "Connected";
		}
	}
}