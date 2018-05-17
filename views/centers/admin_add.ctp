<script>
$(function(){
	$("label").hide();
});
</script>
<div class="centers form" style="background-color:#FFF; height:100%">
<?php echo $form->create('Center');?>
	<table border="1" cellpadding="1" cellspacing="0">
		<tr>
			<th colspan="4">
				Datos Generales de Centro
			</th>
		</tr>
		<tr>
			<td colspan="2">Nombre</td>
			<td colspan="2"><?php echo $form->input('name'); ?></td>
		</tr>
		<tr>
			<td>Propietario</td>
			<td><?php echo $form->input('owner'); ?></td>
			<td>Nombre Comercial</td>
			<td><?php echo $form->input('commercial_name'); ?></td>
		</tr>
		<tr>
			<td>Ciudad</td>
			<td><?php echo $form->input('city'); ?></td>
			<td>Direccion</td>
			<td><?php echo $form->input('address'); ?></td>
		</tr>
		<tr>
			<td>Telefono</td>
			<td><?php echo $form->input('phone_number'); ?></td>
			<td>Email</td>
			<td><?php echo $form->input('email'); ?></td>
		</tr>
	</table>
<?php echo $form->end('Submit');?>
</div>