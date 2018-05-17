<script>
$(function(){
	$("#ProfileAddForm").submit(function() {
		if($('#UserUsername').val() == "" || $('#UserPassword').val() == "" || $('#UserRepassword').val() == ""){
			alert('Debe llenar TODOS los campos de Usuario.');
			return false;
		}
		if($('#UserPassword').val() != $('#UserRepassword').val()){
			alert('El password debe ser igual en ambos campos');
			return false;
		}
    });
});
</script>
<div class="profiles form">
	<?php 
	echo $form->create('Profile',array('action'=>'add_online'));
	?>
	<div class="modalform-panel">
		<?php 
		echo $form->input('name',array('label'=>'Nombre'));
		echo $form->input('User.password',array('label'=>'Password'));
		echo $form->input('phone_number',array('label'=>'Telefono'));
		?>
	</div>
	<div class="modalform-panel">
		<?php
		echo $form->input('User.username',array('label'=>'Usuario'));
		//echo $form->input('User.role_id',array('options'=>$roles,'label'=>'Grado'));
		echo $form->input('User.repassword',array('type'=>'password','label'=>'Conf. Password'));
		?>
	</div>
	<?php 
	echo $form->end();
	?>
</div>