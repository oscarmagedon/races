<style>
label{
	float: left;
	padding-left: 5px;
	color: Black;
}	
</style>
<div>
	<h2>Asignar Carreras a Centros</h2>
	<?php 
    echo $form->create('Race',array('action'=>'assign'));
    echo $form->input('date',array(
                        'value' => $date,'label'=>"Fecha",
                        'class' => 'filter_input','style'=>'width:120px'))
    ?>
	<div style="width: 350px; float: left; clear: none;">
	<table border="1">
	<?php
	foreach($races as $r){
		$rid = $r['Hipodrome']['id'];
		?>
		<tr>
			<th><?php 
			echo $r['Hipodrome']['name']." (".$r[0]['co']." carrs.)"; 
			?></th>
		</tr>
		<?php	
		foreach($centers as $cid => $c){				
		?>
			<tr>
				<td style="text-align: left; font-size:120%"><?php 
				if(!empty($assigned[$rid][$cid])){
					echo "<b>LISTO</b> $c";
				}else{
					echo $form->input("Race.races.$rid.$cid", array('type'=>'checkbox',
                                'value'=>1,'label'=>$c));	
				}				
				?></td>
			</tr>
		<?php	
		}
	}
	?>
	</table>	
	</div>
	<?php echo $form->end('guarda') ?>
</div>
<script>
var filtUrl = '<?php echo $html->url(array("action"=>"assign")) ?>';
        
$(function() {
	$("#RaceDate").attr('readonly',true)
            .datepicker({ dateFormat:"yy-mm-dd" })
            .change(function(){
                location = filtUrl + "/" + $(this).val();
            });
});	
</script>