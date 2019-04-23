
	<div class="panel-titles">
		<span>
			Date: 
		</span>
		<strong>
			<?php echo $dbDate//$proserviceTracks['DateNow'] ?>
		</strong>
		<span>
			Usa Date: 
		</span>
		<strong>
			<?php echo $usaDate//$proserviceTracks['DateNow'] ?>
		</strong>
		<span>
			, Time: 
		</span>
		<strong>
			<?php echo $proTracks['TimeNow'] ?>
		</strong>
	</div>

		
	<div class="panel-infotable">
		<table>
			<tr>
				<?php 
				foreach ($proFields as $pfield): 
					?>
					<th><?php echo $pfield ?></th>	
					<?php 
				endforeach 
				?>
				<th>
					Links
				</th>
			</tr>
		
			<?php 
			foreach ($proTracks['Tracks'] as $proTrack) :
		 		echo "<tr>";
				foreach ($proFields as $pfield): 
					?>
					<td>
						<?php 
						echo $proTrack[$pfield] 
						?>
					</td>	
					<?php 
				endforeach;
		 		?>
	 		
	 			<td>
	 				<a href="<?php echo $proTrack['RaceLink'] ?>" target="_blank">
	 					By Race
	 				</a>
	 			</td>

				<?php
				echo "</tr>";
				
			endforeach;
			?>
		</table>
	</div>

	

	<style type="text/css">
		
		.panel-titles {
			 padding: 16px; 
			 margin-bottom: 16px; 
			 background-color: #DDD; 
			 font-size: 160%
		}
		.panel-infotable {
			background-color: #EEE;
		}
		.panel-infotable table {
			width: auto;
			float: left;
			margin-right: 1rem;
			clear:none;
		}
		.panel-infotable table tr td {
			padding: 0.5rem 1rem;
			font-size: 120%;
		}
		.panel-infotable table tr:nth-child(even) td {
			background: #CCC
		}
		.panel-infotable table tr:nth-child(odd) td {
			background: #FFF
		}

		.panel-infotable table tr:hover td {
			border-bottom: 1px solid #000;
			font-weight: bold;
		}
	</style>
	
