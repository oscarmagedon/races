<style>
h2,h3{
	padding-top:5px;
}
.follow form{
	width:100%;
}
.amount{
	width:100px;
}
.chkbx{
	width:30px;
}
.reached td{
	border: 1px solid Red;
	color: Red;
}
</style>
<div class="alerts">	
<h2>Alertas a la <?php echo $carr ?>a carrera de <?php echo $hip_name ?> del <?php echo $dtime->date_spa_mon_abr($date) ?></h2>	
<?php 
//pr($horses);
echo $html->link("Volver a Seguimiento",array('controller'=>'tickets','action'=>'follow',$date,$hip_id));

echo $form->create("Alert",array('action'=>'set'));
?>
<table border="1" style="width:600px">
	<tr>
		<th>Caballo</th>
		<th>Acumulado</th>
		<th>Limite</th>
		<th>Suspender</th>
	</tr>
	<?php 
	$i = 0;
	foreach($horses as $h){
		$reached = "normal";
		$horse_name = $h['Horse']['number'];
		$chkd = false;
		$theidpart = "";
		$amo_val = "";
		$amo_now = "";
		$cls = "";
		
		if($h['Horse']['name'] != "")
			$horse_name .= " (".$h['Horse']['name'].")";
			
		if(!empty($alerts[$h['Horse']['id']])){
			if($alerts[$h['Horse']['id']]['suspend'] == 1)
				$chkd = true;
			
			$theidpart = $form->input("Alert.$i.id",array('value'=>$alerts[$h['Horse']['id']]['myid']));
			$amo_val = $alerts[$h['Horse']['id']]['amount'];
			$amo_now = $alerts[$h['Horse']['id']]['total_now'];
		
			if($amo_now >= $amo_val){
				$cls = "<br /><span style='color: Red'>Alcanzado</span>";
				$reached = "reached";
			}
		}
		
	?>
		<tr class="<?php echo $reached ?>">
			<td><?php echo $horse_name ?></td>
			<td>&nbsp;<?php echo $amo_now ?></td>
			<td style="text-align:center; vertical-align:middle">
				<?php
				echo $theidpart; 
				echo $form->input("Alert.$i.amount",array('class'=>'amount','label'=>false,'div'=>false,'value'=>$amo_val));
				echo $form->input("Alert.$i.horse_id",array('type'=>'hidden','value'=>$h['Horse']['id']));
				echo $cls;
				?>
			</td>
			<td style="text-align:center; vertical-align:middle">
				<?php 
				echo $form->input("Alert.$i.suspend",array('type'=>'checkbox','class'=>'chkbx','label'=>'Suspender','checked'=>$chkd));
				?>
			</td>
		</tr>
	<?php
	$i ++;
	}
	?>
</table>
<?php 
echo $form->end("Guardar")
?>
</div>