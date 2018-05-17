<div class="playConfigs index">
<h2><?php __('PlayConfigs');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('play_type_id');?></th>
	<th><?php echo $paginator->sort('profile_id');?></th>
	<th><?php echo $paginator->sort('minumum_units');?></th>
	<th><?php echo $paginator->sort('maximum_units');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($playConfigs as $playConfig):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $playConfig['PlayConfig']['id']; ?>
		</td>
		<td>
			<?php echo $html->link($playConfig['PlayType']['name'], array('controller'=> 'play_types', 'action'=>'view', $playConfig['PlayType']['id'])); ?>
		</td>
		<td>
			<?php echo $html->link($playConfig['Profile']['name'], array('controller'=> 'profiles', 'action'=>'view', $playConfig['Profile']['id'])); ?>
		</td>
		<td>
			<?php echo $playConfig['PlayConfig']['minumum_units']; ?>
		</td>
		<td>
			<?php echo $playConfig['PlayConfig']['maximum_units']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $playConfig['PlayConfig']['id'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $playConfig['PlayConfig']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $playConfig['PlayConfig']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $playConfig['PlayConfig']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New PlayConfig', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Play Types', true), array('controller'=> 'play_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Play Type', true), array('controller'=> 'play_types', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Profiles', true), array('controller'=> 'profiles', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Profile', true), array('controller'=> 'profiles', 'action'=>'add')); ?> </li>
	</ul>
</div>