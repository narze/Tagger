<table>
	<?php $i = 1; ?>
	<?php foreach($users as $user) :?>
		<tr>
			<td>
				<a href="<?php echo base_url().'setting/report/'.$app_install_id.'?facebook_uid='.$user['facebook_uid'];?>">
					<?php echo $i++.' : '.$user['profile']['first_name']; ?>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
</table>