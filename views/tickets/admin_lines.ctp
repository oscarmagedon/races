
<?php 
if (!empty($lines['Winner']) ):
?>
	<div style="border-bottom: 1px solid #ccc">
		<strong>Winner box <?php echo $lines['Winner']['Box'] ?></strong>, 
		<em>Unidades: <?php echo $lines['Winner']['Prize'] ?></em>
		<br>Comb.:[ 
		<?php 
		//pr($lines['Winner']); 
		
		foreach ($lines['Winner']['Bets'] as $bk => $bet) {
			echo ($bk!=0)?' + ':null;
			echo $horses[$bet['horse_id']];
		}

		?>
		]
	</div>
<?php  
endif
?>

<div style="width: 300px;">
	<strong>
		<?php echo count($lines['Losers']) ?> losers:
	</strong>
	<?php 
	foreach($lines['Losers'] as $box => $bets) : 
		?>
		 - [ 
		<?php 
		//pr($lines['Winner']); 
		
		foreach ($bets as $bk => $bet) {
			echo ($bk!=0)?' + ':null;
			echo $horses[$bet['horse_id']];
		}

		if ($box > 10) {
			break;
		}

		?>
		]
		<?php 
	endforeach
	?>
	... (<?php echo (count($lines['Losers']) - 10 ) ?> +)

</div>