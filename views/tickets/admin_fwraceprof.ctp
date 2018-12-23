	
	<table>
		<tr>
			<th>Caballo</th>
			<th>Tickets</th>
			<th>Unds</th>
			<th>Bs</th>
			<th>Prem</th>
			<th>Bs</th>
		</tr>
		
		<?php 
		
		foreach ( $horseSales as $hid => $horse ) :
		
			?>
			
			<tr>
				<td>
					<?php echo $horse['horse'] ?>
				</td>
				<td>
					<?php echo $horse['tickets'] ?>
				</td>	
				<td>
					<?php echo $horse['units'] ?>
				</td>
				<td>
					<?php echo $horse['unibs'] ?>
				</td>
				<td>
					<?php echo $horse['prize'] ?>
				</td>
				<td>
					<?php echo $horse['pribs'] ?>
				</td>
			</tr>
			
			<?php 
		
			endforeach ;

			pr($horseSales)

		?>

	</table>