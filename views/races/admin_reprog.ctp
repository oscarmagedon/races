<style>
.changetime{
    background-color: #cbd8e5;
    width: auto;
    padding: 4px;
    float: left;
    margin: 4px 0 12px 10px;
} 
.changetime div{
    clear:none;
    float:left;
}
.changetime label{
    display: inline;
}
#repro {
    width: 45px;
    font-size: 13pt;
}
</style>
<div class="races index">
<h2>Reprogramar Horas en Carreras</h2>
<table style="width:300px">
<tr>
	<td><?php 
		echo $form->input('date',array('value'=>$date,'label'=>"Fecha",'class'=>'filter_input','style'=>'width:120px'))
	?></td>
	<td><?php 
		echo $form->input('hipodrome_id',array('options'=>$hipodromes,'value'=>$htrackid,'empty'=>array(0 => 'Seleccione'),'label'=>"Hipodromo",'class'=>'filter_input'))
	?></td>
</tr>
</table>
<?php
if ( !empty($races) ) {
    echo "<div class='changetime'>";
    echo $form->input('repro',array('label' => 'Minutos','value'=>$minsRep));
    echo $form->button('Ver',array('class' => 'viewrep'));
    echo "</div>";
    //pr($races);
?>
<table cellpadding="0" cellspacing="0" border="1" class="table-total">
<tr>
	
    <th>ID</th>
    <th>Numero</th>
    <th>H. Carr.</th>
    <th>H. Local</th>
</tr>
<?php
foreach ($races as $race):
?>
	<tr>
		<td>
			<?php echo $race['Race']['id'] ?>
		</td>
		<td>
			<?php echo $race['Race']['number']."a"; ?>
		</td>
        <td>
			<?php 
            echo $dtime->time_to_human($race['Race']['race_time']);
            
            if (isset($race['New'])) {
                echo "<br /><b>";
                echo $dtime->time_to_human($race['New']['rtime']) . "</b>";
            }
            ?>
		</td>
        <td>
			<?php 
            echo $dtime->time_to_human($race['Race']['local_time']);
            
            if ( isset($race['New']) ) {
                echo "<br /><b>";
                echo $dtime->time_to_human($race['New']['local']) . "</b>";
            }
            ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php
}
//pr($races);
if ($minsRep != 0) {
    echo $form->create('Race',array('action' => 'reprog'));
    echo $form->input('date',array('value' => $date, 'type' => 'hidden'));
    echo $form->input('hipodrome_id',array('value' => $htrackid, 'type' => 'hidden'));
    echo $form->input('minsrep',array('value' => $minsRep, 'type' => 'hidden'));
    echo $form->end('Reprogramar Carreras');
}
?>

</div>
<script>
var filt_url = '<?php echo $html->url(array("action"=>"reprog")) ?>';		
$(function() { 
	$("#date");
	$("#date").attr('readonly',true)
              .datepicker({dateFormat:"yy-mm-dd"})
              .change( function () {
                   location = filt_url + "/" + $(this).val();
              });
	
	$("#hipodrome_id").change(function(){
		var date = $("#date").val(),
            hipo = $("#hipodrome_id").val();
            
		location = filt_url + "/" + date + "/" + hipo;
	});	
    
    
    $('.viewrep').click( function () {
        var date = $("#date").val(),
            hipo = $("#hipodrome_id").val(),
            mins = $("#repro").val();
            
		location = filt_url + "/" + date + "/" + hipo + "/" + mins;
    });	
	
});	
</script>