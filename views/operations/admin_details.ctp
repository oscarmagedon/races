<table cellpadding="0" cellspacing="0">
<tr>
	<th>Id</th>
	<th>Fecha</th>
	<th>Hora</th>
	<th>Usuario</th>
	<th>Tipo</th>
	<th>Tabla</th>
	<th>Ref</th>
	<th>Detalles</th>
</tr>
<?php
$i = 0;
foreach ($operations as $operation):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td><?php echo $operation['Operation']['id'] ?></td>
		<td><?php echo $dtime->date_from_created($operation['Operation']['created']) ?></td>
		<td><?php echo $dtime->hour_exact_created($operation['Operation']['created']) ?></td>
		<td><?php echo $operation['Profile']['name'] ?></td>
		<td><?php echo $operation['OperationType']['name'] ?></td>
		<td><?php echo $operation['Operation']['metainf'] ?></td>
		<td><?php echo $operation['Operation']['model_id'] ?></td>
		<td><?php echo $operation['Operation']['metadata'] ?></td>
	</tr>
<?php 
endforeach; 

if(!empty($ticket)){
?>
	<tr<?php echo $class;?>>
		<td> - </td>
		<td><?php echo $dtime->date_from_created($ticket['Ticket']['created']) ?></td>
		<td><?php echo $dtime->hour_exact_created($ticket['Ticket']['created']) ?></td>
		<td><?php echo $ticket['Profile']['name'] ?></td>
		<td>VENTA</td>
		<td>Ticket</td>
		<td><?php echo $ticket['Ticket']['id'] ?></td>
		<td>Monto: <?php echo $ticket['Ticket']['amount'] ?> Premio: <?php echo $ticket['Ticket']['prize'] ?></td>
	</tr>
<?php
}	
?>
</table>