<?php
echo $form->create('Account',array('style'=>'width:100%'));
echo $form->input('profile_id',array('value'=>$pid,'type'=>'hidden'));
?>
<table border="1">
	<tr>
		<td><?php 
            //echo $form->input('add',array('label'=>'Recarga?','checked'=>true))
            echo $form->input('title',array('label'=>'Tipo'))
            ?>
        </td>
		<td><?php echo $form->input('amount',array('label'=>'Monto','style'=>'width:100px')) ?></td>
	</tr>
	<tr>
		<td colspan="2">
			<?php echo $form->input('metainf',array('label'=>'Detalles')) ?>
		</td>
	</tr>
</table>
<?php
echo $form->end('Agregar');
?>