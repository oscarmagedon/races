<style>
.number_race{
	width:60px;
	font-size:130%;
}
#GeneralRaceDate{
	width:100px;
}
h3{
padding-top:5px;
}
</style>
<script>
$(function(){
	$("label").hide();
	$("#GeneralRaceDate").datepicker({dateFormat:"yy-mm-dd"});

	$("#plus").click(function(){
		addOptions($("#add").val());
	});

});
var indexRowForm = 0;
function addOption(){
	myNewRow    = document.getElementById("races").insertRow(-1); 
	myNewRow.id = indexRowForm;
	myNewCell   = myNewRow.insertCell(-1);
	myNewCell.innerHTML = "<input name='data[Race]["+indexRowForm+
                        "][number]' value='" + (indexRowForm*1 + 1*1) + "' maxlength='11' "+
                        "id='Race"+indexRowForm+"Number' type='text' class='number_race'>";
	
    myNewCell   = myNewRow.insertCell(-1);
	
	myNewCell.innerHTML="<select name='data[Race]["+indexRowForm+"][race_time][hour]'"+
					" id='Race"+indexRowForm+"RaceTimeHour' class='valid_race'>"+
					get_hours() + 
					"</select>:"+
					"<select name='data[Race]["+indexRowForm+"][race_time][min]'"+
					" id='Race"+indexRowForm+"RaceTimeMin'>"+
					get_minutes() +
					"</select>"+
					"<select name='data[Race]["+indexRowForm+"][race_time][meridian]'"+
					" id='Race"+indexRowForm+"RaceTimeMeridian'>"+
					"<option value='am'>am</option>"+
					"<option value='pm' selected='selected'>pm</option></select>";
	
    myNewCell   = myNewRow.insertCell(-1);
	myNewCell.innerHTML= "<input name='data[Race]["+indexRowForm+
                        "][horses_num]' value='10' maxlength='11' "+
                        "type='text' class='number_race'>";
    
	myNewCell=myNewRow.insertCell(-1);
	myNewCell.innerHTML="<input type='button' value=' X ' class='number_race' onclick='removePerson(this)'>";
	indexRowForm++;
}

function addOptions(times){
	for(i=0; i<times; i++){
		addOption();
	}
}

function removePerson(obj){ 
	var oTr = obj;
	while(oTr.nodeName.toLowerCase()!='tr'){
		oTr=oTr.parentNode;
	}
	var root = oTr.parentNode;
	root.removeChild(oTr);
}

function get_hours(){
	var opts = "";
	var j = 1;
	for(j = 1;j<=12;j++){
		opts = opts + "<option value=" + j;

		if(j == 2)
			opts = opts + " selected=selected";

		opts = opts + ">" + j + "</option>";
	} 
	return opts;
}

function get_minutes(){
	var opts = "";
	var j = 1;
	for(j = 1;j<=60;j++){
		opts = opts + "<option value=" + j ;

		if(j == 30)
			opts = opts + " selected=selected";

		opts = opts + ">" + j + "</option>";
	} 
	return opts;
}
</script>
<div class="races form">
<?php echo $form->create('Race');?>
	<fieldset>
 		<legend>Agregar Carrera</legend>
 		<h3>Datos Generales</h3>
		<table>
			<tr>
				<th>Hipódromo:</th>
				<td><?php echo $form->input('General.hipodrome_id') ?></td>
				<th>Fecha:</th>
				<td><?php echo $form->input('General.race_date',array('type'=>'text','value'=>date("Y-m-d"))) ?></td>
				<th>Agregar:</th>
				<td>
					<input type="text" value="1" class="number_race" id="add">
					<input type="button" value=" + " class="number_race" id="plus">
				</td>
			</tr>
		</table>
		<h3>Datos de Carreras</h3>
		<table id="races" style="width:500px">
			<tr>
				<th>Carrera</th>
				<th>Hora</th>
                <th>Ejemplares</th>
				<th>Borrar</th>
			</tr>
		</table>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>