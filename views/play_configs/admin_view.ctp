<div class="playConfigs view">
<h2><?php  __('PlayConfig');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $playConfig['PlayConfig']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Play Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($playConfig['PlayType']['name'], array('controller'=> 'play_types', 'action'=>'view', $playConfig['PlayType']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Profile'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($playConfig['Profile']['name'], array('controller'=> 'profiles', 'action'=>'view', $playConfig['Profile']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Minumum Units'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $playConfig['PlayConfig']['minumum_units']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Maximum Units'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $playConfig['PlayConfig']['maximum_units']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit PlayConfig', true), array('action'=>'edit', $playConfig['PlayConfig']['id'])); ?> </li>
		<li><?php echo $html->link(__('Delete PlayConfig', true), array('action'=>'delete', $playConfig['PlayConfig']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $playConfig['PlayConfig']['id'])); ?> </li>
		<li><?php echo $html->link(__('List PlayConfigs', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New PlayConfig', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Play Types', true), array('controller'=> 'play_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Play Type', true), array('controller'=> 'play_types', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Profiles', true), array('controller'=> 'profiles', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Profile', true), array('controller'=> 'profiles', 'action'=>'add')); ?> </li>
	</ul>
</div>
