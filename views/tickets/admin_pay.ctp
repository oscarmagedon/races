<script type="text/javascript">
var load_img = 'Cargando... <?php echo $html->image("loading_small.gif",array("alt"=>"Esperando..."))?>';
var url_details = '<?php echo $html->url(array("controller"=>"tickets","action"=>"horses_details"))?>';
var pay_conf_url = '<?php echo $html->url(array("controller"=>"tickets","action"=>"pay_ticket"))?>';

$(function(){
	$(".detail").click(function(){
		var tik_id = $(this).attr('id');
		var tdelem = $(this).parents("td");	
		tdelem.html(load_img);
		tdelem.load(url_details + "/" + tik_id);
		return false;
	});
	
	$("#toPay").click(function(){
		$("#formToPay").attr('action',pay_conf_url);
		$("#formToPay").submit();
	});
});
</script>
<div class="tickets">
	<h2>Pagar Ticket</h2>
	<?php 
	echo $form->create('Ticket',array('action'=>'pay'));

	echo $form->input('number',array('style'=>'width: 150px; font-size:120%','label'=>'Numero'));
	
	echo $form->end("Buscar");
	
	if(!empty($ticket)){
	?>
	<div style="clear:none; float:left; width:400px; margin-top:10px; margin-left:15px">
		<span style="font-size:120%;font-weight:bold;">Resultados: </span><br/>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th>Numero</th>
				<td>
					<?php echo $ticket['Ticket']['number'] ?>
				</td>
			</tr>
			<tr>
				<th>Fecha</th>
				<td>
					<?php echo $dtime->date_from_created($ticket['Ticket']['created']) ?>
				</td>
			</tr>
			<tr>mo
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
					<?php 
                    if ($ticket['Ticket']['enable'] == 1) {
                        echo $ticket['Ticket']['units'];
                    }
                    ?>
				</td>
			</tr>
			<tr>
				<th>Premio</th>
				<td>
					<?php 
                    if ($ticket['Ticket']['enable'] == 1) {
                        echo $ticket['Ticket']['prize'];
                    }
                    ?>
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
		</table>
	</div>
	<div style="clear:none; float:left; margin-top:10px; margin-left:15px; width:400px">
			<?php 
			//  P  A  G  A  B  L  E
			if ( $ticket['Ticket']['prize'] > 0 && $ticket['Ticket']['enable'] == 1){
			?>
			<span style="font-size:120%;font-weight:bold">Detalles de pago:</span><br/>
			<table>
				<tr>
					<td>Premio Base:</td>
					<td>
						<b>
						<?php 
						echo $ticket['Ticket']['prize'];
						?>
						</b>
					</td>
				</tr>
				<tr>
					<td>Pct centro (<?php echo number_format($pct,2) ?> %):</td>
					<td>
						<b>
						<?php 
						$pct_tot = ($ticket['Ticket']['prize'] * $pct/100);
						echo number_format($pct_tot,2);
						?>
						</b>
					</td>
				</tr>
				<tr>
					<td>Total a pagar: </td>
					<td>
						<b>
						<?php 
						$final = ($ticket['Ticket']['prize'] - $pct_tot);
						echo number_format($final,2);
						?>
						</b>
					</td>
				</tr>
				<tr>
					<td>Total en Bs. (Valor <?php echo $unit ?>): </td>
					<td>
						<b>
						<?php  
						$bs = $final * $unit; 
						echo number_format($bs,2);
						?>
						</b>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<?php 
					if($ticket['Ticket']['payed_status_id'] == 1){
					?>
						<form id="formToPay" method="post" style="font-size: 120%">
							<input type="hidden" name="data[Ticket][id]" value="<?php echo $ticket['Ticket']['id'] ?>" id="tikId" />
							Conf.: <input type="text" name="data[Ticket][confirm]" id="confirm" style="font-size: 130%;" />
							<input type="button" value=" PAGAR " id="toPay" style="font-size: 130%; font-weight:bold" />
						</form>
					<?php 
					}
					?>
					</td>
				</tr>
			</table>
			<?php 
			}else{
			//   - N  O -    P  A  G  A  B  L  E
			?>
				<span style="font-size:150%;font-weight:bold; color:red">Ticket NO pagable</span><br/>
			<?php 
			}
			?>
		</div>
	<?php 	
	}
	?>	
	<div style="clear:both"> - </div>
</div>