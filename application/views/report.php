<table border=1>
	<tr>
	<?php $field_names = array_keys($user); 
	foreach($field_names as $field_name) : ?>
		<th><?php echo $field_name;?></th>
	<?php endforeach; ?>
	</tr>
	<tr>
	<?php foreach($user as $field) :?>
		
			<td>
				<?php echo is_array($field) ? print_r($field,TRUE) : $field ;?>
			</td>
	<?php endforeach; ?>
		</tr>
</table>
<p>
	<?php echo $csv; ?>
</p>
<div><a href="<?php echo base_url().'setting/report_list/'.$app_install_id;?>">Back</a></div>