<div class="horses index">
<table cellpadding="0" cellspacing="0" style="font-size:80%">
	<tr>
		<th>Posicion</th>
		<th>Caballo</th>
		<th>Win</th>
		<th>Place</th>
		<th>Show</th>
	</tr>
	<tr>
		<td>1&ordm;	</td>
		<td>
			<?php echo $results[1]['horse'] ?>
		</td>
		<td>
			<?php echo $results[1]['win'] ?>
		</td>
		<td>
			<?php echo $results[1]['place'] ?>
		</td>
		<td>
			<?php echo $results[1]['show'] ?>
		</td>
	</tr>
	<tr>
		<td>2&ordm;	</td>
		<td>
			<?php echo $results[2]['horse'] ?>
		</td>
		<td>
			-
		</td>
		<td>
			<?php echo $results[2]['place'] ?>
		</td>
		<td>
			<?php echo $results[2]['show'] ?>
		</td>
	</tr>
	<tr>
		<td>3&ordm;	</td>
		<td>
			<?php echo $results[3]['horse'] ?>
		</td>
		<td>
			-
		</td>
		<td>
			-
		</td>
		<td>
			<?php echo $results[3]['show'] ?>
		</td>
	</tr>
    <tr>
        <th colspan='5'>
            EXA : <?php echo $exotics['exacta'] ?> ,
            TRI : <?php echo $exotics['trifecta'] ?> ,
            SUP : <?php echo $exotics['superfecta'] ?>.
        </th>
    </tr>
    
    <?php
    if (!empty ($retires)) :
    ?>
	<tr>
		<th colspan="5">
		Retirados en carrera: <br />
			<?php
			foreach($retires as $r){ 
				echo $r.", ";
			}				
			?>	
		</th>
	</tr>
    <?php
    endif;
    ?>
</table>
</div>