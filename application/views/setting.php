<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
<script>
        $(function(){
                $('#start,#end').datepicker({"dateFormat": "yy-mm-dd 00:00:00"});
        });
</script>
<link rel="stylesheet" type="text/css"  href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.15/themes/smoothness/jquery-ui.css" />
<h1>Report</h1>
<?php // Change the css classes to suit your needs    
echo '<p>'.anchor('setting/report_list/'.$app_install_id,'Report list (link)').'</p>';
echo '<p>'.anchor('setting/report_csv/'.$app_install_id,'Report list (CSV)').'</p>';
$attributes = array('class' => '', 'id' => '');
echo form_open('setting/'.$app_install_id, $attributes); ?>
<h1>Setting</h1>
<p><a href="<?php echo $facebook_add_page_app_url;?>">Add app into facebook page</a></p>

<p>
        <label for="photo_message">Photo Message</label>
	<?php echo form_error('photo_message'); ?>
	<br />
							
	<?php echo form_textarea( array( 'name' => 'photo_message', 'rows' => '5', 'cols' => '80', 'value' => set_value('photo_message', isset($setting['photo_message']) ? ($setting['photo_message']) : '') ) )?>
</p>
<p>
        <label for="tag_1">Tag 1 <span class="required">*</span></label>
        <?php echo form_error('tag_1_x'); ?> <?php echo form_error('tag_1_y'); ?>
        <br /><input id="tag_1_x" type="text" name="tag_1_x" maxlength="4" value="<?php echo set_value('tag_1_x', isset($setting['tag_1_x']) ? ($setting['tag_1_x']) : ''); ?>"  />
        <input id="tag_1_y" type="text" name="tag_1_y" maxlength="4" value="<?php echo set_value('tag_1_y', isset($setting['tag_1_y']) ? ($setting['tag_1_y']) : ''); ?>"  />
</p>

<p>
        <label for="tag_2">Tag 2 <span class="required">*</span></label>
        <?php echo form_error('tag_2_x'); ?> <?php echo form_error('tag_2_y'); ?>
        <br /><input id="tag_2_x" type="text" name="tag_2_x" maxlength="4" value="<?php echo set_value('tag_2_x', isset($setting['tag_2_x']) ? ($setting['tag_2_x']) : ''); ?>"  />
        <input id="tag_2_y" type="text" name="tag_2_y" maxlength="4" value="<?php echo set_value('tag_2_y', isset($setting['tag_2_y']) ? ($setting['tag_2_y']) : ''); ?>"  />
</p>

<p>
        <label for="tag_3">Tag 3 <span class="required">*</span></label>
        <?php echo form_error('tag_3_x'); ?> <?php echo form_error('tag_3_y'); ?>
        <br /><input id="tag_3_x" type="text" name="tag_3_x" maxlength="4" value="<?php echo set_value('tag_3_x', isset($setting['tag_3_x']) ? ($setting['tag_3_x']) : ''); ?>"  />
        <input id="tag_3_y" type="text" name="tag_3_y" maxlength="4" value="<?php echo set_value('tag_3_y', isset($setting['tag_3_y']) ? ($setting['tag_3_y']) : ''); ?>"  />
</p>

<p>
        <label for="tag_4">Tag 4 <span class="required">*</span></label>
        <?php echo form_error('tag_4_x'); ?> <?php echo form_error('tag_4_y'); ?>
        <br /><input id="tag_4_x" type="text" name="tag_4_x" maxlength="4" value="<?php echo set_value('tag_4_x', isset($setting['tag_4_x']) ? ($setting['tag_4_x']) : ''); ?>"  />
        <input id="tag_4_y" type="text" name="tag_4_y" maxlength="4" value="<?php echo set_value('tag_4_y', isset($setting['tag_4_y']) ? ($setting['tag_4_y']) : ''); ?>"  />
</p>

<p>
        <label for="tag_5">Tag 5 <span class="required">*</span></label>
        <?php echo form_error('tag_5_x'); ?> <?php echo form_error('tag_5_y'); ?>
        <br /><input id="tag_5_x" type="text" name="tag_5_x" maxlength="4" value="<?php echo set_value('tag_5_x', isset($setting['tag_5_x']) ? ($setting['tag_5_x']) : ''); ?>"  />
        <input id="tag_5_y" type="text" name="tag_5_y" maxlength="4" value="<?php echo set_value('tag_5_y', isset($setting['tag_5_y']) ? ($setting['tag_5_y']) : ''); ?>"  />
</p>

<p>
        <label for="template_name">Template name</label>
        <?php echo form_error('template_name'); ?>
        <br /><input id="template_name" type="text" name="template_name"
         value="<?php echo set_value('template_name', isset($setting['template_name']) ? ($setting['template_name']) : ''); ?>"  />
</p>

<p>
        <label for="template_background">Template : Background image</label>
        <?php echo form_error('template_background'); ?>
        <br /><input id="template_background" type="text" name="template_background"
         value="<?php echo set_value('template_background', isset($setting['template_images']['background']) ? ($setting['template_images']['background']) : ''); ?>"  />
        <?php if(isset($setting['template_images']['background']) && isset($setting['template_name'])) : ?>
                <img src="<?php echo base_url(
                        'assets/images/'.$setting['template_name'].
                        '/'.$setting['template_images']['background']
                );?>" />
        <?php endif; ?>
</p>

<p>
        <label for="template_main">Template : Main page image</label>
        <?php echo form_error('template_main'); ?>
        <br /><input id="template_main" type="text" name="template_main"
         value="<?php echo set_value('template_main', isset($setting['template_images']['main']) ? ($setting['template_images']['main']) : ''); ?>"  />
        <?php if(isset($setting['template_images']['main']) && isset($setting['template_name'])) : ?>
                <img src="<?php echo base_url(
                        'assets/images/'.$setting['template_name'].
                        '/'.$setting['template_images']['main']
                );?>" />
        <?php endif; ?>
</p>

<p>
        <label for="template_register">Template : Register page image</label>
        <?php echo form_error('template_register'); ?>
        <br /><input id="template_register" type="text" name="template_register"
         value="<?php echo set_value('template_register', isset($setting['template_images']['register']) ? ($setting['template_images']['register']) : ''); ?>"  />
        <?php if(isset($setting['template_images']['register']) && isset($setting['template_name'])) : ?>
                <img src="<?php echo base_url(
                        'assets/images/'.$setting['template_name'].
                        '/'.$setting['template_images']['register']
                );?>" />
        <?php endif; ?>
</p>

<p>
        <label for="template_success_popup">Template : Success popup image</label>
        <?php echo form_error('template_success_popup'); ?>
        <br /><input id="template_success_popup" type="text" name="template_success_popup"
         value="<?php echo set_value('template_success_popup', isset($setting['template_images']['success_popup']) ? ($setting['template_images']['success_popup']) : ''); ?>"  />
        <?php if(isset($setting['template_images']['success_popup']) && isset($setting['template_name'])) : ?>
                <img src="<?php echo base_url(
                        'assets/images/'.$setting['template_name'].
                        '/'.$setting['template_images']['success_popup']
                );?>" />
        <?php endif; ?>
</p>

<p>
        <label for="facebook_page_id">Facebook page id*</label>
        <?php echo form_error('facebook_page_id'); ?>
        <br /><input id="facebook_page_id" type="text" name="facebook_page_id"  value="<?php echo set_value('facebook_page_id', isset($facebook_page_id) ? ($facebook_page_id) : ''); ?>"  />
</p>

<p>
        <label for="thumbnail_size">Thumbnail size (original is 50)*</label>
        <?php echo form_error('thumbnail_size'); ?>
        <br /><input id="thumbnail_size" type="text" name="thumbnail_size"  value="<?php echo set_value('thumbnail_size', isset($setting['thumbnail_size']) ? ($setting['thumbnail_size']) : ''); ?>"  />
</p>

<p>
        <label for="start">Start time*</label>
        <?php echo form_error('start'); ?>
        <br /><input id="start" type="text" name="start"  value="<?php echo set_value('start', isset($setting['start']) ? ($setting['start']) : ''); ?>"  />
</p>

<p>
        <label for="end">End time*</label>
        <?php echo form_error('end'); ?>
        <br /><input id="end" type="text" name="end"  value="<?php echo set_value('end', isset($setting['end']) ? ($setting['end']) : ''); ?>"  />
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
