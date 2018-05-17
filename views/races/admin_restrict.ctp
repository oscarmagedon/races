<script>
$(function() {	
	$('.horse_check').hide();
	$('.ui_check').click(function(){
		var chk = $(this).parent().find('.horse_check');
		if(chk.attr('checked') != "checked"){
			chk.attr('checked',true);
			$(this).html('X');
		}else{
			chk.attr('checked',false);	
			$(this).html('');		
		}	
	});
	
});	
</script>
<style>
.ui_check{
	height:20px;
	width:20px;
	float: left;
	margin-left:8px;
	margin-top:3px;
	margin-bottom:3px;
	border: 1px solid #255E00;
	color: Red;
	font-size:12pt;
	font-weight: bold;
	cursor: pointer;
}
</style>
<div class="races index">
<h2 style="width: 100%">
	Restricciones de la <?php echo $race['Race']['number']?>&ordf; 
	Carrera del <?php echo $dtime->date_spa_mon_abr($race['Race']['race_date']) ?>
	de <?php echo $race['Hipodrome']['name']?>
</h2>
<?php
echo $form->create("Race",array("action"=>"restrict"));
echo $form->input('id',array('value'=>$race_id,'type'=>'hidden'));
?>
<table cellpadding="0" cellspacing="0" border="1" style="width:600px">
<tr>
	<th>Taquilla</th>
	<th>W,P,S</th>
	<th>WP,WS,WPS</th>
	<th>EXACTA</th>
	<th>TRIFC</th>
	<th>SUPRF</th>
	<th>PICKS</th>
</tr>
<?php
$j = 0;
foreach($profiles as $k => $p){
	$class = null;
	if ($j++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td> 
			<?php echo $p;?>
		</td>
		<?php
		for($i = 1; $i < 7; $i ++){
			$checked = false;
			
			if(!empty($restrict[$k]) && in_array($i, $restrict[$k]))
				$checked = true;
		?>
			<td>
				<div class="ui_check" title="Seleccionar"><?php if($checked) echo "X" ?></div>
				<?php
				
				
				echo $form->input("Conf.$k.$i",array(
					'value'=>1,'type'=>'checkbox','class'=>'horse_check','div'=>false,'label'=>false,'checked'=>$checked
				));
				?>
			</td>
		<?php	
		}
		?>
	</tr>
<?php 
}
?>
</table>
<?php
echo $form->end("Guardar Configuraciones");
?>
</div>