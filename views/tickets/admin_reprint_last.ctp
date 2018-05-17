<script type="text/javascript">
var load_img = 'Cargando... <?php echo $html->image("loading_small.gif",array("alt"=>"Esperando..."))?>';
var url_details = '<?php echo $html->url(array("controller"=>"tickets","action"=>"horses_details"))?>';

$(function(){
	$(".detail").click(function(){
		var tik_id = $(this).attr('id');
		var tdelem = $(this).parents("td");	
		tdelem.html(load_img);
		tdelem.load(url_details + "/" + tik_id);
		return false;
	});
});
</script>
<div class="tickets">
<h2>Reimprimir Ultimo Ticket</h2>	
<h4>
    El numero de confirmacion del ticket sera cambiado una vez impreso,
    invalidando el ticket anterior.
</h4>
	<div style="clear:both"> </div>	
	<?php
	if($errLine != ""){
	?>
		<span style="font-size:150%;font-weight:bold; color:red"><?php echo $errLine?></span>
	<?php
	}else{
	?>
		<div style="clear:none; float:left; width:400px; margin-top:10px; margin-left:15px">
		<?php
		if(!empty($ticket)){
		?>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th>Numero</th>
				<td>
					<?php echo $ticket['Ticket']['number'] ?>
				</td>
			</tr>
            <tr>
				<th>Confirm.</th>
				<td>
                    <b><?php echo $ticket['Ticket']['confirm'] ?></b>
				</td>
			</tr>
            <tr>
				<th>Copias Impresas</th>
				<td>
                    <b><?php echo $ticket['Ticket']['copies'] ?></b>
				</td>
			</tr>
			<tr>
				<th>Fecha</th>
				<td>
					<?php echo $dtime->date_from_created($ticket['Ticket']['created']) ?>
				</td>
			</tr>
			<tr>
				<th>Hora</th>
				<td>
					<?php echo $dtime->hour_from_created($ticket['Ticket']['created']) ?>
				</td>
			</tr>
			<tr>
				<th>Taquilla</th>
				<td>
					<?php echo $ticket['Profile']['name'] ?>
				</td>
			</tr>
			<tr>
				<th>Unidades</th>
				<td>
					<?php echo $ticket['Ticket']['units'] ?>
				</td>
			</tr>
			<tr>
				<th>Premio</th>
				<td>
					<?php echo $ticket['Ticket']['prize'] ?>
				</td>
			</tr>
			<tr>
				<th>Estado</th>
				<td>
					<?php echo $ticket['PayedStatus']['name'] ?>
				</td>
			</tr>
			<tr>
				<th>Detalles</th>
				<td>
					<?php echo $html->link("Detalles", array('action'=>'#'),array('class'=>'detail','id'=>$ticket['Ticket']['id'])) ?>
				</td>
			</tr>
			<tr>
				<th>Reimprimir</th>
				<td style="font-size: 150%">
					<?php
					echo $html->link("Ticket",array("action"=>"prntkt",$ticket['Ticket']['id'],1))
					?>
				</td>
			</tr>
		</table>
		<?php
		}
		?>
		</div>
	<?php
	}
	?>
	
	<div style="clear:both"> - </div>
</div>