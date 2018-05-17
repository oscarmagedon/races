<?php
$race_id = $race['Race']['id'];

$horses = $dtime->getListedEnabled($allHorses);

$theid1 = 0;
$theid2 = 0;
$theid3 = 0;
$theid4 = 0;
$theid5 = 0;

$c1 = 0;
$c2 = 0;
$c3 = 0;
$c4 = 0;
$c5 = 0;

$w1 = "";
$p1 = ""; 
$s1 = "";
$p2 = "";
$s2 = "";
$s3 = "";

$w4 = "";
$p4 = "";
$s4 = "";

if(!empty($results)){
	
	$theid1 = $results[1]['id'];
	$theid2 = $results[2]['id'];
	$theid3 = $results[3]['id'];
	$theid4 = $results[4]['id'];

	$c1 = $results[1]['horse_id'];
	$c2 = $results[2]['horse_id'];
	$c3 = $results[3]['horse_id'];
	$c4 = $results[4]['horse_id'];
	
	$w1 = $results[1]['win'];
	$p1 = $results[1]['place']; 
	$s1 = $results[1]['show'];
	$p2 = $results[2]['place'];
	$s2 = $results[2]['show'];
	$s3 = $results[3]['show'];
	
	if(!empty($results[5])){
		$theid5 = $results[5]['id'];
		$c5 = $results[5]['horse_id'];
		
		$w4 = $results[5]['win'];
		$p4 = $results[5]['place']; 
		$s4 = $results[5]['show'];
	}
	
}
?>
<script>
var draw_flag = false;

var messages  = {
					'empty'  : 'Ningun campo puede estar vacio.',
					'horse'  : 'Debe seleccionar caballos correctos.'
				};

$(function(){
	
	$("#hasdraw").button({icons: { primary: "ui-icon-newwin" }}).click(function(){
		$("#draw_first").show('slow');
		draw_flag = true;
		$(this).hide();
		$("#hasnotdraw").show();
		return false;
	});

	$("#hasnotdraw").button({icons: { primary: "ui-icon-close" }}).click(function(){
		$("#draw_first").hide('slow');
		draw_flag = false;
		$(this).hide();
		$("#hasdraw").show();
		return false;
	});
	
	<?php
	if(!empty($results[5])){
	?>
		$("#hasnotdraw").show();
		$("#hasdraw").hide();
	<?php
	}else{
	?>
		$("#hasnotdraw").hide();
		$("#draw_first").hide();
	<?php
	}
	?>
	//retired horses
	$(".retired-horses").buttonset();
	
	//validation messages
	$(".row-validation").hide();
	
	// only number! take it to js libs
	$(".number-race").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
	
	//validation before sending
	$("#ResultSetForm").submit(function(){
		var tosub = true;

		if(draw_flag == false){
			$("#draw_first").remove();
		}
		
		//validate number empty 
		$(".number-race").each(function(i){
			
			if($(this).val() == ""){
				$(".row-validation").find('th').html(messages['empty'])
				$(".row-validation").show('slow');
				tosub = false;
			}
		});

		//
		$(".horse_sel").each(function(i){
			if($(this).val() == 0){
				$(".row-validation").find('th').html(messages['horse'])
				$(".row-validation").show('slow');
				tosub = false;
			}
		});
		
		return tosub;
	});
	
	
});
</script>
<?php
echo $form->create('Result',array('action' => 'set'));
?>
<div class="horses index">
<h2>
	RESULTADOS :: 
	<?php echo $dtime->date_spa_mon_abr($race['Race']['race_date']) ?> &gt;&gt;
	<?php echo $race['Hipodrome']['name']?> &gt;&gt;
	<?php echo $race['Race']['number']?>&ordf; carr.  
</h2>

<table style="width:800px">
	<tr>
		<th>Resultados</th><th>Retirados</th>
	</tr>
	<tr>
		<td rowspan="3">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th>Posicion</th>
					<th>Caballo</th>
					<th>Win</th>
					<th>Place</th>
					<th>Show</th>
				</tr>
				<tr>
					<td>1&ordm;
						<?php
						if($theid1 != 0){
							echo $form->input("Result.0.id",array('value' => $theid1,'type'=>'hidden'));	
						}
						
						echo $form->input("Result.0.position.",array('value' => 1,'type'=>'hidden'));
						echo $form->input("Result.0.race_id",array('value' => $race_id,'type'=>'hidden'));
						?>
					</td>
					<td>
						<?php echo $form->input("Result.0.horse_id.",array('options' => $horses,'empty' => array(0 => 'Co.'),'value' => $c1,'class' => 'horse_sel')) ?>
					</td>
					<td>
						<?php echo $form->input("Result.0.win",array('class'=>'number-race','value'=>$w1,'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.0.place",array('class'=>'number-race','value'=>$p1,'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.0.show",array('class'=>'number-race','value'=>$s1,'label'=>false,'div'=>false)) ?>
					</td>
				</tr>
				<tr id="draw_first">
					<td>1&ordm; (empate)
						<?php
						if($theid5 != 0){
							echo $form->input("Result.4.id",array('value' => $theid5,'type'=>'hidden'));	
						}
						
						echo $form->input("Result.4.position.",array('value' => 1,'type'=>'hidden'));
						echo $form->input("Result.4.race_id",array('value' => $race_id,'type'=>'hidden'));
						?>
					</td>
					<td>
						<?php echo $form->input("Result.4.horse_id.",array('options' => $horses,'empty' => array(0 => 'Co.'),'value' => $c5,'class' => 'horse_sel','title'=>'drawer','label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.4.win",array('class'=>'number-race','value'=>$w4,'title'=>'drawer','label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.4.place",array('class'=>'number-race','value'=>$p4,'title'=>'drawer','label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.4.show",array('class'=>'number-race','value'=>$s4,'title'=>'drawer','label'=>false,'div'=>false)) ?>
					</td>
				</tr>
				<tr>
					<td>2&ordm;
						<?php
						if($theid2 != 0){
							echo $form->input("Result.1.id",array('value' => $theid2,'type'=>'hidden'));	
						}
						
						echo $form->input("Result.1.position.",array('value' => 2,'type'=>'hidden'));
						echo $form->input("Result.1.race_id",array('value' => $race_id,'type'=>'hidden'));
						?>
					</td>
					<td>
						<?php echo $form->input("Result.1.horse_id.",array('options' => $horses,'empty' => array(0 => 'Co.'),'value' => $c2,'class' => 'horse_sel','label'=>false,'div'=>false)) ?>
					</td>
					<td>
						-
					</td>
					<td>
						<?php echo $form->input("Result.1.place",array('class'=>'number-race','value'=>$p2,'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.1.show",array('class'=>'number-race','value'=>$s2,'label'=>false,'div'=>false)) ?>
					</td>
				</tr>
				<tr>
					<td>3&ordm;	
						<?php
						if($theid1 != 0){
							echo $form->input("Result.2.id",array('value' => $theid3,'type'=>'hidden'));	
						}
						
						echo $form->input("Result.2.position.",array('value' => 3,'type'=>'hidden'));
						echo $form->input("Result.2.race_id",array('value' => $race_id,'type'=>'hidden'));
						?>
					</td>
					<td>
						<?php echo $form->input("Result.2.horse_id.",array('options' => $horses,'empty' => array(0 => 'Co.'),'value' => $c3,'class' => 'horse_sel','label'=>false,'div'=>false)) ?>
					</td>
					<td>
						-
					</td>
					<td>
						-
					</td>
					<td>
						<?php echo $form->input("Result.2.show",array('class'=>'number-race','value'=>$s3,'label'=>false,'div'=>false)) ?>
					</td>
				</tr>
				<?php 
				if(count($horses) > 3){
				?>
				<tr>
					<td>4&ordm;	
						<?php
						
						if($theid1 != 0){
							echo $form->input("Result.3.id",array('value' => $theid4,'type'=>'hidden'));	
						}
						
						echo $form->input("Result.3.position.",array('value' => 4,'type'=>'hidden'));
						echo $form->input("Result.3.race_id",array('value' => $race_id,'type'=>'hidden'));
						?>
					</td>
					<td>
						<?php echo $form->input("Result.3.horse_id.",array('options' => $horses,'empty' => array(0 => 'Co.'),'value' => $c4,'class' => 'horse_sel','label'=>false,'div'=>false)) ?>
					</td>
					<td>
						-
					</td>
					<td>
						-
					</td>
					<td>
						-
					</td>
				</tr>
				<?php 
				}
				?>
				<tr class="row-validation">
					<th colspan="5">
					</th>
				</tr>
			</table>
			
			<button id="hasdraw">Empate en 1o
			
			<button id="hasnotdraw">SIN Empate</button>
			
		</td>
		
		<td>
			<div class="retired-horses">
			<?php
			foreach($allHorses as $k => $r){
				$checked = false;
				if($r['enable'] == 0)
					$checked = true;				
				
				echo $form->input("Retired." . $r['id'],
						array(
							'type'    => 'checkbox',
							'value'   => $r['enable'],
							'checked' => $checked,
							'div'     => false,
							'label'   => $dtime->horseName($r['number'],$r['name'])
						)
				);
			}
			?>
			</div>
		</td>
	</tr>
	<tr>
		<th>Premios Especiales</th>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					<td>Exacta</td>
					<td><?php 
						echo $form->input("Special.exacta",
							array('class'=>'specials','value'=>$race['Race']['exacta'],'label'=>false,'div'=>false)) 
					?></td>
				</tr>
				<tr>
					<td>Trifecta</td>
					<td><?php 
						echo $form->input("Special.trifecta",
							array('class'=>'specials','value'=>$race['Race']['trifecta'],'label'=>false,'div'=>false))
					 ?></td>
				</tr>
				<tr>
					<td>Superfecta</td>
					<td><?php 
						echo $form->input("Special.superfecta",
							array('class'=>'specials','value'=>$race['Race']['superfecta'],'label'=>false,'div'=>false)) 
					?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<?php 
	echo $form->end('GUARDAR');
?>