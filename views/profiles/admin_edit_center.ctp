<script>
var $refrPin = '<?php echo $html->url(array("action" => "generatepin")) ?>';
$(function(){

	$('.refresh-pin').button( { icons: { primary : 'ui-icon-refresh' } } )
		.click( function (){
			//alert('refr');
			var pinimp = $('#ProfileAutopin');

			$.ajax({
				url		   : $refrPin,
				dataType   :  'json',
				beforeSend : function( ) {
				  	//console.log('loading');
				  	pinimp.val('...');
				},
				success: function(data)
				{
					//console.log(data);
					pinimp.val(data.pin);
				}
			})
			return false;
		});

	$("#ProfileAddForm").submit(function() {
		if($('#UserUsername').val() == ""){
			alert('Debe llenar TODOS los campos de Usuario.');
			return false;
		}
    });
});
</script>
<div class="profiles form">
<?php 
echo $form->create('Profile',array('action'=>'edit_center'));
echo $form->input('id');
echo $form->input('User.id'); 
?>
<div class="modalform-panel">
	<?php 
	echo $form->input('name',array('label'=>'Nombre'));
	echo $form->input('phone_number',array('label'=>'Telefono'));
	?>
</div>
<div class="modalform-panel">	
	<?php
	echo $form->input('User.username',array('label'=>'Usuario','disabled'=>true));
	echo $form->input('User.email',array('label'=>'Email'))
	?>
</div>
<div class="modalform-panel">	
	<?php
    if ($this->data['User']['role_id'] == ROLE_ONLINE) {
    	echo "<button class='refresh-pin'>PIN</button>";
    	echo $form->input('autopin',array('label'=>'PIN','div'=>'input autopin'));
    }    
    ?>
</div>
<?php echo $form->end();?>
</div>
<style type="text/css">
	.autopin {
		clear: none;
		float: left;
		width: 70px;
	}
	.autopin input {
		width: 60px;
	}
	.refresh-pin {
		margin: 5px;
	}
</style>