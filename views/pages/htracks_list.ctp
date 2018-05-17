<h2>Hipodromos con ID</h2>

<div style="width:400px">
	<p>
		Aqui en el link esta el ID de cada hipodromo en el sistema,
		se recibiria algo como: 
		<br />
		<b>tickets/add/[hipodromo-id]/[numero-carrera]</b>	
	</p>
	<p>
		El numero de carrera en cada link es aleatorio entre el 
		1 y 3, pero el buscador funciona siempre y cuando la 
		carrera exista.	
	</p>
	
</div>

<table style="width: 300px" border="1">
	<tr>
		<th>ID</th>
		<th>Enlace</th>
	</tr>
	<?php
	$i = 1;
	foreach ($htracks as $key => $value) {
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		
		?>
		<tr<?php echo $class;?>>
			<td><?= $key ?></td>
			<td style="text-align: left; padding-left: 10px">
				<?php
			 	echo $html->link(
			 					     $value,
			 					     array(
			 						 	'controller' => 'tickets',
			 							'action'     => 'add',
			 							'admin'      => 1,
			 							$key, 
			 							rand(1,3)
									 )
									 ,
									 array(
									 	'target' => 'blank'
									 )	
							     );
			 	?>
			 </td>
		</tr>
		<?php
	}
	?>
</table>