<script>
$(function(){
	$("#ProfileAddForm").submit(function() {
		if($('#UserUsername').val() == ""){
			alert('Debe llenar TODOS los campos de Usuario.');
			return false;
		}
    });
});
</script>
<?php
echo $form->create('Profile');
echo $form->input('User.id');
echo $form->input('id'); 	
?>
<div class="modalform-panel">
	<?php 
	echo $form->input('name',array('label'=>'Nombre'));
	echo $form->input('phone_number',array('label'=>'Telefono'));
	?>
</div>
<div class="modalform-panel">	
	<?php
	echo $form->input('User.username',array('label'=>'Usuario'));
	echo $form->input('User.role_id',array('label'=>'Grado','options'=>$roles));
	?>
</div>
<?php
echo $form->end();
?>