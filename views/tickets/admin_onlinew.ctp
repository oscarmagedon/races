<script type="text/javascript">
var load_img = 'Cargando... <?php echo $html->image("loading_small.gif",array("alt"=>"Esperando..."))?>';

$(function(){
	$("#since").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
	$("#until").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});

	$("#filt").click(function(){
		var filt_url = '<?php echo $html->url(array("action"=>"onlinew")) ?>'
		var since = $("#since").val();
		var until = $("#until").val();
				
		location = filt_url + "/" + since + "/" + until;
	});	
	
});
</script>
<div class='tickets'>
	<h2>Ganadores ONLINE pendientes</h2>
	<table style="width:80%">
	<tr>
		<th>Filtrar Por:</th>
		<td>
		<?php echo $form->input('since',array('value'=>$since,'label'=>"Desde",'style'=>'width:90px')) ?>
		</td>
		<td>
		<?php echo $form->input('until',array('value'=>$until,'label'=>"Hasta",'style'=>'width:90px')) ?>
		</td>
		<td>
		<?php echo $form->button('Filtrar',array('id'=>'filt')) ?>
		</td>
	</tr>
	</table>
	<?php
	//pr($nopayed);
	?>
	<table style="width: 800px">
		<tr>
			<th>Jugador</th><th>Tickets</th><th>Apostado</th>
			<th>PREMIO</th><th>Explorar</th><th>PAGAR</th>
		</tr>
		<?php
		foreach ($nopayed as $np) {
			if($np[0]['tks'] != 0){
			?>
				<tr>
					<td style="text-align: left"><?php echo $np['Profile']['name'] ?></td>
					<td style="text-align: right"><?php echo $np[0]['tks'] ?></td>
					<td style="text-align: right"><?php echo $np[0]['amo'] ?></td>
					<td style="text-align: right"><?php echo $np[0]['pri'] ?></td>
					<td><?php echo $html->link("Explorar",array('action'=>'index',$since,$until,$np['Profile']['id'],1,2),array('target'=>'_blank')) ?></td>
					<td><?php echo $html->link("PAGAR TODOS",array('action'=>'payonline',$np['Profile']['id'],$since,$until)) ?></td>
				</tr>
			<?php
			}
		}
		?>
			
	</table>
	
</div>
