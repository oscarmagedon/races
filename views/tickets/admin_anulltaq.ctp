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
<h2>Anular Ticket de Taquilla</h2>	
	<div style="clear:both"> </div>
	<div style="clear:none; float:left; width:400px; margin-top:10px; margin-left:15px">
		<?php
        if ( empty ( $ticket ) ) {
            
            echo $form->create('Ticket',array('action'=>'anulltaq'));

            echo $form->input('number',array('style'=>'width: 150px; font-size:120%','label'=>'Numero'));

            echo $form->end("Buscar");
        
        } else {
		?>
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
        </table>
	</div>
	<div style="clear:none; float:left; margin-top:10px; margin-left:15px; width:400px">
        <?php
        if($errLine != ""){
        ?>
            <span style="font-size:150%;font-weight:bold; color:red"><?php echo $errLine?></span>
        <?php
        }else{
            echo $form->create('Ticket',array('action'=>'anulltaq'));

            echo $form->input('id',array('type'=>'hidden','value'=>$ticket['Ticket']['id']));


            if ($isOnline == false) {
                echo $form->input('confirm',array('style'=>'width: 150px; font-size:120%','label'=>'Confirm.'));
            }

            echo $form->end("Anular");
        }
    }
    ?>
	</div>
	<div style="clear:both"> - </div>
</div>