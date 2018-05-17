<html>		
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="Keywords" content="hipico,apuestas,online,carreras,caballos" >
		<meta name="Description" content="Portal de informacion y apuestas hipicas" >
		<title>..:: TOTAL HIPICO.COM : Tu portal informativo h&iacute;pico :: .. </title>
		<link rel="shortcut icon" href="<?php echo $html->url('/favicon.ico') ?>" type="image/x-icon">
		<?php 
		echo $html->css('page_style');
		echo $html->css('jquery-ui-1.8.16.custom');
		
		echo $javascript->link("jquery1.7");
		echo $javascript->link("jquery-ui-1.8.16.custom.min");

		?>
		<script>
		$("#panel_look").dialog({
			autoOpen: false,
			bgiframe: true,		
			modal: true,
			height: 300,
			width: 500,
			resizable: true
		});
		
		$(function(){
			$("#login").click(function(){
				var myurl = $(this).attr("href");
				var tit = $(this).attr("title");
				var totit = tit;
				
				$('#panel_look').html('<?php echo $html->image("loading.gif")?>');
				$('#panel_look').dialog({title:totit});
				$('#panel_look').load(myurl);
			
				$('#panel_look').dialog('open');
				return false;
			});
		});
		</script>
	</head>
	<body>
		<div id="wrapper">
			<div id="banner">
				<div id='logo'></div>
				<div id='entrar'>
					<?php
					if(!empty($authUser)){
						echo $html->link("Usuario ".$authUser['profile_name']." ingresado",array('controller'=>'users','action'=>'login'));
					}else{	
					?>
						<h2>Entrar al sistema</h2>
						<form method="post" action="<?php echo $html->url(array('controller'=>'users','action'=>'login')) ?>">
							<table>
								<tr>
									<td style="text-align:right">Usuario:</td>
									<td><input name="data[User][username]" type="text" maxlength="20" value="" id="UserUsername" /></td>
								</tr>
								<tr>
									<td style="text-align:right">Password:</td>
									<td><input type="password" name="data[User][password]" value="" id="UserPassword" /></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align:center"><input type='submit' value="Entrar"></td>
								</tr>
							</table>	
						</form>
					<?php
					}
					?>
				</div>
				<div id="slogan">Tu NUEVO portal de informaci&oacute;n h&iacute;pica</div>
			</div>
			<div id="menu">
				<?php echo $html->link("INICIO",array('action'=>'home')); ?>
				<?php echo $html->link("QUIENES SOMOS",array('action'=>'quienes')); ?>
				<?php echo $html->link("ESTADISTICAS",array('action'=>'constr')); ?>
				<?php echo $html->link("NUESTRO SISTEMA",array('action'=>'constr')); ?>
				<?php echo $html->link("OTROS LINKS",array('action'=>'constr')); ?>
			</div>
			<div id="content">
				<?php  echo $content_for_layout; ?>				
			</div>
			<div id="footer">
				All rights reserved. Caracas, Venezuela.<br />
				Copyright &copy; TotalHipico.Com
			</div>
		</div>
		<div id="panel_look" style="text-align: justify"></div>
	</body>		   
</html>
