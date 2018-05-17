<script>
var load_img = 'Cargando... <?php echo $html->image("loading_small.gif",array("alt"=>"Esperando..."))?>';
var view_horses = '<?php echo $html->url(array("controller"=>"horses","action"=>"view"))?>';
var view_results = '<?php echo $html->url(array("controller"=>"results","action"=>"view"))?>';

$(function() {
	$("#date").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
	$("#date").datepicker();
	
	$("#filt").click(function(){
		var filt_url = '<?php echo $html->url(array("action"=>"view","/")) ?>'
		var date = $("#date").val();
		var hipo = $("#hipodrome_id").val();
		
		location = filt_url + "/" + date + "/" + hipo;
	});	
 
});	
</script>

<style>
 
</style>

<div class="races index">
<h2>Carreras</h2>
<table class="table-total">
<tr>
	<th>Filtrar Por:</th>
	<td><?php 
		echo $form->input('date',array('value'=>$date,'label'=>"Fecha",'class'=>'filter_input','style'=>'width:120px'))
	?></td>
	<td><?php 
		echo $form->input('hipodrome_id',array('options'=>$hipodromes,'value'=>$htrackid,'empty'=>array(0 => 'Seleccione'),'label'=>"Hipodromo",'class'=>'filter_input'))
	?></td>
	<td><?php echo $form->button('Filtrar',array('id'=>'filt')) ?></td>
</tr>
</table>
<p>
<?php 
echo $paginator->counter(array(
'format' => "Pagina %page% de %pages%, mostrando %current% registros de %count% totales, empezando en %start%, terminando en %end%"));
?></p>
<table cellpadding="0" cellspacing="0"  class="table-total">
<tr>
	<th><?php echo $paginator->sort('Hipodromo','hipodrome_id');?></th>
	<th><?php echo $paginator->sort('Numero','number');?></th>
	<th><?php echo $paginator->sort('Fecha','race_date');?></th>
	<th><?php echo $paginator->sort('Hora Carr.','race_time');?></th>
	<th><?php echo $paginator->sort('Hora Local','local_time');?></th>
	<th>Caballos</th>
</tr>
<?php
$j = 0;
foreach ($races as $race):
?>
	<tr>
		<td><?php echo $race['Hipodrome']['name']; 
			
			if($race['Race']['enable'] == 0)
				echo "<br /><span style='color:Red'>Suspendida</span>";
				
			if($race['Race']['ended'] == 1)
				echo "<br /><span style='color:Blue'>Finalizada</span>";
			
		?></td>
		<td>
			<?php echo $race['Race']['number']."°"; ?>
		</td>
		<td>
			<?php echo $dtime->date_spa_mon_abr($race['Race']['race_date']) ?>
		</td>
		<td>
			<?php echo $dtime->time_to_human($race['Race']['race_time']) ?>
		</td>
        <td>
			<?php echo $dtime->time_to_human($race['Race']['local_time']) ?>
		</td>
		<td class="horses_div">
            <?php 
            $this->race = $race;
            $elemCall = 'raceViewHorses';
            if ($race['Race']['ended'] == 1) {
                $elemCall = 'raceViewResults';
            }
            
            echo $this->element($elemCall) ?>
            
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< atras', array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next('adelante >>', array(), null, array('class'=>'disabled'));?>
</div>