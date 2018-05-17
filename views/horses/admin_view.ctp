<div>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th>Numero</th>
			<th>Nombre</th>
		</tr>
		<?php
		$i = 0;
		foreach ($horses as $horse){
			$endvals = "";
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			
			?>
			<tr<?php echo $class;?>>
				<td><?php 
					echo $horse['Horse']['number']; 
					if($horse['Horse']['enable'] == 0)
						echo " <span style='color:Red'>(Retirado)</span>";
				?></td>
				<td><?php echo $horse['Horse']['name'] ?></td>
			</tr>
		<?php 
		} 
		?>
		</table>
</div>