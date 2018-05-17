<style>
.number_race{
	width: 30px;
	font-size: 130%;
}
.name_horse{
	width: 160px;
	font-size: 110%;
}
.del{
    font-size: 8pt;
}
</style>
<script>
$(function() {
	$('.open_panel').click(function() {
		$('#panel_look').html('<?php echo $html->image("loading.gif")?>');
		$('#panel_look').load($(this).attr("href"));
		$('#panel_look').dialog('open');
		return false;
	});
	
	$(".del").click(function(){
		var url_act = $(this).attr("href");
		location = url_act + "/" + $(this).attr('id');
		return false;
	});
});
</script>
<div class="horses index">
<?php 
echo $form->create('Horse',array('action'=>'details'));
?>
<table cellpadding="0" cellspacing="0" class="table-total" style="padding: 1px">
<tr>
	<th>Numero</th>
	<th>Nombre</th>
	<th>Hab.</th>
	<th>Borrar</th>
</tr>
<?php
$i = 0;
foreach ($horses as $horse):
	$class = null;
	if ($i % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td><?php
			echo $form->input("Horse.$i.id.",array('value' => $horse['Horse']['id']));
			echo $form->input("Horse.$i.race_id.",array(
                'value' => $horse['Horse']['race_id'],'type'=>'hidden'));
			echo $form->input("Horse.$i.number",array(
                'value' => $horse['Horse']['number'],'class'=>'number_race',
                'label' => false, 'div' => false)); 
			?>
		</td>
		<td><?php 
			echo $form->input("Horse.$i.name",array(
                'value' => $horse['Horse']['name'],'class'=>'name_horse',
                'label' => false, 'div' => false)); 
			?>
		</td>
        <td>
			<?php
			$checked = true;
			if($horse['Horse']['enable'] == 0)
				$checked = false;			
			
			echo $form->input("Horse.$i.enable",array('checked' => $checked,
                'label' => false, 'div' => false));
			?>
		</td>
		<td style="color: blue">			
			<?php
            echo $html->link('Borrar',array("action"=>"delete",$horse['Horse']['id']));
            ?>
		</td>
	</tr>
<?php $i++; endforeach; ?>
</table>
<?php 
	echo $form->end();
 	echo $html->link("Agregar Caballos", array('action'=>'add',$race_id),array('class'=>'open_panel')); 
 ?>
</div>