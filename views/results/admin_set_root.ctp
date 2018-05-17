<?php
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
$(function(){
	$("#fields_empty").hide();
	$("#fields_number").hide();
	$("#horse_sel").hide();
	$('.horse_check').hide();
	$(".stat_loader").hide();
	$("#get_back").hide();
	
	
	$("#row_horse").buttonset();

	$("#save_results").button({icons: { primary: "ui-icon-disk" }}).click(function(){
		var tosub = true;

		if(draw_flag == false){
			$("#draw_first").remove();
		}
		
		$(".number_race").each(function(i){

			if($(this).val() == ""){
				$("#fields_empty").show('slow');
				tosub = false;
			}
			if(isNaN($(this).val())){
				$("#fields_number").show('slow');
				tosub = false;
			}	
		});

		$(".horse_sel").each(function(i){
			if($(this).val() == 0){
				$("#horse_sel").show('slow');
				tosub = false;
			}
		});
		
		if(tosub == true){
			$("#all_data").hide('fast');
			firstSending($("#ResultSetRootForm"));
		}
		return false;
	});	

	$("#get_back").click(function(){
		history.back();
		return false;
	});
	
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
});

function firstSending(form) {
	form = $(form);
	$.ajax({
	    type: (form.attr('method')),
	    data: form.serialize(),
	    
	    beforeSend: function(){
			$("#first_stat").show("medium");
			$("#first_stat > span").html("Guardando los resultados de los caballos...");
		},
       	success: function(html){
        	$("#first_stat > span").html(html);
        	$("#first_stat").addClass("stat_loader_done");
        	$("#first_stat > div").addClass("img_done");
        	//secondSending(form);
        	$("#get_back").show('slow');
       	},
       	error: function(){
       		$("#first_stat > span").html("El proceso ha fallado. Intente de nuevo");
       	}
	});
	
	return false;
}
</script>
<style>
.number_race{
	width:60px;
	font-size:130%;
}
.specials{
	width:80px;
	font-size:130%;
}
#save_results{
	width: 200px;
	margin-top:20px;	
}
.stat_loader{
	border: 1px solid red; 
	width: 400px; 
	height: 25px;
	background-color:#FCC7C7;
	color: Red;
	-moz-border-radius: 6px; 
	-webkit-border-radius: 6px; 
	margin-top: 5px;
	padding-top: 5px;
	padding-left: 10px;
	font-size: 120%;
}
.stat_loader_done{
	border: 1px solid green; 
	background-color:#B1F4B0;
	color: Green;
}
.img_wait{
	width:20px;
	height:20px;
	float: right;
	margin-right: 5px;
	background-image:url(<?php echo $html->url("/img/wait.png") ?>)
}
.img_done{
	background-image:url(<?php echo $html->url("/img/check.png") ?>)
}
#get_backer{
	margin-top: 10px;
	margin-left: 10px;
	font-size: 120%;
	cursor: pointer;
}
</style>
<?php
echo $form->create('Result',array('action' => 'set_root'));
?>
<div class="horses index">
<h2>
	Resultados de la <?php echo $race['Race']['number']?>&ordf; 
	Carrera del <?php echo $dtime->date_spa_mon_abr($race['Race']['race_date']) ?>
	de <?php echo $race['Hipodrome']['name']?>
</h2>
<div class="stat_loader" id="first_stat">
	<span></span>
	<div class="img_wait"></div>
</div>
<div id="get_backer">
	<a href="#" id="get_back">Volver a Carreras</a>
</div>
<table style="width:800px" id="all_data">
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
						<?php echo $form->input("Result.0.win",array('class'=>'number_race','value'=>$w1,'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.0.place",array('class'=>'number_race','value'=>$p1,'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.0.show",array('class'=>'number_race','value'=>$s1,'label'=>false,'div'=>false)) ?>
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
						<?php echo $form->input("Result.4.win",array('class'=>'number_race','value'=>$w4,'title'=>'drawer','label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.4.place",array('class'=>'number_race','value'=>$p4,'title'=>'drawer','label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.4.show",array('class'=>'number_race','value'=>$s4,'title'=>'drawer','label'=>false,'div'=>false)) ?>
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
						<?php echo $form->input("Result.1.place",array('class'=>'number_race','value'=>$p2,'label'=>false,'div'=>false)) ?>
					</td>
					<td>
						<?php echo $form->input("Result.1.show",array('class'=>'number_race','value'=>$s2,'label'=>false,'div'=>false)) ?>
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
						<?php echo $form->input("Result.2.show",array('class'=>'number_race','value'=>$s3,'label'=>false,'div'=>false)) ?>
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
				<tr>
					<th colspan="5" style="color:Red;">
						<span id="fields_empty">
							Ningun campo puede estar vacio.
						</span>
						<span id="fields_number">
							Solo se aceptan numeros
						</span>
						<span id="horse_sel">
							Debe seleccionar caballos correctos
						</span>
					</th>
				</tr>
			</table>
			
			<button id="hasdraw">Empate en 1o
			
			<button id="hasnotdraw">SIN Empate</button>
			
		</td>
		<td>
			<div id="row_horse">
			<?php
			$i = 0;
			foreach($for_retire as $hk => $hv){ 
				$chk = "";
				if(in_array($hk,$retires)){
					$chk = " checked='checked'";
				}			
			?>
				<label for="Retire<?php echo $i ?>"><?php echo $hv ?>&ordm;</label>
				<input name="data[Retire][<?php echo $i ?>]" value="<?php echo $hk ?>" 
					id="Retire<?php echo $i ?>" type="checkbox"<?php echo $chk ?>>				
			<?php 
				$i ++;
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
	<tr>
		<td colspan="2">
			<button id="save_results">Guardar Resultados</button>	
		</td>
	</tr>
</table>
</div>
<?php 
	echo $form->end();
?>