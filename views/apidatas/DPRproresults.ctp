
	<div class="panel-titles">
		<span>
			Date: 
		</span>
		<strong>
			<?php echo $date ?>
		</strong>
		<span>
			Usa Date: 
		</span>
		<strong>
			<?php //echo $usaDate//$proserviceTracks['DateNow'] ?>
		</strong>
		<span>
			, Time: 
		</span>
		<strong>
			<?php //echo $proserviceTracks['TimeNow'] ?>
		</strong>

	</div>


	<div class="panel-infotable">
			
		<table>
			<tr>
				<th>My race</th>
				<th>Check Result</th>
				<th>Save Result</th>
				<th>Delete Result</th>
			</tr>
			<?php 
			foreach ($racesLog as $race): 
				?>
				<tr>
					<td style="text-align: left">
						<strong>
						<?php 
							echo $race['Info']['Race']['number'];
							echo ' '. $race['Info']['Hipodrome']['nick'];
							echo ' :'. $race['Info']['Race']['id'];
							echo '<br>Local Time: ';
							echo $race['Info']['Race']['local_time'];
							echo '<br>SUSP: ';
							echo ($race['Info']['Race']['enable'])?'No':'YES';
							echo '<br>END: ';
							echo ($race['Info']['Race']['ended'])?'No':'YES';
							//echo '<br>'. $race['ProURL'];
						?>	 
						</strong>
					</td>
					<td>
						<?php 
		 				echo $html->link('ProservResult', $race['ProURL']) 
						?>
					</td>
					<td>
						<?php 
						echo $html->link('Save Result',[
							'action' => 'saveresults',
							$race['Info']['Race']['id'],
							$date, 
							$race['Info']['Hipodrome']['nick'],
							$race['Info']['Race']['number']
						]);
		 				?>
					</td>
					<td>
						<?php 
						
						if ($race['Info']['Race']['ended']){

							echo $html->link('Reset Race',[
								'action' => 'resetrace',
								$race['Info']['Race']['id']
							]);	
						}
						?>
					</td>
				</tr>	
				<?php 
			endforeach;
			//pr($racesLog);
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