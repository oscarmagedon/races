<script type="text/javascript">

$(function(){
	$("#since").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
	$("#until").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
	
	$("#filt").click(function(){
		var filt_url = '<?php echo $html->url(array("action"=>"sales","/")) ?>'
		var since = $("#since").val();
		var until = $("#until").val();
		var profile_id = $("#profile_id").val();
				
		location = filt_url + "/" + since + "/" + until;
	});	
});
</script>
<style>
.filter-table{
	width: 500px;
}

.totals-table{
	width: auto;
}
.totals-table th{
	font-weight: normal;
	text-align: left;
	padding: 3px 12px 1px 4px;
}
.totals-table td{
	font-weight: bold;
	font-size: 120%;
	text-align: right;
	padding: 3px 10px 1px 20px;
}
.terminals-table{
	width: auto;
}
.terminals-table th{
	padding: 4px 8px;
}
.terminals-table td{
	text-align: right;
	padding: 4px 8px;
}
.terminals-table .totals-term{
	text-align: left;
	padding-left: 10px;
}
</style>
<div class="tickets">
	<h2>Ventas (<?php echo $html->link('Nueva Pant.',array('action' => 'salesnew')) ?>)</h2>
	<table class="filter-table" cellpadding="0" cellspacing="0">
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
	<table cellpadding="0" cellspacing="0" class="totals-table">
		<tr>
			<th colspan="2">Tickets:</th>
			<td colspan="2"><?php echo $values['tickets'] ?></td>
		</tr>
		<tr>
			<th>Unidades:</th>
			<td><?php echo number_format($values['unidades'],0,'.',',') ?></td>
			<th>En Bs:</th>
			<td><?php echo number_format($values['unidades_bs'],0,'.',',') ?></td>
		</tr>
		<tr>
			<th>Premios:</th>
			<td><?php echo number_format($values['premios'],0,'.',',') ?></td>
			<th>En Bs:</th>
			<td><?php echo number_format($values['premios_bs'],0,'.',',') ?></td>
		</tr>
		<tr>
			<th>Utilidad:</th>
			<td><?php echo number_format($values['utilidad'],0,'.',',') ?></td>
			<th>En Bs:</th>
			<td><?php echo number_format($values['utilidad_bs'],0,'.',',') ?></td>
		</tr>
	</table>
	<h3>Por usuarios:</h3>
	<table class="terminals-table" cellpadding="0" cellspacing="0" border="1">
		<tr>
			<th>Taquilla</th>
			<th>Tickets</th>
			<th colspan="2">Ventas</th>
			<th colspan="2">Premios</th>
			<th>Pct %</th>
			<th colspan="2">Montos Pct</th>
			<th colspan="2">Utilidad</th>
		</tr>
		<?php 		
		$i = 0;
		foreach($profiles as $pk => $pv){
			$class = null;
			if ($i++ % 2 != 0) {
				$class = ' class="altrow"';
			}
			
			$tickets = 0;
			$ventas = 0;
			$ventas_bs = 0; 
			$premios = 0;
			$premios_bs = 0;
			$pct = 0;
			$premio_pct = 0;
			$premio_pct_bs = 0;
			$utilidad = 0;
			$utilidad_bs = 0;

			if(!empty($profiles_tickets[$pk])){
				$tickets = $profiles_tickets[$pk]['co'];
				$ventas = $profiles_tickets[$pk]['un'];
				$ventas_bs = $ventas * $money;
			} 

			if(!empty($profiles_payed[$pk])){
				$premios = $profiles_payed[$pk]['pr'];
				$premios_bs = $premios * $money;
				if(!empty($pcts[$pk]))
					$pct = $pcts[$pk];
				$premio_pct = $profiles_payed[$pk]['pr'] * $pct/100;
				$premio_pct_bs = $premio_pct * $money;
			}
			
			$utilidad = $ventas - $premios + $premio_pct;
			$utilidad_bs = $utilidad * $money;
			
		?>
			<tr<?php echo $class;?>>
				<td class="totals-term"><?php echo $pv ?></td>
				<td><?php echo number_format($tickets,0) ?></td>
				<td><?php echo number_format($ventas,0) ?></td>
				<td><?php echo "Bs. ".number_format($ventas_bs,0) ?></td>
				<td><?php echo number_format($premios,0) ?></td>
				<td><?php echo "Bs. ".number_format($premios_bs,0) ?></td>
				<td><?php echo number_format($pct,1) ?></td>
				<td><?php echo number_format($premio_pct,2) ?></td>
				<td><?php echo "Bs. ".number_format($premio_pct_bs,2) ?></td>
				<td><?php echo number_format($utilidad,2) ?></td>
				<td><?php echo "Bs. ".number_format($utilidad_bs,2) ?>
				</td>
			</tr>
		<?php 	
		}
		?>
	</table>
</div>