
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
			<?php echo $proserviceTracks['TimeNow'] ?>
		</strong>
		<span>. Next Days</span>
		<select id="change-date">
			
			<?php 
			foreach($proserviceTracks['NextDs'] as $date) {
				echo "<option value='";
				echo substr($date, 5, 0);
				echo "'>";
				echo $date;
				echo '</option>';
			}
			?>	
		</select>
		<select>
			<option value="0"<?php echo ($fullMode==0)?' selected':'' ?>>Basic</option>
			<option value="1"<?php echo ($fullMode==1)?' selected':'' ?>>FullMode</option>
		</select>
	</div>


	<div class="panel-infotable">
			
		<table>
			<tr>
				<th>My races</th>
				<th>Save Nick</th>
				<th>Delete Nick</th>
			</tr>
			<?php 
			foreach ($trackIds as $nick => $extra): 
				?>
				<tr>
					<td>
						<strong>
						<?php 
							echo (isset($racesNick[$nick]))?$racesNick[$nick]['races']:'-';
						?>	 
						</strong>
					</td>
					<td>
						<?php 
		 				echo $html->link($nick,
							[
								'action'=>'proservbytrack', 
											$nick, 
											$extra['country'],
											$extra['dayEve']
							]
						) 
						?>
					</td>
					<td>
						<?php 
						if ( isset($racesNick[$nick]) ) {
							echo $html->link($nick,
								[
									'action' => 'deletebytrack',$nick],
								[
									'style'  => 'color: Red'
								]
							);								
						}
		 				?>
					</td>
				</tr>	
				<?php 
			endforeach 
			?>
		</table>
	</div>

	<?php 
	if($fullMode==1) :
		?>
		
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
					<th colspan="2">
						Links
					</th>
				</tr>
			
				<?php 
				foreach ($proserviceTracks['Tracks'] as $proTrack) :
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
		 					Proservice
		 				</a>
		 			</td>

		 			<td>
		 				<?php 
		 				echo $html->link('OurLink',
								[
									'action'=>'proservrace',
									$proTrack['currentRace'],
									$proTrack['trackId'],
									$proTrack['country'],
									$proTrack['dayEvening'],
							],
								['target'=>'_blank']) ?>
		 			</td>

					<?php
					echo "</tr>";
					
				endforeach;
				?>
			</table>
		</div>

		<?php 
	endif
	?>

	<style type="text/css">
		
		.panel-titles {
			 padding: 16px; 
			 margin-bottom: 16px; 
			 background-color: #DDD; 
			 font-size: 160%
		}
		.panel-infotable {
			background-color: #EEE; 
			max-height: 380px; 
			overflow-y: scroll;
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
	
