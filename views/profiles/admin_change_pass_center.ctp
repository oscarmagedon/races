<script>
$(function(){	
	$("#ProfileChangePassCenterForm").submit(function() {
		if($('#ProfileNewPass').val() == "" || $('#ProfileConfPass').val() == ""){
			alert('Debe llenar TODOS los campos');
			return false;
		}
		
		if($('#ProfileNewPass').val() != $('#ProfileConfPass').val()){
			alert('El password debe ser igual en ambos campos');
			return false;
		}
    });
});
</script>
<div class="profiles form">
	<?php 
	echo $form->create('Profile',array('action'=>'change_pass_center'));
	
	echo $form->input('user_id',array('value'=>$user_id,'type'=>'hidden'));
	
	echo $form->input('new_pass',array('type'=>'password','label' =>'Escriba nuevo password:'));
	
	echo $form->input('conf_pass',array('type'=>'password','label' =>'Confirme nuevo password:'));
	
	echo $form->end()
	?>
</div>