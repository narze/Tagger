<?php
//SocialHappen
$config['app_id'] = 4;
$config['app_secret_key'] = 'cd14463efa98e6ee00fde6ccd51a9f6d';
$config['api_url'] = 'https://socialhappen.dyndns.org/socialhappen/api/';

//Mongodb
$config['mongodb_username'] = 'sohap'; 
$config['mongodb_password'] = 'figyfigy';
$config['mongodb_host'] = 'localhost';
$config['mongodb_port'] = 27017;
$config['mongodb_database'] = 'tagger-production';

//Facebook : Fill these if you want to use facebook library
$config['facebook_app_id'] = '184949704958188';
$config['facebook_app_secret'] = '20c297ef212278ca53d35d3f97793658';
$config['facebook_app_scope'] = 'user_about_me,user_photos,publish_stream,user_likes,email';

//MockupHappen : Use only if you want to use app without socialhappen api calls
//This will mockup all api request and return values corresponding to api names
//See mockuphappen config file for for information
//Or you can just use :
//if($this->config->item('mockuphappen_enable')){
//	do something without calling socialhappen api request	
//} else {
//	do socialhappen api request
//}
$config['mockuphappen_enable'] = TRUE;