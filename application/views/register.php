<?php  

$attributes = array('class' => '', 'id' => '');
echo form_open('register/'.$app_install_id, $attributes); ?>
<img src="https://graph.facebook.com/<?php echo $facebook_uid;?>/picture" />
<p>
        <label for="first_name">First name <span class="required">*</span></label>
        <?php echo form_error('first_name'); ?>
        <br /><input id="first_name" type="text" name="first_name" maxlength="50" value="<?php echo set_value('first_name'); ?>"  />
</p>

<p>
        <label for="last_name">Last name <span class="required">*</span></label>
        <?php echo form_error('last_name'); ?>
        <br /><input id="last_name" type="text" name="last_name" maxlength="50" value="<?php echo set_value('last_name'); ?>"  />
</p>

<p>
        <label for="email">Email <span class="required">*</span></label>
        <?php echo form_error('email'); ?>
        <br /><input id="email" type="text" name="email" maxlength="255" value="<?php echo set_value('email'); ?>"  />
</p>

<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
