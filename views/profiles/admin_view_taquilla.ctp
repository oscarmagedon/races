<div class="profiles form">
	<h2>Mi Perfil</h2>
	<table cellpadding="1" border="1" cellspacing="0" style="width: 500px">
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
			<td><?php echo  $profile['User']['email'] ?></td>
		</tr>
	</table>
	<h2>Restricciones para hoy</h2>
	<table cellpadding="1" border="1" cellspacing="0" style="width: 500px">
	<?php 
	foreach ($restrictions as $hipk => $hipo) {
	?>
		<tr>
			<th colspan="2"><?php echo $hipk ?></th>
		</tr>	
		<?php
		foreach($hipo as $rid => $race){
		?>
		<tr>
		<td><?php echo $rid?>&ordm; </td>
		<td style="color: Red; font-weight: bold">
		<?php
			foreach($race as $r){
				switch ($r) {
					case 1:
						echo "WIN, PLACE, SHOW";
						break;
					case 2:
						echo "WP, WS, WPS";
						break;
					case 3:
						echo "EXACTA";
						break;
					case 4:
						echo "TRIFECTA";
						break;
					case 5:
						echo "SUPERFECTA";
						break;
					case 6:
						echo "PICKS";
						break;
				}
				echo "<br />";
			}
		?>
		</td>
		</tr>
		<?php
		}
	}
	?>	
	</table>
	
</div>