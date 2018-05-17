<div class="playConfigs form">
<?php echo $form->create('PlayConfig');?>
	<fieldset>
 		<legend><?php __('Edit PlayConfig');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('play_type_id');
		echo $form->input('profile_id');
		echo $form->input('minumum_units');
		echo $form->input('maximum_units');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('PlayConfig.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('PlayConfig.id'))); ?></li>
		<li><?php echo $html->link(__('List PlayConfigs', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Play Types', true), array('controller'=> 'play_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Play Type', true), array('controller'=> 'play_types', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Profiles', true), array('controller'=> 'profiles', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Profile', true), array('controller'=> 'profiles', 'action'=>'add')); ?> </li>
	</ul>
</div>
