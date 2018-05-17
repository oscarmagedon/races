<table border="1" id="list_horses" cellpadding="0" cellspacing="0">
<tr>
	<th>Nro</th><th>Nombre</th><th>Sel.</th>
</tr>
<?php 
$i = 0;
foreach($horses as $horse){
	?>
	<tr class="row_horse">
		<td class="num_horse" id="<?php echo $horse['Horse']['number'] ?>" style="font-size: 12pt">
			<?php echo $horse['Horse']['number'] ?>
		</td>
		<td style="text-align: left;"><?php 
			if($horse['Horse']['name']) 
				echo $horse['Horse']['name']; 
			else 
				echo " - ";
			?></td>
		<td>
			<div class="ui_check" title="Seleccionar"></div>
			<input name="data[Horse][<?php echo $i ?>][horse_id]" value="<?php echo $horse['Horse']['id']?>" title='<?php echo $i ?>' class="horse_check" type="checkbox">
		</td>
	</tr>
	<?php
	$i ++;
}
?>
</table>