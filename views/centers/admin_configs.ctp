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
<style>
    .panel-config{
        width: auto;
        float: left;
        border: 1px solid blue;
        padding: 5px;
        margin: 0 10px 10px 5px;
    }
    .panel-config td {
        text-align: left;
    }
    .panel-config th {
        text-align: left;
    }
    .inp-amount{
        width: 40px;
        text-align: right;
        float: right;
        margin-right: 10px;
    }
    .inp-amo-big{
        width: 80px;
        text-align: right;
        float: right;
        margin-right: 5px;
        font-size: 110%;
    }
    .del-cnf {
        color: #900;
        padding: 1px 8px;
        font-weight: bolder;
        font-size: 130%;
        float: right;
    }
</style>

<div>
  
	<h2>NUEVAS Configuraciones de <?php echo $center['Center']['commercial_name'] ?></h2>
    
    <div class="panel-config">
        <h3>Unidades</h3>
        <?php
        //pr($units);
        echo $form->create('Config',array('action' => 'setvalues'));
        echo $form->input('Center.id',array('value' => $id));
        $unitNac = "";
        $unitInt = "";
        $delNacLink = "";
        $delIntLink = "";
        
        if ( isset ( $units[1] ) ) {
            $unitNac    = $units[1]['amount'];
            $delNacLink = $html->link('x',array('controller'=>'configs',
                                'action'=>'delete',$units[1]['id']),
                                array('class' => 'del-cnf','title' => 'Borrar Nac.'));
                
            echo $form->input('Config.1.id',array('value' => $units[1]['id'], 
                'type' => 'hidden'));
        }
        if ( isset ( $units[5] ) ) {
            $unitInt    = $units[5]['amount'];
            $delIntLink = $html->link('x',array('controller'=>'configs',
                                'action'=>'delete',$units[5]['id']),
                                array('class' => 'del-cnf','title' => 'Borrar Int.'));
            
            echo $form->input('Config.5.id',array('value' => $units[5]['id'],
                'type' => 'hidden'));
            
        }
        /*
        echo $form->input('Config.0.config_type_id',array('type' => 'hidden',
                        'value' => 1));
        
        echo $form->input('Config.1.config_type_id',array('type' => 'hidden',
                        'value' => 5));
        */
        ?>
        <table>
            <tr>
                <th>Nacional</th>
                <th>Internacional</th>
            </tr>
            <tr>
                <td>
                    <?php
                    echo $form->input('Config.1.amount',array('label' => false,
                        'value' => $unitNac ,'class' => "inp-amount"));
                    
                    echo $delNacLink;
                    ?>
                </td>
                <td>
                    <?php
                    echo $form->input('Config.5.amount',array('label' => false,
                        'value' => $unitInt ,'class' => "inp-amount"));
                    
                    echo $delIntLink;
                    
                    ?>
                </td>
            </tr>
        </table>
        <?php
        echo $form->end('Guardar');
        ?>
    </div>
    
    <div class="panel-config">
        <h3>Porcentajes</h3>
        <?php
        //pr($units);
        echo $form->create('Config',array('action' => 'setvalues'));
        echo $form->input('Center.id',array('value' => $id));
        $i = 0;
        ?>
        <table>
            <?php
            foreach ($percts as $confId => $perc) {
            ?>
                <tr>
                    <th>
                        <?php
                        echo $players[$perc['profile_id']];
                        ?>
                    </th>
                    <td>
                        <?php
                        echo $form->input("Config.$i.id",array(
                            'value' => $confId, 'type' => 'hidden'));
                        
                        echo $form->input("Config.$i.amount",array('label' => false,
                            'value' => $perc['amount'], 'class' => "inp-amount",
                            'div' => false));
                        ?>
                        
                    </td>
                    <td>
                        <?php
                        echo $html->link('x',array('controller'=>'configs','action'=>'delete',$confId),
                            array('class' => 'del-cnf','title' => 'Borrar Config.'));
                        ?>
                    </td>
                    
                </tr>
            <?php
                $i ++;
            }
            ?>
            <tr>
                <th colspan="3">Agregar otro</th>
            </tr>
            <tr>
                <td>
                    <?php
                    echo $form->input("Config.new.config_type_id",array(
                            'value' => 4, 'type' => 'hidden'));
                    
                    echo $form->input("Config.new.actual",array(
                            'value' => 1, 'type' => 'hidden'));
                    
                    echo $form->input("Config.new.profile_id",array(
                            'options' => $players, 'empty' => array(0 => 'Selec...'),
                            'label' => false));
                    ?>
                </td>
                <td colspan="2">
                    <?php
                    echo $form->input("Config.new.amount",array('class' => "inp-amount",
                            'div' => false,'label' => false));
                    ?>
                </td>
            </tr>
        </table>
        <?php
        echo $form->end('Guardar');
        ?>
    </div>
    
    <div class="panel-config">
        <h3>Limites</h3>
        <?php
        //pr($units);
        echo $form->create('Config',array('action' => 'setvalues'));
        echo $form->input('Center.id',array('value' => $id));
        $i = 0;
        ?>
        
        <table>
            <tr>
                <th>Taquilla</th>
                <th>Max. Monto TICKET</th>
                <th>Max. por Carrera</th>
                <th>Max. por Caballo</th>
            </tr>
            <?php
            $j = 0;
            foreach ($limits as $pid => $limit) {
            ?>
                <tr>
                    <td><?php echo $players[$pid] ?></td>
                    <td>
                        <?php
                        if ( isset( $limit[7] ) ) {
                            echo $form->input("Config.$j.id",array(
                                    'value' => $limit[7]['id'], 'type' => 'hidden'));
                            
                            echo $form->input("Config.$j.amount",array(
                                'class' => "inp-amo-big", 'label' => false, 'div' => false,
                                'value' => $limit[7]['amount']));

                            echo $html->link('x',array('controller'=>'configs',
                                'action'=>'delete',$limit[7]['id']),
                                array('class' => 'del-cnf','title' => 'Borrar Config.'));
                            
                            $j ++;
                            
                        } else {
                            echo "-";
                        }
                        
                        ?>
                    </td>
                    <td>
                        <?php
                        if ( isset( $limit[8] ) ) {
                            echo $form->input("Config.$j.id",array(
                                    'value' => $limit[8]['id'], 'type' => 'hidden'));
                            
                            echo $form->input("Config.$j.amount",array(
                                'class' => "inp-amo-big", 'label' => false,
                                'value' => $limit[8]['amount']));
                            
                            echo $html->link('x',array('controller'=>'configs',
                                'action'=>'delete',$limit[8]['id']),
                                array('class' => 'del-cnf','title' => 'Borrar Config.'));
                            
                            $j ++;
                        } else {
                            echo "-";
                        }
                        
                        ?>
                    </td>
                    <td>
                        <?php
                        
                        if ( isset( $limit[9] ) ) {
                            echo $form->input("Config.$j.id",array(
                                    'value' => $limit[9]['id'], 'type' => 'hidden'));
                            
                            echo $form->input("Config.$j.amount",array(
                                'class' => "inp-amo-big", 'label' => false,
                                'value' => $limit[9]['amount']));
                        
                            echo $html->link('x',array('controller'=>'configs',
                                'action'=>'delete',$limit[9]['id']),
                                array('class' => 'del-cnf','title' => 'Borrar Config.'));
                            
                            $j ++;
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>
                    
                </tr>
            <?php
                
            }
            ?>
            <tr>
                <th colspan="4">Agregar Nueva</th>
            </tr>
            <tr>
                <td>
                    <?php
                    echo $form->input("Config.new.profile_id",array(
                            'options' => $players, 'empty' => array(0 => 'Selec...'),
                            'label' => 'Usuario'));
                    ?>
                </td>
                <td>
                    <?php
                    echo $form->input("Config.new.config_type_id",array(
                            'options' => array(
                                7 => 'Max. Ticket',
                                8 => 'Max. Carrera',
                                9 => 'Max. Caballo'), 
                            'empty' => array(0 => 'Selec...'), 'label' => 'Limite'));
                    ?>
                </td>
                <td>
                    <?php
                    echo $form->input("Config.new.amount",array(
                            'label' => 'Monto', 'class' => "inp-amo-big"));
                    ?>
                </td>
            </tr>
        </table>
        <?php
        echo $form->end('Guardar');
        ?>
    </div>
    
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
            <th>Acc. Online</th>
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

            <td class="but_set">
            <?php
            if ($s['User']['role_id'] == 3) :
                
                $re0 = ""; $re1 = "";
                switch ($s['Profile']['onl_perms']) {
                    case 0:
                        $re0 = "checked='checked'";
                        break;
                    case 1:
                        $re1 = "checked='checked'";
                        break;                  
                }
                ?>
                <input name="data[Profile][<?php echo $pid ?>][onl_perms]" value="1" 
                type="radio" id="siop<?php echo $pid ?>"<?php echo $re1 ?>/>
                    <label for="siop<?php echo $pid ?>"> SI </label>
                <input name="data[Profile][<?php echo $pid ?>][onl_perms]" value="0" 
                type="radio" id="noop<?php echo $pid ?>" <?php echo $re0 ?>/>
                    <label for="noop<?php echo $pid ?>"> NO </label>
            
            <?php
            
            endif;
            ?>
            </td>
		</tr>
	<?php	
	}
	?>
		<tr>
			<td colspan="7">
				<button id="saveall">Guardar Todo</button>
			</td>
		</tr>
	</table>
	<?php
	echo $form->end();
	?>
	
</div>