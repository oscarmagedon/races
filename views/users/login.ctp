<?php
if(empty($authUser)){
	echo $form->create('User',array('action'=>"login"));
	echo $form->input('username',array('label'=>'Usuario'));
	echo $form->input('password',array('label'=>'Contrasena'));
	echo $form->submit("Entrar");
	echo $form->end(); 
}else{
	echo "<h2>Usuario ".$authUser['profile_name']." en sesion.</h2>";header( "refresh:3; url=http://twihorses.com/horses/admin/tickets/add" );
}
?>
