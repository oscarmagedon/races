<script>
$(function() {
	$("#panel_look").dialog({
		autoOpen: false,
		bgiframe: true,		
		modal: true,
		height: 350,
		width: 600,
		resizable: true
	});
		
	$('.open_panel').click(function() {
		var myurl = $(this).attr("href");
		var tit = $(this).attr("title");
		var totit = tit;
		
		$('#panel_look').html('<?php echo $html->image("loading.gif")?>');
		$('#panel_look').dialog({title:totit});
		$('#panel_look').load(myurl);
		$('#panel_look').dialog('open');
		return false;
	});
	
	$(".ui_button_general").click(function(){
		window.location = '<?php echo $html->url(array("action"=>"set_enable")) ?>' + "/" + $(this).attr('id') + "/" + $(this).attr('title');
	});
	
});	
</script>
<div class="centers index">
<h2>Centros</h2>
<div class="actions" style="float:right; font-size:130%">
	<ul>
		<li><?php echo $html->link("Crear Nuevo Centro", array('action'=>'add'),array('class'=>'open_panel','title'=>'Crear Nuevo Centro')); ?></li>
	</ul>
</div>
<p><?php
echo $paginator->counter(array(
'format' => "Pagina %page% de %pages%, mostrando %current% registros de %count% totales, empezando en %start%, terminando en %end%"));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('name');?></th>
	<th><?php echo $paginator->sort('city');?></th>
	<th><?php echo $paginator->sort('address');?></th>
	<th><?php echo $paginator->sort('phone_number');?></th>
	<th><?php echo $paginator->sort('email');?></th>
	<th><?php echo $paginator->sort('owner');?></th>
	<th><?php echo $paginator->sort('commercial_name');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($centers as $center):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $center['Center']['name']; ?>
		</td>
		<td>
			<?php echo $center['Center']['city']; ?>
		</td>
		<td>
			<?php echo $center['Center']['address']; ?>
		</td>
		<td>
			<?php echo $center['Center']['phone_number']; ?>
		</td>
		<td>
			<?php echo $center['Center']['email']; ?>
		</td>
		<td>
			<?php echo $center['Center']['owner']; ?>
		</td>
		<td>
			<?php echo $center['Center']['commercial_name']; ?>
		</td>
		<td class="actions">
			<?php 
			echo $html->link("Detalles", array('action'=>'view', $center['Center']['id'])); 
			echo $html->link("Editar", array('action'=>'edit', $center['Center']['id']),array('class'=>'open_panel','title'=>'Editar Centro')); 
			echo $html->link("Agregarle Usuario", array('controller'=>'profiles','action'=>'add', $center['Center']['id']),array('class'=>'open_panel','title'=>'Nuevo Usuario para: '.$center['Center']['name']));
			?>
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