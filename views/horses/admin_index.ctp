<style>
    .no-btn-show{
        display: none;
    }
</style>
<div class="hipodromes index">
<h2>Hipodromos</h2>
<p style="clear: both">
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0" class="table-total">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
    <th><?php echo $paginator->sort('NOMBRE','name');?></th>
    <th><?php echo $paginator->sort('nick');?></th>
    <th><?php echo $paginator->sort('GMT','htgmt');?></th>
	<th><?php echo $paginator->sort('NAC/INT','national');?></th>
	<th class="actions">Acciones</th>
</tr>
<?php
//pr($hipodromes);

foreach ($hipodromes as $hipodrome):
	$discls = "";
    $enabtn = "no-btn-show";
    $disbtn = "";
    
    if($hipodrome['Hipodrome']['enable'] == 0) {
        $discls = " class='disable-row'";
        $enabtn = "";
        $disbtn = "no-btn-show";        
    }
?>
	<tr<?php echo $discls ?>>
		<td>
			<?php echo $hipodrome['Hipodrome']['id']; ?>
		</td>
        <td class="title-col">
			<?php echo $hipodrome['Hipodrome']['name']; ?>
		</td>
		<td>
			<?php echo $hipodrome['Hipodrome']['nick']; ?>
		</td>
		<td>
			<?php echo $hipodrome['Hipodrome']['htgmt']; ?>
		</td>
		<td>
			<?php if($hipodrome['Hipodrome']['national'] == 1) echo "NAC."; else echo "INTL." ?>
		</td>
		<td class="actions">
            <button class="redir-btn disable-btn <?= $disbtn ?>" href="<?php echo $html->url(array(
                'action'=>'enable',$hipodrome['Hipodrome']['id'],0)) ?>" >Deshabilitar</button>
			<button class="redir-btn enable-btn <?= $enabtn ?>" href="<?php echo $html->url(array(
                'action'=>'enable',$hipodrome['Hipodrome']['id'],1)) ?>" >Habilitar</button>
			<button class="open_panel edit-btn" href="<?php echo $html->url(array('action'=>'edit',$hipodrome['Hipodrome']['id'])) ?>" >Editar</button>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link("Agregar Hipódromo", array('action'=>'add'),array('class'=>'open_panel','title'=>"Agregar Hipódromo")); ?></li>
	</ul>
</div>
<script>
$(function() {
	$("#panel_look").dialog({
		autoOpen: false,
		bgiframe: true,		
		modal: true,
		height: 300,
		width: 400,
		resizable: true
	});
	
	$(".edit-btn").button({ icons: { primary: "ui-icon-pencil" }, text: false});
    
    $(".enable-btn").button({ icons: { primary: "ui-icon-plus" }, text: false});
    
    $(".disable-btn").button({ icons: { primary: "ui-icon-minus" }, text: false});
	
	$(".open_panel").click(function(){
		var myurl = $(this).attr('href');
		
		$('#panel_look').html('<?php echo $html->image("loading.gif")?>');
		$('#panel_look').dialog({title:"Editar"});
		$('#panel_look').load(myurl);
		$('#panel_look').dialog('open');
		
        return false;
	});
    
    $(".redir-btn").click( function () {
        location = $(this).attr('href');
    });

});	
</script>