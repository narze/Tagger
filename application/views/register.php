<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.css');?>" />
        <link rel="stylesheet" href="<?php echo base_url('assets/css/responsive.css');?>" />
        <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css'); ?>" />
    </head>
    <body>
        <div class="body-wrapper">
                <div class="like-us">
                        <img src="<?php echo base_url('assets/images/like-us.png');?>" alt="">
                </div>
                <div class="header-container">
                        <a class="button-detail" target="_top" href="http://www.fashionisland.co.th/activities/detail/250"></a>
                </div>
                <div class="content-container">
                        <?php $attributes = array('class' => '', 'id' => '');
                        echo form_open('register/'.$app_install_id, $attributes); ?>
                        <div class="profile-box">
                                <p>Welcome,</p>
                                <p class="profile-name"><?php  echo $facebook_user['name']; ?></p>
                                <img class="profile-pic" src="https://graph.facebook.com/<?php echo $facebook_uid;?>/picture" />
                        </div>
                        <div class="white-box">
                                <h2>Sign Up</h2>

                                <label for="email">Email</label>
                                <input id="email" class="span3" type="text" name="email" maxlength="255" value="<?php echo set_value('email',$facebook_user['email']); ?>"  />
                                <?php echo form_error('email'); ?>

                                <label for="first_name">First name </label>
                                <input id="first_name" class="span3" placeholder="First name" type="text" name="first_name" maxlength="50" value="<?php echo set_value('first_name'); ?>"  />
                                <?php echo form_error('first_name'); ?>

                                <label for="last_name">Last name</label>
                                <input id="last_name" class="span3" placeholder="Last name" type="text" name="last_name" maxlength="50" value="<?php echo set_value('last_name'); ?>"  />
                                <?php echo form_error('last_name'); ?>

                                <p><button type="submit" class="btn btn-primary">Done</button></p>
                                
                                <p>By clicking 'Done' you agreed to the Terms of Use</p>
                                <?php echo form_close(); ?>
                        </div>
                </div>
                <div class="footer-container">
                        <p>http://www.facebook.com/FashionIslandmall</p>
                </div>
        </div>
    </body>
</html>

