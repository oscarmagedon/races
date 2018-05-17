<?php
/**
 * campos oligatorios:
 * 		Nombre referencial de centro
 * 		nombre, login y pass del Admin y de cada taquilla que se agregue (class)
 * Validar en modelo el usuario unico por jquery pa no tener q recargar la vista de nuevo
 * Copypastear en el controller la  preg y resp del admin pa sus taquillas
 * en Editar si habra mas detalle de estas cosas pa actualizar
 * 
 */
?>
<script>
var i = 1;
$(function(){
	$("#add_taquilla").button({
		icons: {
        	primary: "ui-icon-circle-plus"
    	}
    }).click(function(){
    	i = i + 1;
    	to_append(i);
    	return false;
    });
    
    $("#ProfileCreateAllForm").submit(function(){
		if($("#CenterName").val() == ""){
			alert("El nombre de centro es obligatorio");
			return false;
		}
		
		if($("#Profs0ProfileName").val() == ""){
			alert("El nombre de administrador es obligatorio");
			return false;
		}
		
		if($("#Profs0UserUsername").val() == ""){
			alert("El usuario de administrador es obligatorio");
			return false;
		}
		
    });
	
});
function to_append(i){
	var toadd= "<tr><td>\
			<input type='hidden' id='Profs"+ i +"UserRoleId' value='3' name='data[Profs]["+ i +"][User][role_id]'>\
			<input type='text' id='Profs"+ i +"ProfileName' name='data[Profs]["+ i +"][Profile][name]'></td>\
			<td><input type='text' id='Profs"+ i +"UserUsername' name='data[Profs]["+ i +"][User][username]'></td>\
			<td><input type='password' id='Profs"+ i +"UserPassword' name='data[Profs]["+ i +"][User][password]' value='123456'></td></tr>";
	$("#mytable").append(toadd);
}
</script>
<style>
#add_taquilla{
	font-size: 80%;
	float:right;
	margin-right: 15px;
}
.obli{
	color: Red;
	font-size: 80%;
}
</style>
<div class="centers form" style="background-color:#FFF; height:100%; width: 600px">
<?php echo $form->create('Profile',array('action'=>'create_all'));?>
	<table border="1" cellpadding="1" cellspacing="0" id="mytable">
		<tr>
			<th colspan="3">
				Datos Generales de Centro
			</th>
		</tr>
		<tr>
			<td colspan="3"><?php echo $form->input('Center.name',array('label'=>"Nombre de Referencia <span class='obli'>(*)</span>")) ?></td>
		</tr>
		<tr>
			<td><?php echo $form->input('Center.commercial_name',array('label'=>'Nombre Comercial')) ?></td>
			<td><?php echo $form->input('Center.owner',array('label'=>'Propietario')) ?></td>
			<td><?php echo $form->input('Center.phone_number',array('label'=>'Telefono')); ?></td>	
		</tr>
		<tr>
			<td><?php echo $form->input('Center.city',array('label'=>'Ciudad')) ?></td>
			<td><?php echo $form->input('Center.address',array('label'=>'Direccion')); ?></td>
			<td><?php echo $form->input('Center.email',array('label'=>'Email')); ?></td>
		</tr>
		<tr>
			<th colspan="3">Administrador del Centro</th>
		</tr>
		<tr>
			<td><?php 
				echo $form->input('Profs.0.User.role_id',array('type'=>'hidden','value'=>2));
				echo $form->input('Profs.0.Profile.name',array('label'=>"Nombre <span class='obli'>(*)</span>")) ?>
			</td>
			<td><?php echo $form->input('Profs.0.User.username',array('label'=>"Usuario <span class='obli'>(*)</span>")) ?></td>	
			<td><?php echo $form->input('Profs.0.User.password',array('value'=>123456,'type'=>'password')) ?></td>
		</tr>
		<tr>
			<th colspan="2">Taquilla (s)</th>
			<th>
				<button id="add_taquilla">Agregar Otra</button>
			</th>
		</tr>
		<tr>
			<th>Nombre</th>
			<th>Usuario</th>
			<th>Password</th>
		</tr>
		<tr>
			<td>
				<?php 
				echo $form->input('Profs.1.User.role_id',array('type'=>'hidden','value'=>3));
				echo $form->input('Profs.1.Profile.name',array('label'=>false,'div'=>false)) 
				?>
			</td>
			<td><?php echo $form->input('Profs.1.User.username',array('label'=>false,'div'=>false)) ?></td>	
			<td><?php echo $form->input('Profs.1.User.password',array('label'=>false,'div'=>false,'value'=>123456,'type'=>'password')) ?></td>
		</tr>
	</table>
<?php echo $form->end('Guardar Todo');?>
</div>