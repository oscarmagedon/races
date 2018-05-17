<div class="hipodromes form" style="background-color:#FFF; height:100%;">
<?php echo $form->create('Hipodrome');?>
	<table>
		<tr>
			<td>Nombre</td>
			<td><?php echo $form->input('id');
			echo $form->input('name',array('label'=>false));?></td>
		</tr>
		<tr>
			<td>NICK</td>
			<td><?php
			echo $form->input('nick',array('label'=>false));?></td>
		</tr>
        <tr>
			<td>NICK TVG</td>
			<td><?php
			echo $form->input('tvgnick',array('label'=>false));?></td>
		</tr>
        <tr>
			<td>BOVADA</td>
			<td><?php
			echo $form->input('bovada',array('label'=>false));?></td>
		</tr>
        <tr>
			<td>BOVADA 2</td>
			<td><?php
			echo $form->input('bovalt',array('label'=>false));?></td>
		</tr>
        <tr>
			<td>GMT</td>
			<td><?php
			echo $form->input('htgmt',array('label'=>false));?></td>
		</tr>
		<tr>
			<td>Nacional</td>
			<td><?php echo $form->input('national',array('label'=>false)); ?></td>
		</tr>
        <tr>
			<td>Clase</td>
			<td><?php echo $form->input('class',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td>Disponible</td>
			<td><?php echo $form->input('enable',array('label'=>false)); ?></td>
		</tr>
	</table>
<?php echo $form->end('Guardar');?>
</div>
