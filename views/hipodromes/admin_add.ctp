<div class="hipodromes form" style="background-color:#FFF; height:100%;">
<?php echo $form->create('Hipodrome');?>
	<?php
		echo $form->input('name',array('label'=>'Nombre'));
        echo $form->input('nick',array('label'=>'NICK'));
        echo $form->input('tvgnick',array('label'=>'NICK TVG'));
		echo $form->input('htgmt',array('label'=>'GMT','value' => 0));
		echo $form->input('national',array('label'=>'Nacional'));
	?>
<?php echo $form->end('Submit');?>
</div>