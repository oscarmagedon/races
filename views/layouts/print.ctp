<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="">
<meta name="description" content="">
	<title>
		Impresion de Ticket
	</title>
	<?php
	echo $html->css('cake.generic');
	echo $javascript->link("jquery1.7");
	?>
<style type="text/css">
* {
	margin:0;
	padding:0;
	font-size:10pt;
	text-align:left;
}
body {
	background: #fff;
	font-family:verdana,helvetica,arial,sans-serif;
	font-size:10pt;
	margin: 0;
}
h3{
font-size:14px;
margin-bottom:6px;
}
h4{
font-size:12px;
margin-bottom:6px;
}
.odd {
  background-color: #e6e6e6;
}
td,th{
padding:3px;
}
</style>
</head>
<body>
	<div style="position:absolute;top:0px;left:0px;">
		<?php echo $content_for_layout;?>
	</div>
</body>
</html>