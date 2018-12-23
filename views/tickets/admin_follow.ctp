
	<?php 
	//echo $date;
	//pr($profiles);
	//pr($htracks);
	?>
	<style type="text/css">
		.profile-box {
			border: 1px solid #000; 
			max-width: 380px; 
			float: left; 
			margin: 1rem;
			padding: 0.5rem;
		}
		.profile-follow {
			margin: 0.5rem; 
			background-color: #DDD
		}
	</style>

	<table style="width:auto;">
		
		<tr>
			<td>
				<?php 
				echo $form->input('date',array(
					'value'=>$date,'label'=>"Fecha",
					'style'=>'width:90px','class'=>'input filter-field')) ?>
			</td>
			<td>
				<?php 
				echo $form->input('htracks',array(
						'value'=>$htrackid,'label'=>"Hipodromo",
						'options'=>$htracks,'empty'=>array(0=>'Sel...'),
						'class' => 'input filter-field')) ?>
			</td>
			<td>
				<?php 
				echo $form->input('races',array(
							'value'=>$raceid,'label'=>"Carrera",
							'options'=>$races,'empty'=>array(0=>'Sel...'),
						'class' => 'input filter-field')) ?>
			</td>

		</tr>
	
	</table>

	<div class="profile-box" style="width: 500px;">
			
		<a href="#" class="follow-races" data-raceid="<?php 
		  echo $raceid 
		  ?>" data-profileid="0">
			TOTALS
		</a>

		<div class="profile-follow">
			- select race
		</div>

	</div>

	<?php 
	foreach ($profiles as $pid => $pname) :
		?>
		<div class="profile-box">
			
			<a href="#" class="follow-races" data-raceid="<?php 
			  echo $raceid 
			  ?>" data-profileid="<?php 
			  echo $pid 
			  ?>">
				<?php echo $pname ?>
			</a>

			<div class="profile-follow">
				- select race
			</div>


		</div>
		<?php 
	
	endforeach;
	?>


	<script type="text/javascript">
		var load_img = 'Cargando... <?php echo $html->image("loading_small.gif",array("alt"=>"Esperando..."))?>',
	        url_filter = '<?php echo $html->url(array("action"=>"follow"))?>',
	        url_details = '<?php echo $html->url(array("action"=>"fwraceprof"))?>';

		$(function(){
		
			$("#date").attr('readonly',true).datepicker({dateFormat:"yy-mm-dd"});
		
			$('.filter-field').change( function () {
				
				location = url_filter + '/' + $('#date').val() + '/' + 
									$('#htracks').val() + '/' + $('#races').val() ;
			} ) ;	


			$('.follow-races').click( function () {

				$btnProf   = $(this),
				$boxProf   = $btnProf.parent().find('.profile-follow') ,
				$profileid = $btnProf.data('profileid') ,
				$raceid    = $btnProf.data('raceid') , 
				$urlFoll   = url_details + '/' + $raceid + '/' + $profileid;
				
				$boxProf.html(load_img);
				
				$boxProf.load($urlFoll);

				return false;
			} ) ;
		
		} ) ;

	</script>

