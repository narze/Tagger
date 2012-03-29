<table>
	<?php $i = 1; ?>
	<?php foreach($users as $user) :?>
		<tr>
			<td>
				<?php var_export($user); ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>