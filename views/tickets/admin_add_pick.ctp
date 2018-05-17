<script>
var load_img = '<?php echo $html->image("loading_small.gif",array("alt"=>"Esperando..."))?>';
var load_races = '<?php echo $html->url(array("controller"=>"races","action"=>"data_pick"))?>';
var load_horses = '<?php echo $html->url(array("controller"=>"horses","action"=>"data_pick"))?>';
var dat = '<?php echo date("Y-m-d")?>';
$(function(){
	$("#pkraces").html("<div id='pktext'>Seleccione Hipodromo y Carreras</div>");
	$("#pkmess_bet").hide();
	err_line(1,"Recuerde que las PICKS deben ser del mismo hipodromo",$("#pkmess_bet"));
	
	$("#pk_type").buttonset().change(function(){
		put_races_on();
	});
	$("#TicketHipodromeId").change(function(){
		put_races_on();	
	});
	
	$("#pkbetting").button({icons: {primary: "ui-icon-circle-check"}}).css({'width':'150px','margin-top':'20px'}).click(
		function(){
			var val = true;
			
			if($("#TicketUnits").val() == "" || $("#TicketUnits").val() == 0){
				err_line(2,"Las unidades deben ser mayor a cero.",$("#pkmess_bet"));
				val = false;
			}
			
			
			$(".horsesall").each(function(){
				if(val){
					if($(this).val() == 0){
						err_line(2,"Debe escoger TODOS los caballos en carreras.",$("#pkmess_bet"));
						val = false;
					}	
				}
			});
			
			if(val == true){
				$("#TicketRaceId").val($("#race_1").val());
				$(this).parents('form').submit();
			}else{
				return false;
			}
			
		}
	);
	
});

function put_races_on(){
	var h = $("#TicketHipodromeId").val();
	if(h != 0){
		show_races(h,0);	
	}
}

function show_races(hipo,from){
	var q = $("#pk_type").find("input:checked").val();
	var alldata = Array();
	$("#pkraces").html("<div id='pktext'>Cargando... Espere. <br>" + load_img + "</div>");
	
	$.getJSON(load_races + "/" + hipo + "/" + dat, function(data) {
		alldata = data;
	 	var topos = Object();
	 	var t = 1;
	 	$.each(data['Races'], function(key, val) {
			topos[t] = key;
			t++;
		});
		
		var availab_races = t - 1;
		
		if(availab_races < q){
			err_line(2,"Hay solamente "+availab_races+" carreras disponibles. No se permite el PICK "+q,$("#pkmess_bet"));
		}else{
			$("#pkraces").html(construct_table(q));
			
			$("#caballos").find(".race_num").each(function(){
				
				var mypar = $(this).parents('tr').attr('id');
				var find = mypar;
				if(from != 0){
					$.each(topos, function(key, val) {
						if(val == from)
							find = key;
					});
				}
				
				if(mypar == 1){
					var race_opts = "<select name='race' id='first_race'>";
					$.each(data['Races'], function(key, val) {
						var sel = "";
						if(key == topos[find])
							sel = " selected='selected'";
						
						race_opts = race_opts + "<option value='"+ key +"'"+ sel +">"+ val +"</option>";
					});
					race_opts = race_opts +"</select>";	
				}else{
					if(from != 0)
						find = mypar*1 + find*1 - 1;
					else
						find = mypar;
					
					$.each(data['Races'], function(key, val) {
						if(key == topos[find])
							race_opts = val;
					});
				}
									
				$(this).html(race_opts);
			
				var horses_part = $(this).parents('tr').find('.hors_num');
				var hors_opts = "";
				
				horses_part.html(load_img);
				
				if(data['Horses'][topos[find]] != null){
					var i = 0;
					$.each(data['Horses'][topos[find]], function(key, val) {
					 	hors_opts = hors_opts + "<label for='Horses" + mypar + i + "'>" + val + "</label>" +
						"<input name='data[Horses][" + mypar + "][" + i + "]' value='" + key + 
						"' id='Horses" + mypar + i + "' type='checkbox'>";
						i ++;
					});	
				}
				horses_part.html(hors_opts);
			});
			
			$("#caballos").find(".hors_num").buttonset();	
			
			$("#first_race").change(function(){	
				show_races(hipo,$(this).val());
			});
		}	
	});
	
	
}


function construct_table(rows){
	var tohtml = "<table id='caballos' border='1'><tr><th>No.</th><th>Carrera</th><th>Caballos</th></tr>";
	for(i = 1; i <= rows; i = i + 1){
		tohtml = tohtml + "<tr class='row_pos' id='"+ i +"'><td id='"+ i +"_title'>"+ i +"a</td>" +
		"<td class='race_num'>" + load_img + "</td><td class='hors_num'> - </td></tr>";
	}
	tohtml = tohtml + "</table>";
	return tohtml;
}

</script>
<style>
#pktitl{
	color: #255E00;
    float: left;
    font-size: 160%; font-weight: bold;
    margin-bottom: 2px;
    padding-bottom: 3px; padding-top: 10px;
    width: 240px; height:30px;
}
#pkmess_bet{
	width: 500px; height:35px; 
	padding-left:5px;
	font-size: 110%;
	border: 1px solid;
	clear: right;
	float: left;
}
#pkdiv_bet{
	float:left; 
	width:800px;
	height:350px;
	border: 1px solid black;
	margin-bottom: 5px;
	padding: 5px;
}
#pk_type{
	width: 200px;
	height: 50px;
	float: left;
	clear: none;
	border-bottom: 1px solid black;
	font-size: 120%;
	padding-bottom: 3px;
}
#pkhipo{
	width: 200px;
	height: 50px;
	float: left;
	clear: none;
	border-bottom: 1px solid black;
	font-size: 120%;
	margin-top: 5px;
	padding-bottom: 3px;
}
	#TicketHipodromeId{
		font-size: 150%;
	}
#pkraces{
	width: 520px;
	height: 350px;
	float: right;
	clear: none;
	border-left: 1px solid black;
}
	#first_race{
		font-size: 110%;
	}
	#pktext{
		font-size: 150%;
		color: #49A006;
		margin: 50px 0 0 20px;
	}
#pkunits{
	float:left; clear:left; 
	width: 200px; height: 100px; 
	margin-top: 5px;
	font-size: 120%;
}
.race_num{
	font-size: 110%;
}
</style>
<div class='tickets'>
	<div id="pktitl">PICKS<?php echo ", ".$dtime->date_spa_mon_abr(date("Y-m-d")); ?></div>
	<div id="pkmess_bet"></div>
	<?php 
	echo $form->create('Ticket',array('action'=>'add_pick','div'=>false));
	echo $form->input('race_id',array('type'=>'hidden'));
	?>
	<div id="pkdiv_bet">
		<div id="pk_type">
		Carreras: <br />
		<?php echo $form->radio('picks',array(2=>2,3=>3,4=>4,6=>6,9=>9),array('legend'=>false,'value'=>2)) ?>	
		</div>
		<div id="pkraces"></div>
		<div id="pkhipo">
 			<?php 
 			echo $form->input('hipodrome_id',
 				array('options' => $hipodromes,'empty' => array(0 => 'Seleccione'),'label'=>'Hip&oacute;dromo'))
 			?>
		</div>
		
		<div id="pkunits">
			Unidades: 
			<?php echo $form->input('units',array('label'=>'','class'=>'field_final')) ?>
			<button id="pkbetting" title="Apostar">APOSTAR</button>
		</div>
	</div>
	<?php echo $form->end() ?>
</div>
