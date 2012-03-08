<?php // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('setting/'.$app_install_id, $attributes); ?>

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
        <label for="background_image_url">Background Image Url</label>
        <?php echo form_error('background_image_url'); ?>
        <br /><input id="background_image_url" type="text" name="background_image_url"  value="<?php echo set_value('background_image_url', isset($setting['background_image_url']) ? ($setting['background_image_url']) : ''); ?>"  />
        <?php if(isset($setting['background_image_url'])) : ?>
                <img src="<?php echo $setting['background_image_url'];?>" />
        <?php endif; ?>
</p>

<p>
        <label for="facebook_page_id">Facebook page id*</label>
        <?php echo form_error('facebook_page_id'); ?>
        <br /><input id="facebook_page_id" type="text" name="facebook_page_id"  value="<?php echo set_value('facebook_page_id', isset($facebook_page_id) ? ($facebook_page_id) : ''); ?>"  />
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
