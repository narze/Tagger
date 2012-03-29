<html>
    <head>
    	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.css');?>" />
        <link rel="stylesheet" href="<?php echo base_url('assets/css/responsive.css');?>" />
        <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css'); ?>" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    </head>
    <body>
    <script>
			function upload() {
				var url = '<?php echo base_url().'tag/uploadToFacebook/'.$app_install_id;?>';
				$(function(){
				  $.getJSON(url, function(result){
				  	if(result.success == true) {
				  		console.log(result);
				  		$('.popup-success').show();
				  		$('#upload').prop("onclick", null);
				  	} else {
				  		//error
				  		console.log(result);
				  	}
				  })
				});
			}
    </script>
       <a id="upload" href="#" onclick="upload()"><img src="<?php echo base_url().'uploads/'.$filename.'.png';?>" /></a>
       <div class="popup-success">
            <img src="<?php echo base_url('assets/images/popup-success.png');?>" alt="">
        </div>
    </body>
</html>
