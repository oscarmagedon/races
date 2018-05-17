<script>
$(function(){
	$("#UserReader").focus();
	
	$("#UserBarcForm").submit(function(){
		var valu =  $("#UserReader").val();
		alert(valu);
		var parts = valu.split("-");
		alert('parte 1:' + parts[0] + "con parte 2: " + parts[1] + " y parte 3: " + parts[2]);
		return false;
	});
});
</script>
<?php

echo $form->create("User",array('action'=>'barc'));
echo $form->input('reader');
echo $form->end("OK PAGAR");
?>

