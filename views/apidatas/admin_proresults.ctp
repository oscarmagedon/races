
	<script type="text/javascript">
		$(function(){

			var filt_url = '<?php echo $html->url(array("action"=>"proresults")) ?>';

			$("#date").datepicker({ dateFormat : "yy-mm-dd" });

			$("#date").change(function() {

				location = filt_url + "/" + $(this).val();
			});

			$("#htracks").change(function() {

				location = filt_url + "/" + $('#date').val() + "/" + $(this).val();
			});

		});
	</script>


	<div class="panel-titles">
		<span>
			Date: 
		</span>

		<input type="text" value="<?php echo $date ?>" name="date" id="date" readonly="readonly">	
		
		<span>
			Usa Date: 
		</span>
		
		<strong>
			<?php echo $usaDate ?>
		</strong>

		<span>
			Horsetrack: 
		</span>

		<?php 
		echo $form->input('htracks',[
			'options'=>$htracks,
			'value'=>$htrack,
			'div'=>false,'label'=>false])
		 ?>

	</div>


	<div class="panel-infotable">
			
		<table>
			<tr>
				<th>Race</th>
				<th>Time</th>
				<th>Status</th>
				<th>Check Race</th>
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
						?>	 
						</strong>
					</td>
					<td style="text-align: left">
						<strong>
						<?php 
							echo $race['Info']['Race']['local_time'];
						?>	 
						</strong>
					</td>
					<td style="text-align: left">
						<strong>
						<?php 							

						echo ($race['Info']['Race']['enable'])?'Act':'SUSP';
						echo ' - ';
						echo ($race['Info']['Race']['ended']==0)?'NoRes.':'ENDED';
						//echo '<br>'. $race['ProURL'];
						?>	 
						</strong>
					</td>
					<td>
						<?php 
		 				echo $html->link('ProservRace', $race['ProRace']) 
						?>
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