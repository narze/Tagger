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
		date_default_timezone_set('UTC');
		$this->load->model('setting_model');
		$setting = $this->setting_model->getOne(array('app_install_id' => $this->app_install_id));
		if(!$setting) {
			redirect($this->app_install_id);
		} else if (isset($setting['end']) && $setting['end'] <= date('Y-m-d H:i:s')) {
			redirect($this->app_install_id);
		}
		
		$this->load->library('form_validation');
		$this->load->vars(array(
			'facebook_uid' => $this->facebook_uid,
			'fb_root' => $this->fb->getFbRoot()
		));
		$this->load->view('tag');
	
	}

	function execute(){
		//Remove old upload image if exists
		$oldsize = 50; //facebook thumbnail
		$newsize = 70; //temp
		$user = $this->user;
		if(isset($user['tag_image'])){
			$image_path = FCPATH.'uploads/'.$user['tag_image'].'.png';
			if(is_writable($image_path)) {
				unlink($image_path);
			}
		}

		$this->load->model('setting_model');
		$setting = $this->setting_model->getOne(array('app_install_id' => $this->app_install_id));
		if(!$setting) {
			redirect($this->app_install_id);
		}

		$setting_data = $setting['data'];
		$tagged_facebook_uids = explode(',',$this->input->post('tagged'));
		if(count($tagged_facebook_uids) < 5){
			echo anchor('tag/'.$this->app_install_id, 'back');
			exit("Please tag 5 people");
		}
		//1. save images into vars
		$images = $resized = array();
		foreach($tagged_facebook_uids as $key => $value){
			$images[$key] = imagecreatefromstring(file_get_contents("http://graph.facebook.com/{$value}/picture"));
			$resized[$key] = imagecreatetruecolor($newsize, $newsize);
			imagecopyresampled($resized[$key], $images[$key], 0, 0, 0, 0, $newsize, $newsize, $oldsize, $oldsize);
			// imagecopyresampled(dst_image, src_image, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
		}

		//2. get template image and x,y for each image
		$background_image_size = getimagesize($setting_data['background_image_url']);
		$background_image_width = $background_image_size[0];
		$background_image_height = $background_image_size[1];
		if(strrpos($setting_data['background_image_url'], '.png') !== FALSE) {
			$background_image = imagecreatefrompng($setting_data['background_image_url']);
		} else if(strrpos($setting_data['background_image_url'], '.jpg') !== FALSE) {
			$background_image = imagecreatefromjpeg($setting_data['background_image_url']);
		} else if(strrpos($setting_data['background_image_url'], '.gif') !== FALSE) {
			$background_image = imagecreatefromgif($setting_data['background_image_url']);
		}
		$tag_x[0] = $setting_data['tag_1_x'];
		$tag_y[0] = $setting_data['tag_1_y'];
		$tag_x[1] = $setting_data['tag_2_x'];
		$tag_y[1] = $setting_data['tag_2_y'];
		$tag_x[2] = $setting_data['tag_3_x'];
		$tag_y[2] = $setting_data['tag_3_y'];
		$tag_x[3] = $setting_data['tag_4_x'];
		$tag_y[3] = $setting_data['tag_4_y'];
		$tag_x[4] = $setting_data['tag_5_x'];
		$tag_y[4] = $setting_data['tag_5_y'];

		//3. create a new image
		// header ('Content-Type: image/png'); //for test
		// $background_image = @imagecreatetruecolor(600, 400); // create new blank image
		$text_color = imagecolorallocate($background_image, 233, 14, 91);
		// imagestring($background_image, 1, 5, 5,  'U R TAGGED', $text_color);
		imagecopymerge($background_image, $resized[0], $tag_x[0], $tag_y[0], 0, 0, $newsize, $newsize, 100);
		imagecopymerge($background_image, $resized[1], $tag_x[1], $tag_y[1], 0, 0, $newsize, $newsize, 100);
		imagecopymerge($background_image, $resized[2], $tag_x[2], $tag_y[2], 0, 0, $newsize, $newsize, 100);
		imagecopymerge($background_image, $resized[3], $tag_x[3], $tag_y[3], 0, 0, $newsize, $newsize, 100);
		imagecopymerge($background_image, $resized[4], $tag_x[4], $tag_y[4], 0, 0, $newsize, $newsize, 100);
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
					'recent_tagged_list' => $tagged_facebook_uids,
				),
				'$addToSet' => array('tagged_list' => array('$each' => $tagged_facebook_uids))
			)
		);
		$this->load->vars(array(
			'filename' => $filename
		));
		// $this->load->view('tag_execute');
		redirect('tag/uploadToFacebook/'.$this->app_install_id);
	}

	function uploadToFacebook() {
		$user = $this->user;
		if(!isset($user['tag_image'])){
			echo json_encode(array('success' => FALSE, 'error' => 'Image not found'));
			return;
		}
		$tagged_facebook_uids = $user['recent_tagged_list'];
		$filepath = FCPATH.'uploads/'.$user['tag_image'].'.png';
		$this->load->model('setting_model');
		$setting = $this->setting_model->getOne(array('app_install_id' => $this->app_install_id));
		if(!$setting) {
			redirect($this->app_install_id);
		}
		$setting_data = $setting['data'];
		//4. upload to facebook, if album not exists, create it
		$photo_message = $setting_data['photo_message'];
		
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
		$tag_x[0] = $setting_data['tag_1_x'];
		$tag_y[0] = $setting_data['tag_1_y'];
		$tag_x[1] = $setting_data['tag_2_x'];
		$tag_y[1] = $setting_data['tag_2_y'];
		$tag_x[2] = $setting_data['tag_3_x'];
		$tag_y[2] = $setting_data['tag_3_y'];
		$tag_x[3] = $setting_data['tag_4_x'];
		$tag_y[3] = $setting_data['tag_4_y'];
		$tag_x[4] = $setting_data['tag_5_x'];
		$tag_y[4] = $setting_data['tag_5_y'];
		//assigning users to tag and cordinates
		foreach($tagged_facebook_uids as $key => $value){
			$argstag = array(
				'to' => $value,
				'x' => ($tag_x[$key]+25)*100/$image_width,
				'y' => ($tag_y[$key]+25)*100/$image_height
			);
			// $datatag = $this->facebook->api('/' . $data['id'] . '/tags', 'post', $argstag);
		}

		$photo = $this->facebook->api($data['id']); 
		// echo '<a href="'.$photo['link'].'">Success! Check your facebook wall now</a>';

		//6. remove temp file
		if(is_writable($filepath)) {
			unlink($filepath);
		}

		//7.Update user data
		$this->load->model('user_model');
		$update = array(
			'$set' => array(
				'tagged' => TRUE,
				'image_url' => $photo['link']
			),
			'$unset' => array('tag_image' => TRUE)
		);
		if($result = $this->user_model->update(array(
				'app_install_id' => $this->app_install_id,
				'facebook_uid' => $this->facebook_uid
				), $update)){
			echo json_encode(array('success' => TRUE));
			// $this->load->view('tag_success');
			// echo 'Tagged successfully';
			// echo '<pre>';
			// var_dump($tagged_facebook_uids);
			// echo '</pre>';
		} else {
			//update user model error
		}
	}
}
