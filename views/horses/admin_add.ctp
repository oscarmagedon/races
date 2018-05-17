<style>
.number_race{
	width:60px;
	font-size:130%;
}
.name_horse{
	width:200px;
	font-size:110%;
}
#GeneralRaceDate{
	width:100px;
}
</style>
<script>
$(function(){
	$("#plus").click(function(){
		addOptions($("#add").val());
	});

	$('.open_panel').click(function() {
		$('#panel_look').html('<?php echo $html->image("loading.gif")?>');
		$('#panel_look').load($(this).attr("href"));
		$('#panel_look').dialog('open');
		return false;
	});
});
var indexRowForm = 0;
function addOption(){
	myNewRow = document.getElementById("horses").insertRow(-1); 
	myNewRow.id=indexRowForm;
	myNewCell=myNewRow.insertCell(-1);
	myNewCell.innerHTML="<input name='data[Horse]["+indexRowForm+
	"][number]' value='" + (indexRowForm*1 + 1*1) + "' maxlength='11' "+
	"id='Race"+indexRowForm+"Number' type='text' class='number_race'>";
	myNewCell=myNewRow.insertCell(-1);
	myNewCell.innerHTML="<input name='data[Horse]["+indexRowForm+
	"][name]' value='' maxlength='50' "+
	"id='Race"+indexRowForm+"Name type='text' class='name_horse'>";
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
</script>
<div class="races form">
<?php 
	echo $form->create('Horse');
	echo $form->input('General.race_id',array('value'=>$race_id,'type'=>'hidden'));
?>
 	<table id="horses" style="font-size:80%">
		<tr>
			<th>Numero</th>
			<th>Nombre</th>
			<th>Agregar:
				<input type="text" value="1" class="number_race" id="add">
				<input type="button" value=" + " class="number_race" id="plus">
			</th>			
		</tr>
	</table>
<?php 
	echo $form->end();
	echo $html->link("Ver Caballos", array('action'=>'details',$race_id),array('class'=>'open_panel')); 
?>
</div>