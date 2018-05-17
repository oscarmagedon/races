<script type="text/javascript">
var load_img = '<?php echo $html->image("loading_small.gif",array("alt"=>"Esperando..."))?>';
var load_races = '<?php echo $html->url(array("controller"=>"races","action"=>"list_ajax"))?>';
var dat = '<?php echo $date?>';
var filt_url = '<?php echo $html->url(array("action"=>"follow","/")) ?>'

$(function(){
	$("#chngdat").click(function(){
		$("#date").show();
		$(this).hide();
		$("#date_part").hide();
		return false;
		
	});

	$("#date").attr('readonly',true).datepicker().hide().change(function(){
		var date = $(this).val();					
		location = filt_url + "/" + date;	
	});

	$(".hipodrome").change(function(){
		var hip = $(this).attr('value');
		$("#race_id").html(load_img);
		$("#race_id").load(load_races + "/" + hip + "/" + dat);
	});
	
});
</script>
<style>
h2,h3{
	padding-top:5px;
}
</style>
<div class="tickets">
	<h2>
		Seguimiento del dia 
		<span id='date_part'>
			<?php 
			echo $dtime->date_spa_mon_abr($date);
			if($hip_name != "") echo " de $hip_name" 
			?>
		</span>
		<span style='font-size:60%'>
			<a href="#" id="chngdat">Cambiar fecha</a>
		</span>
		<?php 
		echo $form->input('date',
			array('value'=>$date,'label'=>false,'style'=>'width:120px','div'=>false))
		?>
		
	</h2>
	<table style="width: 80%">
		<tr>
			<th>Totales: </th>
			<td>
				<?php echo number_format($values['tickets'],0) ?> Tickets
			</td>
			<td>
				<?php echo number_format($values['unidades'],0) ?> Unidades
				(Bs. <?php echo number_format($values['unidades_bs'],0) ?>)
			</td>
			<td>
				<?php echo number_format($values['premios'],2) ?> En Premios
				(Bs. <?php echo number_format($values['premios_bs'],2) ?>)
			</td>
			<td>
				<?php echo number_format($values['utilidad'],2) ?> En Utilidad
				(Bs. <?php echo number_format($values['utilidad_bs'],2) ?>)
			</td>
		</tr>
	</table>
	<h3>Detalles</h3>
	<?php 
	//pr($for_details); pr($alerts);
	if($hipodrome_id != "")
		echo $html->link("<- Volver a Hipodromos",array('action'=>'follow',$date));
	
	?>
	<table border="1">
		<tr>
			<th><?php echo $to_show ?></th>
			<?php 
			foreach($profiles as $p){
				echo "<th>$p</th>";
			}
			?>
		</tr>
		<?php	
		$i = 0;
		foreach($for_details as $title => $dets){
		?>
		<tr>
			<td style="vertical-align:middle">
			<?php
			if($hipodrome_id == "")
				echo $html->link($title,array('action'=>'follow',$date,$dets['hip_id']));
			else{
				echo "<b>$title</b><br />";
				echo $html->link("Ver Alertas",array('controller'=>'alerts','action'=>'index',$dets['race_id']));			
			}
			?>
			</td>
			<?php 
			foreach($profiles as $pk => $pv){
				$tickets = 0;
				$ventas = 0;
				$ventas_bs = 0; 
				$premios = 0;
				$premios_bs = 0;
				$utilidad = 0;
				$utilidad_bs = 0;
	
				if(!empty($dets[$pk])){
					$tickets = $dets[$pk]['co'];
					$ventas = $dets[$pk]['un'];
					$ventas_bs = $ventas * $money;
					$premios = $dets[$pk]['pr'];
					$premios_bs = $premios * $money;
					$utilidad = $ventas - $premios;
					$utilidad_bs = $utilidad * $money;
				}
					
			?>
				<td>
					<table cellspacing="0" cellpadding="0" style="width:100%; font-size:70%; margin-bottom: 0px">
						<tr>
							<th><?php echo number_format($tickets,0)." Tickets" ?></th>
							<th>Unds</th><th>Bs.</th>
						</tr>
						<tr>
							<td>Ventas</td>
							<td><?php echo number_format($ventas,2) ?></td>
							<td><?php echo number_format($ventas_bs,2) ?></td>
						</tr>
						<tr>
							<td>Premios</td>
							<td><?php echo number_format($premios,2) ?></td>
							<td><?php echo number_format($premios_bs,2) ?></td>
						</tr>
						<tr>
							<td>Utilidad</td>
							<td><?php echo number_format($utilidad,2) ?></td>
							<td><?php echo number_format($utilidad_bs,2) ?></td>
						</tr>
					</table>
				</td>
			<?php 
			}
			?>
		</tr>
		<?php 
			$i ++;
		}
		?>
	</table>
</div>