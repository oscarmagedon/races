<div class="tickets view">
	<?php 
	/*
	if($play_type == "PICK"){
		echo "$play_type $pick de $hipodrome";
	}
	else
		echo "$play_type: $number &ordm; de $hipodrome"; 
	*/
	?>
	<table style="font-size:95%; margin-bottom: 0px" cellspacing="0">
	<?php 
	if(empty($details['Each'])){
		?>
		<tr>
			<?php
			if($play_type != "PICK"){
			?>
				<th>Caballo</th>
				<th>Estado</th>
				<th>Unds.</th>
				<th>Premio</th>
			<?php	
			}	
			?>
		</tr>
		<?php 
		//pr($details);
		foreach($details as $detail){
		?>
			<tr>
				<?php
				if($play_type == "PICK"){
					echo "<td style='border-bottom:1px solid black; text-align:left'>";
					foreach ($detail as $race => $ho) {
						echo "<span style='font-weight:bold; font-size:130%'>$race: ".$ho['horse']."</span>(".$dtime->color_stat($ho['stat'])."), ";
					}
					echo "</td>";
				}else{
				?>
					<td><?php echo $detail['horse'] ?></td>
					<td><?php echo $dtime->color_stat($detail['stat'])?></td>
					<td><?php echo $detail['und'] ?></td>
					<td><?php echo $detail['pri'] ?></td>
				<?php	
				}
						
				?>
			</tr>
		<?php 
		}
	}else{
		?>
		<tr>
			<th>Combs.</th>
			<th>Estado</th>
			<th>Unds.</th>
		</tr>		
		<?php 
		foreach($details['Boxes'] as $box){
		?>
			<tr>
				<td>
					<?php 
					foreach($box['Horses'] as $ho){
						echo $ho.",";
					} 
					?>
				</td>
				<td><?php echo $dtime->color_stat($box['Stat']) ?></td>
				<td><?php echo $details['Each'] ?></td>
			</tr>
		<?php 
		}
	}
	?>	
	</table>
</div>