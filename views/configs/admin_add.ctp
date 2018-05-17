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
	$("#ConfigFrom").datepicker({dateFormat:"yy-mm-dd"});
	$("#ConfigUntil").datepicker({dateFormat:"yy-mm-dd"});
});
</script>
<div class="units form">
<?php echo $form->create('Config');?>
	<table>
		<tr>
			<td>
				Tipo: 
			</td>
			<td>
				<?php echo $form->input('config_type_id',array('options'=>$conf_types,'label'=>false)) ?>
			</td>
			<td>
				Taquillas: 
			</td>
			<td>
				<?php echo $form->input('profile_id',array('options'=>$profiles,'label'=>false)) ?>
			</td>
		</tr>
		<tr>
			<td>Desde: </td>
			<td><?php echo $form->input('from',array('type'=>'text','label'=>false)) ?></td>
			<td>Hasta: </td>
			<td><?php echo $form->input('until',array('type'=>'text','label'=>false)) ?></td>
		</tr>
		<tr>
			<td>Monto: </td>
			<td><?php echo $form->input('amount',array('label'=>false)); ?></td>
			<td>Actual: </td>
			<td><?php echo $form->input('actual',array('label'=>false)); ?></td>
		</tr>
	</table>
<?php echo $form->end();?>
</div>
