
	<div style='border:1px solid blue; padding: 2px; width: auto'>
		<em>
			<?php echo $urlCheck ?>
		</em>
	</div>

	<?php 
	foreach ($bovadaLog as $race) :
		?>
		
		<div style="border-bottom: 1px solid #000; padding: 4px; margin-bottom: 8px">
			<strong>Race <?php echo $race['number']; ?></strong>
			<span> /status:</span>
			<em><?php echo $race['status'] ?></em>
			<ul>
				<?php 
				foreach ($race['Horses'] as $horse) :
					?>
					<li <?php echo (isset($horse['scratched'])?"style='color:#F00'":null) ?>>
						
						<?php 
						echo (isset($horse['scratched'])?'<i>RETIRED- </i>':null);
						?>
						<strong>
							<?php 
							echo $horse['number'];
							echo $horse['ccode'];
							?>	
						</strong>
						<small>
							<?php 
							echo $horse['name'];
							?>
						</small>
					</li>
					<?php 
				endforeach 
				?>
			</ul>
			<?php 
			//echo $race->description;
            //echo '<br> - status: '.$race->status;
            //echo '<br> - mtp: '.$race->details->mtp;
			?>
		</div>
		
		<?php

	endforeach;
	//pr($bovadaLog);
	?>

	