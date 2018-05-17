<script>
$(function(){
	$("#date").datepicker({dateFormat: "yy-mm-dd"}).attr('readonly',true);
	$("#filt").button({ icons: {primary: "ui-icon-zoomin"}}).css('font-size','140%').click(function(){
		var da = $("#date").val();
		var ho = $("#houre").val();
		var us = $("#user").val();
		var ty = $("#type").val();
		location = '<?php echo $html->url(array("action"=>"center")) ?>'+'/'+da+'/'+ho+'/'+us+'/'+ty;
	});
});	
</script>
<div class="operations index">
<h2>Auditoria de movimientos</h2>
<table style="width:800px;">
	<tr>
		<td><?php 
		echo $form->input("date",array('label'=>'Fecha','value'=>$date,'readonly'=>'readonly','style'=>'width:90px'));
		?></td>
		<td><?php 
		echo $form->input("houre",array('label'=>'Horas','value'=>$hour,'options'=>$hours,'empty'=>array(0=>"Todo el Dia")));
		?></td>
		<td><?php 
		echo $form->input("user",array('label'=>'Usuario','value'=>$profile_id,'options'=>$profiles,'empty'=>array(0=>"Todos"))) 	
		?></td>
		<td><?php 
		echo $form->input("type",array('label'=>'Tipo','value'=>$optype_id,'options'=>$op_types,'empty'=>array(0=>"Todos"))) 	
		?></td>
		<td><button id="filt">Filtrar</button></td>
	</tr>
</table>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th>Id</th>
	<th>Fecha</th>
	<th>Hora</th>
	<th>Usuario</th>
	<th>Tipo</th>
	<th>Tabla</th>
	<th>Ref</th>
	<th>Detalles</th>
</tr>
<?php
$i = 0;
foreach ($operations as $operation):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $operation['Operation']['id'] ?>
		</td>
		<td>
			<?php echo $dtime->date_from_created($operation['Operation']['created']) ?>
		</td>
		<td>
			<?php echo $dtime->hour_exact_created($operation['Operation']['created']) ?>
		</td>
		<td>
			<?php echo $operation['Profile']['name'] ?>
		</td>
		<td>
			<?php echo $operation['OperationType']['name'] ?>
		</td>
		<td>
			<?php echo $operation['Operation']['metainf']; ?>
		</td>
		<td>
			<?php echo $operation['Operation']['model_id'];	?>
		</td>
		<td>
			<?php echo $operation['Operation']['metadata']; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array('url'=>array('action'=>'center',$date,$hour,$profile_id,$optype_id)), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers(array('url'=>array('action'=>'center',$date,$hour,$profile_id,$optype_id)));?>
	<?php echo $paginator->next(__('next', true).' >>', array('url'=>array('action'=>'center',$date,$hour,$profile_id,$optype_id)), null, array('class' => 'disabled'));?>
</div>