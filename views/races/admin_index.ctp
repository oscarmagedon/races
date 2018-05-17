<script>
var filtUrl = '<?php echo $html->url(array("action"=>"index","/")) ?>';
        
$(function() {
	$("#date").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
	
	$("#filt").click(function(){
		var date     = $("#date").val(),
            hipo     = $("#hipodrome_id").val(),
            end      = $("#ended option:selected").val();
		
		location = filtUrl + "/" + date + "/" + hipo + "/" + end;
	});	
	
	$("#panel_look").dialog({
		autoOpen: false,
		bgiframe: true,		
		modal: true,
		width: 500,
		height: 350,
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
	
	$(".to_redir").click(function(){
		location = $(this).attr('href');
	});
	
	$(".act_each button").button({
	    icons: {
			primary: "ui-icon-circle-minus"
		},
		text: false
	}).next().button({
	    icons: {
			primary: "ui-icon-circle-plus"
		},
		text: false
	}).next().button({
	    icons: {
			primary: "ui-icon-pencil"
		},
		text: false
	}).next().button({
	    icons: {
			primary: "ui-icon-document"
		},
		text: false
	}).next().button({
	   	icons: {
			primary: "ui-icon-note"
		},
		text: false
	}).next().button({
	   	icons: {
			primary: "ui-icon-home"
		},
		text: false
	}).next().button({
	   	icons: {
			primary: "ui-icon-flag"
		},
		text: false
	}).next().button({
	   	icons: {
			primary: "ui-icon-wrench"
		},
		text: false
	}).next().button({
	   	icons: {
			primary: "ui-icon-circle-close"
		},
		text: false
	});
});	
</script>
<style>
    .race-started {
        color: #B00;
    }
</style>
<div class="races index">
<h2>Carreras</h2>
<table style="width:80%">
<tr>
	<th>Filtrar Por:</th>
	<td><?php 
		echo $form->input('date',array('value'=>$date,'label'=>"Fecha",'class'=>'filter_input','style'=>'width:120px'))
	?></td>
	<td><?php 
		echo $form->input('hipodrome_id',array('options'=>$hipodromes,'value'=>$hipodrome_id,'empty'=>array(0 => 'Seleccione'),'label'=>"Hipodromo",'class'=>'filter_input'))
	?></td>
	<td><?php 
		echo $form->input('ended',array('options'=>array(1=>'Finalizadas',2 => 'Todas'),'value'=>$ended,'empty'=>array(0=>'Activas'),'label'=>"Estado",'class'=>'filter_input'))
	?></td>
	<td><?php echo $form->button('Filtrar',array('id'=>'filt')) ?></td>
</tr>
</table>
<div class="actions">
	<ul>
		<li><?php echo $html->link("Agregar Carreras", array('action'=>'add')); ?></li>
		<li><?php echo $html->link("Resumen Carreras", array('action'=>'view')); ?></li>
		<li><?php echo $html->link("Premios Pick", array('controller'=>'pick_results','action'=>'pick_prices')); ?></li>
	</ul>
</div>
<p>
<?php
echo $paginator->counter(array(
'format' => "Pagina %page% de %pages%, mostrando %current% registros de %count% totales, empezando en %start%, terminando en %end%"));
?></p>
<table cellpadding="0" cellspacing="0" border="1" class="tablegenerals">
<tr>
	<th><?php echo $paginator->sort('ID','id');?></th>
	<th><?php echo $paginator->sort('Fecha','race_date');?></th>
	<th><?php echo $paginator->sort('H. Carr.','race_time');?></th>
	<th><?php echo $paginator->sort('H. Local','local_time');?></th>
	<th><?php echo $paginator->sort('Hipodromo','hipodrome_id');?></th>
	<th><?php echo $paginator->sort('Numero','number');?></th>
	<th class="actions">Acciones</th>
</tr>
<?php
$i = 0;
foreach ($races as $race):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
    
    $started = '';
    
    if (strtotime(date('H:i:s')) >= strtotime($race['Race']['local_time'])) {
        $started = " class='race-started'";
    }
    
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $race['Race']['id'] ?>
		</td>
		<td>
			<?php echo $dtime->date_spa_mon_abr($race['Race']['race_date']) ?>
		</td>
		<td<?= $started ?>>
			<?php echo $dtime->time_to_human($race['Race']['race_time']) ?>
		</td>
        <td<?= $started ?>>
			<?php echo $dtime->time_to_human($race['Race']['local_time']) ?>
		</td>
		<td>
			<?php echo $race['Hipodrome']['name']; ?>
		</td>
		<td>
			<?php echo $race['Race']['number']."°"; ?>
		</td>
		<td class="act_each">
			<?php 
			$hab = " style='display:none' ";
			$des = "";	
			$hors_add = "";
			$hors_edit = " style='display:none' ";	
			$res_new = "";
			$res_edit = " style='display:none' ";	
			if($race['Race']['enable'] == 0){
				$hab = "";
				$des = " style='display:none' ";
			}
			if(in_array($race['Race']['id'],$horses)){
				$hors_add = " style='display:none' ";
				$hors_edit = "";
			}
			if($race['Race']['ended'] == 1){
				$res_new = " style='display:none' ";
				$res_edit = "";	
			}	
			?>
			<button <?php echo $hab ?>class="to_redir" href="<?php echo $html->url(array("controller"=>"races","action"=>"set_enable", $race['Race']['id'],1)) ?>">
				Habilitar
			</button>
			<button <?php echo $des ?>class="to_redir" href="<?php echo $html->url(array("controller"=>"races","action"=>"set_enable", $race['Race']['id'],0)) ?>">
				Deshabilitar
			</button>
			<button class="to_modal" href="<?php echo $html->url(array("action"=>"edit",$race['Race']['id'])) ?>">
				Editar
			</button>
			<button <?php echo $hors_add ?>class="to_modal" href="<?php echo $html->url(array("controller"=>"horses","action"=>"add",$race['Race']['id'])) ?>">
				Agregar Caballos
			</button>
			<button <?php echo $hors_edit ?>class="to_modal" href="<?php echo $html->url(array("controller"=>"horses","action"=>"details",$race['Race']['id'])) ?>">
				Editar Caballos
			</button>
			<button <?php echo $res_new ?>class="to_redir" href="<?php echo $html->url(array("controller"=>"results","action"=>"set",$race['Race']['id'])) ?>">
				Colocar Resultados
			</button>
			<button <?php echo $res_edit ?>class="to_redir" href="<?php echo $html->url(array("controller"=>"results","action"=>"set",$race['Race']['id'])) ?>">
				Editar Resultados
			</button>
			<button class="to_redir" href="<?php echo $html->url(array("controller"=>"races","action"=>"restrict",$race['Race']['id'])) ?>">
				Restricciones
			</button>
			<button class="to_redir" href="<?php echo $html->url(array("action"=>"delete",$race['Race']['id'])) ?>">
				Borrar
			</button>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array('url'=>array('action'=>'index',$date,$hipodrome_id,$ended)), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers(array('url'=>array('action'=>'index',$date,$hipodrome_id,$ended))); ?>
	<?php echo $paginator->next(__('next', true).' >>', array('url'=>array('action'=>'index',$date,$hipodrome_id,$ended)), null, array('class'=>'disabled'));?>
</div>