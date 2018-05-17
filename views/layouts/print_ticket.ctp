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
}
body {
	background: #FFF;
	font-family: "Verdana";
	font-size: 10pt;
	margin: 0;
    top: 0px;
    left: 0px;
}
#accept{
    display: none;
}
.wait-screen {
   margin: 2px;
   padding: 2px;
   font-size: 14pt;
}

.ticket-printer {
   margin: 2px;
   padding: 2px;
   width: 300px;
   height: auto;
}

/* ALL TICKET TABLES */

table {
    background: none;
    border: none;
}
table tr td{
    text-align: left;
    background: none;
    border: none;
}
table tr th{
    border: none;
    background: none;
    
}

.ticket-table {
    
}

.ticket-table .title-big {
    font-size: 14pt;
    font-weight: bold;
    text-align: center;
}
.print-dets{
    margin: 0 auto;
    font-size: 12pt;
    width: auto;
}
.print-dets th{
    text-align: left;
    background: none;
    padding: 2px 6px;
}
.print-dets td{
    padding: 2px;
}

.ticket-table .middle-title {
    padding: 0;
    font-size: 12pt;
    font-weight: bold;
}

.right-paneled {
    float: right;
}

@media print {
	.wait-screen {
 		display:none;
    }
}

    @media screen {
        .ticket-printer {
            <?php
            if ($test != 'show') {
                echo "display:none;";
            }
            ?>
        }

    }

/*
OLDIEES!! TMP
*/

#title_center{
	float: left; 
	clear: both; 
	width: 290px; 
	margin-left:5px;
	text-align: center;
	font-size:14pt;
	font-weight:bold;
}
#rif_lic{
	width: 300px;
	float: left;
}
#rif{
	float: left; width: 155px;
	margin-bottom: 5px;
}
#lic{
	float: right; width: 135px;
	text-align: right; margin-bottom: 5px;
}
#number_serial{
	float: left; 
	clear: none; 
	width: 190px;
	font-size:12pt;
}
	
#create_time{
	float: right; 
	clear: none; 
	width: 110px; 
	text-align:right;
	font-size:12pt;
}
	
#race_data{
	float: left; 
	clear: both; 
	width: 300px; 
	text-align: center;
	font-size:16pt;
	margin-top:10px;
}
	#race_number{
		float:left; 
		width:40px;
		font-size:12pt;
		font-weight:bold;
	}
	#hipodrome{
		float:left;
		width:150px; 
		font-size:12pt;
		font-weight:bold;
		text-align:center;
	}
	#race_date{
		float:right;
		text-align:right;
		font-size:12pt;
		width: 110px;
		font-weight:bold;
	}

#play_data{
	float: left; 
	clear: both; 
	width: 285px; 
	margin-top:5px;
}
	#horses{
		width:100%;
		margin-left:5px;
		margin-bottom:10px;
		background-color:blue;
		clear:both;
	}
	#play_type{
		float:left; 
		width:90px;
		padding-left: 5px; 
		font-weight:bold; 
		font-size:14pt;
	}
	#units{
		float:right;
		text-align:right;
		width: 100px;
		font-size:14pt;
	}
	.barcode{
		border-top: 1px dotted #000;
		border-bottom: 1px dotted #000;
		padding: 3px;
	}
#valid{
	clear:both;
	font-size:14pt;
	font-weight:bold;
	padding-top: 10px;
}
</style>
<script type="text/javascript">
    var url_loc = '<?php echo $html->url(array("action"=>"add")) ?>';
    
    $(function() {
        
        $('.imgload').hide();
        $("#accept").show();
        
        <?php if ($test != 'show') echo "print();";  ?>

        $("#accept").click(function(){ refrescador(); });
        
    });
    
    function refrescador(){
        location = url_loc;
    }
    
    <?php if ($test != 'show') echo "setInterval('refrescador()',3000);";  ?>
</script>
</head>
<body>
	<?php echo $content_for_layout ?>
</body>
</html>

