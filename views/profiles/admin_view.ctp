<div class="profiles view">
<h2><?php  __('Profile');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $profile['Profile']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $profile['Profile']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Center'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($profile['Center']['name'], array('controller'=> 'centers', 'action'=>'view', $profile['Center']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Phone Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $profile['Profile']['phone_number']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('User Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $profile['Profile']['user_id']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Profile', true), array('action'=>'edit', $profile['Profile']['id'])); ?> </li>
		<li><?php echo $html->link(__('Delete Profile', true), array('action'=>'delete', $profile['Profile']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $profile['Profile']['id'])); ?> </li>
		<li><?php echo $html->link(__('List Profiles', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Profile', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Centers', true), array('controller'=> 'centers', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Center', true), array('controller'=> 'centers', 'action'=>'add')); ?> </li>
	</ul>
</div>
