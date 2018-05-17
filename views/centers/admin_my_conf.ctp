<script>
$(function() {
	$("#panel_look").dialog({
		autoOpen: false,
		bgiframe: true,		
		modal: true,
		width: 500,
		height: 250,
		resizable: true,
		buttons: {
        	'Guardar': function() {
				$(this).find('form').submit();
            },
        	'Cerrar': function() {$(this).dialog('close');}
		}
	});
	
	$(".to_modal").click(function(){		
		$('#panel_look').html('<?php echo $html->image("loading.gif")?>');
		$('#panel_look').dialog({title:$(this).text()});
		$('#panel_look').load($(this).attr('href'));
		$('#panel_look').dialog('open');
	});
		
	$("#edit button").button({ icons: { primary: "ui-icon-pencil" }}).css('margin','2px');
	$("#new_conf").button({ icons: { primary: "ui-icon-plus" }}).css('margin','5px');
	$("#saveall").button({ icons: { primary: "ui-icon-disk" }}).css('margin','5px').click(function(){
		$(this).parents('form').submit();
	});
	
	$(".but_set").buttonset();
});	
</script>
<div>
	<h2>Configuraciones de <?php echo $center ?></h2>
	<h3>Configuraciones de Montos</h3>
	<table border="1" style="width:700px">
		<tr>
			<th>Configuracion</th>
			<th>Monto</th>
			<th>Taquilla</th>
			<th>Desde</th>
			<th>Hasta</th>
			<th>Actual</th>
			<th>Acciones</th>
		</tr>
		<?php 
		foreach($confs as $c){
		?>
			<tr>
				<td><?php echo $c['ConfigType']['name'] ?></td>
                <td style="text-align: right; padding-right: 10px">
                    <?php 
                    echo number_format($c['Config']['amount'],0,',','.') 
                    ?>
                </td>
				<td><?php echo $profiles[$c['Config']['profile_id']] ?></td>
				<td><?php echo $dtime->date_spa_mon_abr($c['Config']['from']) ?></td>
				<td><?php 
					if($c['Config']['until'] != "0000-00-00" && $c['Config']['actual'] == 0)
						echo $dtime->date_spa_mon_abr($c['Config']['until']);
					else
						echo " - ";
					?>
				</td>
				<td><?php if($c['Config']['actual'] == 1) echo "SI"; else echo "NO"; ?></td>
				<td id="edit">		
					<button class="to_modal" href="<?php echo $html->url(array('controller'=>'configs','action'=>'edit',$c['Config']['id'])) ?>">Editar</button>
				</td>
			</tr>
		<?php 
		}
		?>
		<tr>
			<td colspan="7">
				<button class="to_modal" href="<?php echo $html->url(array('controller'=>'configs','action'=>'add')) ?>" id="new_conf">Agregar Nueva Configuracion</button>
			</td>
		</tr>
	</table>
	<hr />
	<h3>Restricciones Especiales</h3>
	<?php
    //pr($specials);
	echo $form->create("Profile",array('action'=>'confs_adv'))
	?>
	<table border="1" style="width:auto">
		<tr>
			<th>Tipo</th>
            <th>Usuario</th>
            <th style="width: 200px;">Hipodromos</th>
			<th>Anula Ult. Ticket</th>
			<th>Reimprime Ult. Ticket</th>
            <th>Imp. Codigo Barra</th>
		</tr>
	<?php
	foreach ($specials as $s) {
		$pid = $s['Profile']['id'];
	?>
		<tr>
            <td>
                <?php
                echo ($s['User']['role_id'] == 4) ? 'ONLINE' : 'TAQUILLA';
                ?>
            </td>
            <td><b><?php echo $s['Profile']['name'] ?></b>
            </td>
			<td class="but_set">
				<?php
				$ch0 = ""; $ch1 = ""; $ch2 = "";
				switch ($s['Profile']['bet_tracks']) {
					case 0:
						$ch0 = "checked='checked'";
						break;
					case 1:
						$ch1 = "checked='checked'";
						break;
					case 2:
						$ch2 = "checked='checked'";
						break;
				}
				?>
				<input name="data[Profile][<?php echo $pid ?>][bet_tracks]" value="0" 
				type="radio" id="all<?php echo $pid ?>" <?php echo $ch0 ?>/>
					<label for="all<?php echo $pid ?>">Todos</label>
				<input name="data[Profile][<?php echo $pid ?>][bet_tracks]" value="1"
				type="radio" id="nat<?php echo $pid ?>" <?php echo $ch1 ?>/>
					<label for="nat<?php echo $pid ?>">NAC.</label>
				<input name="data[Profile][<?php echo $pid ?>][bet_tracks]" value="2" 
				type="radio" id="int<?php echo $pid ?>" <?php echo $ch2 ?>/>
					<label for="int<?php echo $pid ?>">INT.</label>
			</td>
			<td class="but_set">
                <?php
                $an0 = ""; $an1 = "";
				switch ($s['Profile']['anull_last']) {
					case 0:
						$an0 = "checked='checked'";
						break;
					case 1:
						$an1 = "checked='checked'";
						break;					
				}
				?>
				<input name="data[Profile][<?php echo $pid ?>][anull_last]" value="1"
				type="radio" id="si<?php echo $pid ?>" <?php echo $an1 ?>/>
					<label for="si<?php echo $pid ?>"> SI </label>
				<input name="data[Profile][<?php echo $pid ?>][anull_last]" value="0" 
				type="radio" id="no<?php echo $pid ?>" <?php echo $an0 ?>/>
					<label for="no<?php echo $pid ?>"> NO </label>
               
            </td>
			<td class="but_set">
            <?php
            if ($s['User']['role_id'] == 3) :
                
                $re0 = ""; $re1 = "";
				switch ($s['Profile']['reprint_last']) {
					case 0:
						$re0 = "checked='checked'";
						break;
					case 1:
						$re1 = "checked='checked'";
						break;					
				}
				?>
				<input name="data[Profile][<?php echo $pid ?>][reprint_last]" value="1" 
				type="radio" id="sir<?php echo $pid ?>"<?php echo $re1 ?>/>
					<label for="sir<?php echo $pid ?>"> SI </label>
				<input name="data[Profile][<?php echo $pid ?>][reprint_last]" value="0" 
				type="radio" id="nor<?php echo $pid ?>" <?php echo $re0 ?>/>
					<label for="nor<?php echo $pid ?>"> NO </label>
			
            <?php
            endif;
            ?>
			</td>
            <td class="but_set">
            <?php
            if ($s['User']['role_id'] == 3) :
                
                $re0 = ""; $re1 = "";
				switch ($s['Profile']['barcode']) {
					case 0:
						$re0 = "checked='checked'";
						break;
					case 1:
						$re1 = "checked='checked'";
						break;					
				}
				?>
				<input name="data[Profile][<?php echo $pid ?>][barcode]" value="1" 
				type="radio" id="sibc<?php echo $pid ?>"<?php echo $re1 ?>/>
					<label for="sibc<?php echo $pid ?>"> SI </label>
				<input name="data[Profile][<?php echo $pid ?>][barcode]" value="0" 
				type="radio" id="nobc<?php echo $pid ?>" <?php echo $re0 ?>/>
					<label for="nobc<?php echo $pid ?>"> NO </label>
			
            <?php
            
            endif;
            ?>
			</td>
		</tr>
	<?php	
	}
	?>
		<tr>
			<td colspan="5">
				<button id="saveall">Guardar Todo</button>
			</td>
		</tr>
	</table>
	<?php
	echo $form->end();
	?>
	<hr />
	
</div>