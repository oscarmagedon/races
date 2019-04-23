
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
	</div>


	<div class="panel-infotable">
			
		<table>
			<tr>
				<th>Horsetrack</th>
				<th>My races</th>
				<th>Save Nick</th>
				<th>Delete Nick</th>
			</tr>
			<?php 
			foreach ($trackIds as $nick => $extra): 
				?>
				<tr>
					<td>
						<?php
						echo $extra['name'] 
						?>
						<small>
							<?php 
							echo $extra['country'];
							 ?>
						</small>
					</td>
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
						) ;
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
	//0pr($trackIds) 
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
	
