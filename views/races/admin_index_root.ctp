<script>
var postTimeUrl = '<?php echo $html->url(array('action'=>'set_post_time')) ?>'; 
$(function() { 
	$("#date").attr('readonly',true);
	$("#date").datepicker({dateFormat:"yy-mm-dd"});
	
	$("#filt").click(function(){
		var filt_url = '<?php echo $html->url(array("action"=>"index_root")) ?>'
		var date = $("#date").val();
		var hipo = $("#hipodrome_id").val();
		var end = $("#ended option:selected").val();
		
		location = filt_url + "/" + date + "/" + hipo + "/" + end;
	});	
	
	$("#panel_look").dialog({
		autoOpen: false,
		bgiframe: true,		
		modal: true,
		width: 600,
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
	
    $('.active-btns button').button({
        icons: {
			primary: "ui-icon-circle-minus"
		},
		text: false
    }).next().button({
	    icons: {
			primary: "ui-icon-circle-plus"
		},
		text: false
	});
    
	$(".act_each button").button({
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
			primary: "ui-icon-circle-close"
		},
		text: false
	});
    
    $(".save-ptime").button({
	    icons: {
			primary: "ui-icon-disk"
		},
		text: false
	}).click(function () {
        var ptime  = $(this).parents('td').find('.postval').val(),
            raceId = $(this).parents('td').find('#raceval').val();
        
        location = postTimeUrl +  '/' + raceId + '/' + ptime;
        //alert(ptime);
    });
    
    //REFRESH
    setTimeout(function (){
        location.reload();
    },180000);
});	
</script>

<style>
.postval{
    width: 28px;
}
.ptnozero {
    color: #8000FF;
    font-weight: bold;
}
.save-ptime{
    width: 40px;
}
</style>
<div class="races index">
<h2>Carreras del ROOT</h2>
<table style="width:80%">
<tr>
	<th>Filtrar Por:</th>
	<td><?php 
		echo $form->input('date',array('value'=>$date,'label'=>"Fecha",'class'=>'filter_input','style'=>'width:120px'))
	?></td>
	<td><?php 
		echo $form->input('hipodrome_id',array('options'=>$hipodromes,'value'=>$htrackid,'empty'=>array(0 => 'Seleccione'),'label'=>"Hipodromo",'class'=>'filter_input'))
	?></td>
	<td><?php 
		echo $form->input('ended',array('options'=>array(1=>'Finalizadas',2 => 'Todas'),'value'=>$ended,'empty'=>array(0=>'Activas'),'label'=>"Estado",'class'=>'filter_input'))
	?></td>
	<td><?php echo $form->button('Filtrar',array('id'=>'filt')) ?></td>
</tr>
</table>
<div class="srv-mess">
    
</div>
<p>
<?php
echo $paginator->counter(array(
'format' => "Pagina %page% de %pages%, mostrando %current% registros de %count% totales, empezando en %start%, terminando en %end%"));
?></p>
<table cellpadding="0" cellspacing="0" border="1" class="table-total">
<tr>
	
    <th><?php echo $paginator->sort('ID','id');?></th>
	<th><?php echo $paginator->sort('Fecha','race_date');?></th>
	<th><?php echo $paginator->sort('Hipodromo','Hipodrome.name');?></th>
    <th><?php echo $paginator->sort('GMT','Hipodrome.htgmt');?></th>
    <th><?php echo $paginator->sort('H. Carr.','race_time');?></th>
    <th><?php echo $paginator->sort('H. Local','local_time');?></th>
	<th><?php echo $paginator->sort('Post Time','post_time');?></th>
	<th><?php echo $paginator->sort('Numero','number');?></th>
    <th><?php echo $paginator->sort('Activa','Race.enable');?></th>
    <th class="actions">Acciones</th>
</tr>
<?php
foreach ($races as $race):
    $hab       = " style='display:none' ";
    $des       = "";	
    $hors_add  = "";
    $hors_edit = " style='display:none' ";	
    $res_new   = "";
    $res_edit  = " style='display:none' ";	
    
    if ($race['Race']['enable'] == 0) {
        $hab = "";
        $des = " style='display:none' ";
    }
    
    if ( in_array($race['Race']['id'],$horses) ) {
        $hors_add = " style='display:none' ";
        $hors_edit = "";
    }
    
    if ($race['Race']['ended'] == 1) {
        $res_new = " style='display:none' ";
        $res_edit = "";	
    }
?>
	<tr>
		<td>
			<?php echo $race['Race']['id'] ?>
		</td>
		<td>
			<?php echo $dtime->date_spa_mon_abr($race['Race']['race_date']) ?>
		</td>
		<td>
			<?php echo $race['Hipodrome']['name']; ?>
		</td>
        <td>
			<?php echo $race['Hipodrome']['htgmt']; ?>
		</td>
		<td>
			<?php 
            echo $dtime->time_to_human($race['Race']['race_time']);
            
            if ($race['Race']['post_time'] != 0) {
                echo 
                "<br /><span style='color:#8000FF'>".
                $dtime->addPostMins($race['Race']['race_time'],$race['Race']['post_time']).
                '</span>';
            }
            
            
            ?>
		</td>
        <td>
			<?php 
            echo $dtime->time_to_human($race['Race']['local_time']);
            if ($race['Race']['post_time'] != 0) {
                echo 
                "<br /><span style='color:#8000FF'>".
                $dtime->addPostMins($race['Race']['local_time'],$race['Race']['post_time']).
                '</span>';
            }
            ?>
		</td>
        <td>
			<?php //echo $race['Race']['post_time'] 
            $ptnoz = "";
            if ($race['Race']['post_time'] != 0) {
                $ptnoz = " ptnozero";
            }
            echo $form->input('ptime',array('value'=>$race['Race']['post_time'],
                                'label' => false,'class'=>"postval$ptnoz",'div'=>false));
            echo $form->input('race_id',array('value'=>$race['Race']['id'],
                                'type' => 'hidden','id' => 'raceval'));
            ?>
            <button class="save-ptime">Guardar</button>
		</td>
        <td>
			<?php echo $race['Race']['number']."°"; ?>
		</td>
        <td class="active-btns">
            <button <?php echo $hab ?>class="to_redir ui-state-error" href="<?php echo $html->url(array("controller"=>"races","action"=>"set_enable", $race['Race']['id'],1)) ?>">
				Habilitar
			</button>
			<button <?php echo $des ?>class="to_redir" href="<?php echo $html->url(array("controller"=>"races","action"=>"set_enable", $race['Race']['id'],0)) ?>">
				Deshabilitar
			</button>
            <?php
            if ($race['Race']['close_time'] != null) {
                echo 
                "<br /><span style='color:#800; padding-top: 8px'>CT: ".
                $dtime->time_to_human_exact($race['Race']['close_time']).
                '</span>';
            }
            ?>
        </td>
		<td class="act_each">
			<button class="to_modal" href="<?php echo $html->url(array("action"=>"edit",$race['Race']['id'])) ?>">
				Editar
			</button>
			<button <?php echo $hors_add ?>class="to_modal" href="<?php echo $html->url(array("controller"=>"horses","action"=>"add",$race['Race']['id'])) ?>">
				Agregar Caballos
			</button>
			<button <?php echo $hors_edit ?>class="to_modal" href="<?php echo $html->url(array("controller"=>"horses","action"=>"details",$race['Race']['id'])) ?>">
				Editar Caballos
			</button>
			<button <?php echo $res_new ?>class="to_redir" href="<?php echo $html->url(array("controller"=>"results","action"=>"rootset",$race['Race']['id'])) ?>">
				Colocar Resultados
			</button>
			<button <?php echo $res_edit ?>class="to_redir" href="<?php echo $html->url(array("controller"=>"results","action"=>"rootset",$race['Race']['id'])) ?>">
				Editar Resultados
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
	<?php echo $paginator->prev('<< '.__('previous', true), array('url'=>array('action'=>'index_root',$date,$htrackid,$ended)), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers(array('url'=>array('action'=>'index_root',$date,$htrackid,$ended))); ?>
	<?php echo $paginator->next(__('next', true).' >>', array('url'=>array('action'=>'index_root',$date,$htrackid,$ended)), null, array('class'=>'disabled'));?>
</div>