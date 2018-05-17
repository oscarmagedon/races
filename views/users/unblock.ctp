<?php 
echo $form->create('User',array('action'=>'unblock'));
?>
<div style="width:30%">
<table border="1">
	<tr>
		<td colspan="2"><?php echo __('Username');?></td>
		<td colspan="2"><?php echo $form->input('username',array('label'=>'')); ?></td>
	</tr>
	<?php 
	if($secretQuestion != ""){
	?>
		<tr>
			<td align="right"><?php echo __('Secret Question');?></td>
			<td><b><?php echo $secretQuestion; ?></b></td>
			<td align="right"><?php echo __('Secret Answer');?></td>
			<td><?php echo $form->input('answer',array('label'=>'')); ?></td>
		</tr>
	<?php
	}else{
		if($chk == 1){
		?>
		<tr>
			<td align="right"colspan="4"><?php echo "Ud. no tiene pregunta/respuesta secreta asignada" ?></td>
		</tr>
		<?php
		}	
	}
	?>
</table>
</div>
<?php echo $form->end("Enviar"); ?>