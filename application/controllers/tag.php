<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tag extends CI_Controller {

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
		} else {
			$this->load->model('user_model');
			if($this->user = $this->user_model->getOne(array(
					'app_install_id' => $this->app_install_id,
					'facebook_uid' => $this->facebook_uid))){

				} else {
				redirect('register/'.$this->app_install_id);
			}
		}
	}

	function index(){
		$this->load->library('form_validation');
		if(isset($this->user['tagged']) && count($this->user['tagged'])){
			echo 'You cannot tag anymore, please wait until the end of this campaign';
		} else {

			$this->load->vars(array(
				'facebook_uid' => $this->facebook_uid,
				'fb_root' => $this->fb->getFbRoot()
			));
			$this->load->view('tag');
		}
	}

	function execute(){
		//Remove old upload image if exists
		$user = $this->user;
		if(isset($user['tag_image'])){
			$image_path = FCPATH.'uploads/'.$user['tag_image'].'.png';
			unlink($image_path);
		}

		$this->load->model('setting_model');
		$setting = $this->setting_model->getOne(array('app_install_id' => $this->app_install_id));
		if(!$setting) {
			redirect($this->app_install_id);
		}
		$tagged_facebook_uids = explode(',',$this->input->post('tagged'));
		if(count($tagged_facebook_uids) < 5){
			echo anchor('tag/'.$this->app_install_id, 'back');
			exit("Please tag 5 people");
		}
		//1. save images into vars
		$images = array();
		foreach($tagged_facebook_uids as $key => $value){
			$images[$key] = imagecreatefromstring(file_get_contents("http://graph.facebook.com/{$value}/picture"));
		}

		//2. get template image and x,y for each image
		$background_image_size = getimagesize($setting['background_image_url']);
		$background_image_width = $background_image_size[0];
		$background_image_height = $background_image_size[1];
		$background_image = imagecreatefrompng($setting['background_image_url']);
		$tag_x[0] = $setting['tag_1_x'];
		$tag_y[0] = $setting['tag_1_y'];
		$tag_x[1] = $setting['tag_2_x'];
		$tag_y[1] = $setting['tag_2_y'];
		$tag_x[2] = $setting['tag_3_x'];
		$tag_y[2] = $setting['tag_3_y'];
		$tag_x[3] = $setting['tag_4_x'];
		$tag_y[3] = $setting['tag_4_y'];
		$tag_x[4] = $setting['tag_5_x'];
		$tag_y[4] = $setting['tag_5_y'];

		//3. create a new image
		// header ('Content-Type: image/png'); //for test
		// $background_image = @imagecreatetruecolor(600, 400); // create new blank image
		$text_color = imagecolorallocate($background_image, 233, 14, 91);
		imagestring($background_image, 1, 5, 5,  'U R TAGGED', $text_color);
		imagecopymerge($background_image, $images[0], $tag_x[0], $tag_y[0], 0, 0, 50, 50, 100);
		imagecopymerge($background_image, $images[1], $tag_x[1], $tag_y[1], 0, 0, 50, 50, 100);
		imagecopymerge($background_image, $images[2], $tag_x[2], $tag_y[2], 0, 0, 50, 50, 100);
		imagecopymerge($background_image, $images[3], $tag_x[3], $tag_y[3], 0, 0, 50, 50, 100);
		imagecopymerge($background_image, $images[4], $tag_x[4], $tag_y[4], 0, 0, 50, 50, 100);
		// imagecopymerge(dst_im, src_im, dst_x, dst_y, src_x, src_y, src_w, src_h, pct)
		//set random filename
		mt_srand();
		$filename = md5(uniqid(mt_rand()));
		imagepng($background_image, $filepath = FCPATH.'uploads/'.$filename.'.png');
		$this->load->model('user_model');
		$this->user_model->update(array(
			'app_install_id' => $this->app_install_id,
			'facebook_uid' => $this->facebook_uid
			), array(
				'$set' => array(
					'tag_image' => $filename,
					'tagged_list' => $tagged_facebook_uids
				)
			)
		);
		$this->load->vars(array(
			'filename' => $filename
		));
		$this->load->view('tag_execute');
	}

	function uploadToFacebook() {
		$user = $this->user;
		if(!isset($user['tag_image'])){
			exit("Image not found");
		}
		$tagged_facebook_uids = $user['tagged_list'];
		$filepath = FCPATH.'uploads/'.$user['tag_image'].'.png';
		$this->load->model('setting_model');
		$setting = $this->setting_model->getOne(array('app_install_id' => $this->app_install_id));
		if(!$setting) {
			redirect($this->app_install_id);
		}
		//4. upload to facebook, if album not exists, create it
		$photo_message = $setting['photo_message'];
		
		//upload image
		$this->facebook->setFileUploadSupport(true);

		$args = array(
			'message' => $photo_message,
			'image' => '@'.$filepath
		);
		$data = $this->facebook->api('me/photos', 'POST', $args);
		

		//5. tag
		$image_size = @getimagesize($filepath);
		$image_width = $image_size[0];
		$image_height = $image_size[1];
		$tag_x[0] = $setting['tag_1_x'];
		$tag_y[0] = $setting['tag_1_y'];
		$tag_x[1] = $setting['tag_2_x'];
		$tag_y[1] = $setting['tag_2_y'];
		$tag_x[2] = $setting['tag_3_x'];
		$tag_y[2] = $setting['tag_3_y'];
		$tag_x[3] = $setting['tag_4_x'];
		$tag_y[3] = $setting['tag_4_y'];
		$tag_x[4] = $setting['tag_5_x'];
		$tag_y[4] = $setting['tag_5_y'];
		//assigning users to tag and cordinates
		foreach($tagged_facebook_uids as $key => $value){
			$argstag = array(
				'to' => $value,
				'x' => ($tag_x[$key]+25)*100/$image_width,
				'y' => ($tag_y[$key]+25)*100/$image_height
			);
			$datatag = $this->facebook->api('/' . $data['id'] . '/tags', 'post', $argstag);
			if($datatag){ echo 'tagged '. $value; }
		}
		$photo = $this->facebook->api($data['id']);

		echo '<a href="'.$photo['link'].'">Success! Check your facebook wall now</a>';

		//6. remove temp file
		$remove = unlink($filepath);

		//7.Update user data
		$this->load->model('user_model');
		$update = array(
			'$set' => array(
				'tagged' => TRUE
			),
			'$unset' => array('tag_image' => TRUE)
		);
		if($result = $this->user_model->update(array(
				'app_install_id' => $this->app_install_id,
				'facebook_uid' => $this->facebook_uid
				), $update)){
			echo 'Tagged successfully';
			echo '<pre>';
			var_dump($tagged_facebook_uids);
			echo '</pre>';
		} else {
			//update user model error
		}
	}
}
