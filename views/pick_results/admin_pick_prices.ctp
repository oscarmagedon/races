<script>
$(function() {
	$("#PickResultDate").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"}).change(function(){
		location = '<?php echo $html->url(array("action"=>"pick_prices")) ?>' + "/" + $(this).val();
	});
});	
</script>
<style>
.number_race{
	width:60px;
	font-size:130%;
}
</style>
<div>
<h2>Premios para PICK</h2>
<?php echo $form->create('PickResult',array('action'=>'pick_prices')) ?>
<table style="width: 800px">
	<tr>
		<th><?php 
		echo $form->input('date',array('value'=>$date,'label'=>"Fecha",'class'=>'filter_input','style'=>'width:120px','type'=>'text'));
		?></th>
		<th> 2 </th>
		<th> 3 </th>
		<th> 4 </th>
		<th> 6 </th>
		<th> 9 </th>
	</tr>
	<?php 
	//pr($pick_results);
	foreach ($hipodromes as $k => $h) {
		$p2 = ""; $p3 = ""; $p4 = ""; $p6 = ""; $p9 = ""; 
		$idp2 = ""; $idp3 = ""; $idp4 = ""; $idp6 = ""; $idp9 = "";
		if(!empty($pick_results[$k][2])){
			$p2 = $pick_results[$k][2]['prize']; 
			$idp2 = $form->input("Pick.$k.2.id",array('value'=>$pick_results[$k][2]['id'],'type'=>'hidden'));
		}
		
		if(!empty($pick_results[$k][3])){
			$p3 = $pick_results[$k][3]['prize']; 
			$idp3 = $form->input("Pick.$k.3.id",array('value'=>$pick_results[$k][3]['id'],'type'=>'hidden')); 
		}
		
		if(!empty($pick_results[$k][4])){
			$p4 = $pick_results[$k][4]['prize']; 
			$idp4 = $form->input("Pick.$k.4.id",array('value'=>$pick_results[$k][4]['id'],'type'=>'hidden')); 
		}
		
		if(!empty($pick_results[$k][6])){
			$p6 = $pick_results[$k][6]['prize']; 
			$idp6 = $form->input("Pick.$k.6.id",array('value'=>$pick_results[$k][6]['id'],'type'=>'hidden'));
		}
		
		if(!empty($pick_results[$k][9])){	
			$p9 = $pick_results[$k][9]['prize'];
			$idp9 = $form->input("Pick.$k.9.id",array('value'=>$pick_results[$k][9]['id'],'type'=>'hidden'));
		}
	?>
	<tr>
		<td><?php echo $h ?></td>
		<td><?php 
		echo $idp2;
		echo $form->input("Pick.$k.2.prize",array('value'=>$p2,'label'=>false,'div'=>false,'class'=>'number_race')) 
		?></td>
		<td><?php 
		echo $idp3;
		echo $form->input("Pick.$k.3.prize",array('value'=>$p3,'label'=>false,'div'=>false,'class'=>'number_race')) 
		?></td>
		<td><?php 
		echo $idp4;
		echo $form->input("Pick.$k.4.prize",array('value'=>$p4,'label'=>false,'div'=>false,'class'=>'number_race')) 
		?></td>
		<td><?php 
		echo $idp6;
		echo $form->input("Pick.$k.6.prize",array('value'=>$p6,'label'=>false,'div'=>false,'class'=>'number_race')) 
		?></td>
		<td><?php 
		echo $idp9;
		echo $form->input("Pick.$k.9.prize",array('value'=>$p9,'label'=>false,'div'=>false,'class'=>'number_race')) 
		?></td>
	</tr>
	<?php
	}
	?>
</table>
<?php echo $form->end("Guardar") ?>
</div>