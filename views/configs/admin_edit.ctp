<style>
#RaceNumber{
	width:60px;
	font-size:130%;
}
#RaceRaceDate{
	width:100px;
}
</style>
<script type="text/javascript">
$(function(){
	$("label").hide();
	$("#ConfigFrom").datepicker({dateFormat:"yy-mm-dd"});
	$("#ConfigUntil").datepicker({dateFormat:"yy-mm-dd"});
	$("#ConfigActual").change(function(){
		if($(this).attr('checked') == false){
			$("#ConfigUntil").removeAttr('disabled');
			$("#ConfigUntil").datepicker();
		}else{
			$("#ConfigUntil").val('0000-00-00');
			$("#ConfigUntil").attr('disabled',true);
		}
	});
});
</script>
<div class="units form">
<?php echo $form->create('Config'); echo $form->input('id');?>
	<table>
		<tr>
			<td>Desde: </td>
			<td><?php echo $form->input('from',array('type'=>'text')); ?></td>
			<td>Hasta: </td>
			<td><?php 
				$disabled = false;
				if($this->data['Config']['actual'] == 1)
					$disabled = true;	
				echo $form->input('until',array('type'=>'text','disabled'=>$disabled)); 
				?>
			</td>
		</tr>
		<tr>
			<td>Monto: </td>
			<td><?php echo $form->input('amount'); ?></td>
			<td>Actual: </td>
			<td><?php echo $form->input('actual'); ?></td>
		</tr>
	</table>
<?php echo $form->end();?>
</div>