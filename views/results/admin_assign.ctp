<script>
var races = '<?php echo $html->url(array('action'=>'getthem')) ?>';
var centers = '<?php echo $html->url(array('action'=>'centers')) ?>';
var races_son = Object();
$(function(){
	$("#get_backer").hide();
	$(".stat_loader").hide();
	$("#ResultHipo").change(function(){
		var itop = '';
		$("#ResultRaces").html("<option value=''>Carreras...</option>");
		$.getJSON(races + "/" + $(this).val(), function(data){
			$.each(data, function(i,item){
				itop = itop + "<option value='" + i + "'>" + item +  "</option>";
			});
			$("#ResultRaces").append(itop).attr('disabled',false);;
		});
	});
	
	$("#ResultRaces").change(function(){
		var itop = '';
		$("#ResultCenters").html("<option value=''>Centros...</option>");
		$.getJSON(centers + "/" + $(this).val(), function(data){
			$.each(data.centers, function(i,item){
				itop = itop + "<option value='" + i + "'>" + item +  "</option>";
			});
			
			$("#ResultCenters").append(itop).attr('disabled',false);;
		
			$.each(data.races, function(i,item){
				races_son[i] = item;
			});
			
		});
	});
	
	$("#ResultCenters").change(function(){
		$("#ResultRaceSon").val(races_son[$(this).val()]);
	});
	
	$("#get_backer").click(function(){
		location.reload();
		return false;
	});
	
	$("#ResultAssignForm").submit(function(){
		if($("#ResultCenters").val() == ''){
			alert("Debe seleccionar los 3 campos");
		}else{
			firstSending($(this));
			$("#all_data").hide('medium');	
		}
		return false;
	});
});

function firstSending(form) {
	form = $(form);
	$.ajax({
	    type: (form.attr('method')),
	    data: form.serialize(),
	    
	    beforeSend: function(){
			$("#first_stat").show("medium");
			$("#first_stat > span").html("Asignando y copiando los resultados...");
		},
       	success: function(html){
        	$("#first_stat > span").html(html);
        	$("#first_stat").addClass("stat_loader_done");
        	$("#first_stat > div").addClass("img_done");
        	$("#get_backer").show();
       	},
       	error: function(){
       		$("#first_stat > span").html("El proceso ha fallado. Intente de nuevo");
       	}
	});
	
	return false;
}
</script>
<style>
h3{
	font-size:120%;
	margin-top: 5px;
	text-decoration: none;
}
.stat_loader{
	border: 1px solid red; 
	width: 400px; 
	height: 25px;
	background-color:#FCC7C7;
	color: Red;
	-moz-border-radius: 6px; 
	-webkit-border-radius: 6px; 
	margin-top: 50px;
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
}
</style>
<div id='all_data'>
	<h2>Asignar RESULTADOS DE CARRERAS</h2>
	<?php 
	echo $form->create('Result',array('action'=>'assign'));
	
	$opts = array(0=>"Hipodromos...");
	foreach($races as $r){
		$opts[$r['Hipodrome']['id']] = $r['Hipodrome']['name']." (".$r[0]['co']." carrs.)";
	}
	?>
	<h3>Seleccione Hipodromo</h3>
	
	<?php echo $form->input('hipo',array('options'=>$opts,'label'=>false,'div'=>false)) ?>
	
	<h3>Seleccione La Carrera (debe tener resultados)</h3>
	
	<select id='ResultRaces' name='data[Result][races]' disabled=true>
		<option value=''>Carreras...</option>
	</select>
	
	<h3>Seleccione El Centro (debe haber sido copiado desde el Master)</h3>
	
	<select id='ResultCenters' name='data[Result][centers]' disabled=true>
		<option value=''>Centros...</option>
	</select>
	
	<?php
	echo $form->input('race_son',array('type'=>'hidden','label'=>false,'div'=>false));
	
	echo $form->end('Asignar');
	?>
</div>
<div class="stat_loader" id="first_stat">
	<span></span>
	<div class="img_wait"></div>
</div>
<div id="get_backer">
	<a href="#" id="get_back">Volver a Asignar</a>
</div>