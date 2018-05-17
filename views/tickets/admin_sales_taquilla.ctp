<script type="text/javascript">
$(function(){
	$("#since").attr('readonly',true).datepicker({dateFormat: "yy-mm-dd"});
	$("#until").attr('readonly',true).datepicker({dateFormat: "yy-mm-dd"});

	$("#filt").click(function(){
		var filt_url = '<?php echo $html->url(array("action"=>"sales_taquilla","/")) ?>'
		var since = $("#since").val();
		var until = $("#until").val();
		var profile_id = $("#profile_id").val();
				
		location = filt_url + "/" + since + "/" + until;
	});	
});
</script>
<div class="tickets">
	<h2>Ventas</h2>
	<table style="width:80%">
		<tr>
			<th>Filtrar Por:</th>
			<td><?php 
				echo $form->input('since',array('value'=>$since,'label'=>"Desde",'class'=>'filter_input','style'=>'width:120px'))
			?></td>
			<td><?php 
				echo $form->input('until',array('value'=>$until,'label'=>"Hasta",'class'=>'filter_input','style'=>'width:120px'))
			?></td>
			<td><?php echo $form->button('Filtrar',array('id'=>'filt')) ?></td>
		</tr>
	</table>
	<?php 
	//pr($values)
	?>
	<table style="width: 600px">
		<tr>
			<th colspan="2">Tickets:</th>
			<td colspan="2"><?php echo $values['tickets'] ?></td>
		</tr>
		<tr>
			<th>Unidades:</th>
			<td><?php echo number_format($values['unidades'],2) ?></td>
			<th>En Bs:</th>
			<td><?php echo number_format($values['unidades_bs'],2) ?></td>
		</tr>
		<tr>
			<th>Premios:</th>
			<td><?php echo number_format($values['premios'],2) ?></td>
			<th>En Bs:</th>
			<td><?php echo number_format($values['premios_bs'],2) ?></td>
		</tr>
		<tr>
			<th>Pct (<?php echo $values['pct'] ?>):</th>
			<td><?php echo number_format($values['premios_pct'],2) ?></td>
			<th>En Bs:</th>
			<td><?php echo number_format($values['premios_pct_bs'],2) ?></td>
		</tr>
		<tr>
			<th>Utilidad:</th>
			<td><?php echo number_format($values['utilidad'],2) ?></td>
			<th>En Bs:</th>
			<td><?php echo number_format($values['utilidad_bs'],2) ?></td>
		</tr>
	</table>
</div>