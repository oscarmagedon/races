<div class="profiles form">
	<h2>Mi Perfil</h2>
	<table cellpadding="1" border="1" cellspacing="0" style="width: 500px; display:none;">
		<tr>
			<th>Centro</th>
			<td><?php echo $profile['Center']['name'] ?></td>
			<th>Usuario</th>
			<td><?php echo $profile['User']['username'] ?></td>
		</tr>
		<tr>
			<th>Nombre</th>
			<td><?php echo $profile['Profile']['name'] ?></td>
			<th>Telefono</th>
			<td><?php echo $profile['Profile']['phone_number'] ?></td>
		</tr>
		<tr>
			<th>Creacion</th>
			<td><?php echo $profile['User']['created'] ?></td>
      <th>Email</th>
			<td><?php echo $profile['User']['email'] ?></td>
		</tr>
	</table>
	
    <h2>Cambiar Password</h2>
    
  <?php 
	echo $form->create('Profile',array('action'=>'viewonl'));
	
	echo $form->input('user_id',array('value'=>$profile['User']['id'],'type'=>'hidden'));
    
  echo $form->input('username',array('value'=>$profile['User']['username'],'readonly' => true));
	
	echo $form->input('new_pass',array('type'=>'password','label' =>'Escriba nuevo password:'));
	
	echo $form->input('conf_pass',array('type'=>'password','label' =>'Confirme nuevo password:'));
	
	echo $form->end('CAMBIAR');
	?>
</div>