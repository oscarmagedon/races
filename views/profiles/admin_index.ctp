<script>
$(function() {
	$("#filt").click(function(){
		var filt_url = '<?php echo $html->url(array("controller"=>"profiles","action"=>"index/")) ?>'
		var role_id = $("#role_id").val();
		var enable = $("#enable").val();
		var center_id = $("#center_id").val();
		var user_like = "";
		if($("#user_like").val() != "")
			user_like = $("#user_like").val();
		
		location = filt_url + "/" + role_id + "/" + enable + "/" + center_id + "/" + user_like;
	});	
	
	$("#panel_look").dialog({
		autoOpen: false,
		bgiframe: true,		
		modal: true,
		height: 260,
		width: 500,
		resizable: true,
		buttons : {
			'Guardar' : function(){
				$(this).find('form').submit();
			}
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
			primary: "ui-icon-key"
		},
		text: false
	});
	
	$(".act_center button").button({
	    icons: {
			primary: "ui-icon-clipboard"
		},
		text: false
	}).next().button({
	    icons: {
			primary: "ui-icon-person"
		},
		text: false
	});
});	
</script>
<style>
.act_each{margin-top: 3px; margin-bottom: 3px;}	
</style>
<div class="users index">
<h2>Usuarios</h2>
<table style="width:auto">
<tr>
	<td><?php 
		echo $form->input('role_id',array('options'=>$roles,'value'=>$role_id,'label'=>"Grados",'empty'=>array(0=>"Todos"),'class'=>'filter_input'))
	?></td>
	<td><?php 
		echo $form->input('enable',array('options'=>$enables,'value'=>$enable,'label'=>"Estado",'class'=>'filter_input'))
	?></td>
	<td><?php 
		echo $form->input('center_id',array('options'=>$centers,'empty'=>array(0 => "Todos"),'label'=>"Centro",'value'=>$center_id,'class'=>'filter_input'))
	?></td>
	<td><?php 
		echo $form->input('user_like',array('label'=>"Nombre de Usuario",'value'=>$user_like,'class'=>'filter_input','style'=>'width: 180px;'))
	?></td>
	<td><?php 
		echo $form->button("Filtrar",array('id'=>'filt','style'=>'font-size:110%')) 
	?></td>
</tr>
</table>
<p><?php
echo $paginator->counter(array(
'format' => "Pagina %page% de %pages%, mostrando %current% registros de %count% totales, empezando en %start%, terminando en %end%"));
?></p>
<table class="tablegenerals" cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('Nombre','Profile.name');?></th>
	<th><?php echo $paginator->sort('Usuario','username');?></th>
	<th><?php echo $paginator->sort('Centro','Center.name');?></th>
	<th><?php echo $paginator->sort('Grado','role_id');?></th>
	<th class="actions">Acciones Usuario</th>
	<th class="actions">Centro</th>
</tr>
<?php
$i = 0;
foreach ($profiles as $profile):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td class="tdnames">
			<?php echo $profile['Profile']['name']; ?>
		</td>		
		<td class="tdnames">
			<?php echo $profile['User']['username']; ?>
		</td>
		<td class="tdnames">
			<?php echo $profile['Center']['name']; ?>
		</td>
		<td class="tdnames">
			<?php echo $roles[$profile['User']['role_id']]; ?>
		</td>
		<td>
			<div class="act_each">		
			<?php
			// SI es taquilla u online no hago nada
			if($profile['User']['role_id'] <= 2){
			?>
					
				<?php 
				$act = " style='display:none' ";
				$des = "";	
				if($profile['User']['enable'] == 0){
					$act = "";
					$des = " style='display:none' ";
				}
				?>
				<button <?php echo $act ?>class="to_redir ui-state-error-text" href="<?php echo $html->url(array("controller"=>"users","action"=>"set_enable", $profile['Profile']['user_id'],1)) ?>">
					Activar
				</button>
				<button <?php echo $des ?>class="to_redir" href="<?php echo $html->url(array("controller"=>"users","action"=>"set_enable", $profile['Profile']['user_id'],0)) ?>">
					Desactivar
				</button>
				<button class="to_modal" href="<?php echo $html->url(array("action"=>"edit",$profile['Profile']['id'])) ?>">
					Editar
				</button>
				<button class="to_modal" href="<?php echo $html->url(array("action"=>"change_pass",$profile['Profile']['id'])) ?>">
					Cambiar Password
				</button>
			<?php
			} else echo "-";
			?>
			</div>
		</td>
		<td>
			<?php
			if($profile['User']['role_id'] == 2){
			?>
			<div class="act_center">				
				<button class="to_modal" href="<?php echo $html->url(array("controller"=>"centers","action"=>"edit", $profile['Profile']['center_id'])) ?>">
					Datos Generales del Centro
				</button>
				<button class="to_modal" href="<?php echo $html->url(array("action"=>"add", $profile['Profile']['center_id'])) ?>">
					Agregar Usuario
				</button>
			</div>
			<?php
			}
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link("Crear Nuevo Centro",array('action'=>'create_all'))?></li>
	</ul>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array('url'=>array("action"=>"index",$role_id,$enable,$center_id,$user_like)), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers(array('url'=>array("action"=>"index",$role_id,$enable,$center_id,$user_like)));?>
	<?php echo $paginator->next(__('next', true).' >>', array('url'=>array("action"=>"index",$role_id,$enable,$center_id,$user_like)), null, array('class'=>'disabled'));?>
</div>