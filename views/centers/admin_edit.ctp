<div class="centers form">
	<?php 
	echo $form->create('Center');
	echo $form->input('id'); 
	?>
	<div class="modalform-panel">
		<?php 
		echo $form->input('name',array('label'=>'Nombre'));
		echo $form->input('city',array('label'=>'Ciudad'));
		echo $form->input('email',array('label'=>'Email')); 
		?>
	</div>
	<div class="modalform-panel">
		<?php
		echo $form->input('commercial_name',array('label'=>'Nombre Comercial'));
		echo $form->input('address',array('label'=>'Direccion'));
		echo $form->input('rif',array('label'=>'R.I.F.'));
		?>
	</div>
	<div class="modalform-panel">
		<?php
		echo $form->input('owner',array('label'=>'Propietario'));
		echo $form->input('phone_number',array('label'=>'Telefono'));
		echo $form->input('nro_lic',array('label'=>'Nro. Licencia'));
		?>
	</div>
	<?php echo $form->end() ?>		
</div>
