<!DOCTYPE html>		
	<head>
        <meta charset="UTF-8">
        <title>
            <?php 
            echo $title_for_layout;
            ?>
            - TWI Horses::..
        </title>
		<link rel="shortcut icon" href="<?php echo $html->url('/favicon.ico') ?>" type="image/x-icon">
		<?php 
		echo $html->css(array('cake.generic','jquery-ui-1.8.16.custom',
            'specials.css?g=a','totalstyle.css?p=0'));
		echo $javascript->link(array("jquery1.7","jquery-ui-1.8.16.custom.min","generals.js?a=b"));
		?>
		<script>
		var urlHour    = "<?php echo $html->url(array(
                        'controller'=>'centers','action'=>'getTimeValues','admin'=>0)) ?>",
            urlClosing = '<?php echo $html->url(array(
                        'controller'=>'results','action'=>'chkclosebov','admin'=>1)) ?>',
            $busyFlag  = false;
		
        $(function(){
            <?php
            if ( !empty($authUser)){
                echo "setInterval(timeRefresh,58000);";
                /*
                if ($authUser['role_id'] == ROLE_ROOT) {
                    //closing check every 10 secs
                    echo "setInterval(function () { checkService(urlClosing) },4000);";
                }
                */
                
            } 
            ?>
        });
        
        function checkService (urlSrvc) {
            
            if ($busyFlag == false) {
                
                $waitMsg = 'Buscando cierres...',
                $sucMsg  = ' carreras cerradas.',
                $failMsg = 'Ninguna carrera cerrada.';
                
                $.ajax({
                    url         : urlSrvc,
                    type        : 'get',
                    dataType    : 'json',
                    contentType : "application/json; charset=utf-8",
                    beforeSend: function (){
                        $('.service-panel').html($waitMsg);
                        $busyFlag = true;
                    },
                    success: function(changes){
                        //console.log(changes);
                        if (changes.count == 0) {
                            $message = $failMsg;
                        } else {
                            $message = '<b style="color:#FA5858">' + changes.closed  +
                                        $sucMsg + '</b>';
                        }

                        $('.service-panel').html($message);

                        $busyFlag = false;
                        
                        setTimeout(function(){
                            $('.service-panel').html('');
                        },2000);
                        
                        
                    }
                });
            }
        }
        
        function timeRefresh() {
            $.ajax({
                url         : urlHour,
                type        : 'get',
                dataType    : 'html',
                contentType : "application/json; charset=utf-8",
                success: function(newtime){
                    
                    $('.regular-value-time').html(newtime);
                    
                    
                }
            });
		}  </script> 
    <script>
		$(document).ready(function() {
  // fade out flash 'success' messages
  $('.message').delay(4000).hide('highlight', {color: 'transparent'}, 0);
});
    </script>
	</head>
	<body>
		<div id="banner">
			<?php 
			if(!empty($authUser)){
                //pr($authUser)
			?>
				<div id="logged">
					<?php 
					echo "Ingresado <b>".$authUser['profile_name']."</b> ";
					echo $html->link("Cerrar Sesion",array(
                        'controller'=>'users','action'=>'logout','admin'=>0))
					?>
				</div>
                
                <div class='hour-panel regular'>
					<i>Hora:</i> 
                    <b class="regular-value-time">
                        <?php echo $dtime->time_to_human(date('H:i:s')) ?>
                    </b>
				</div>
                <div class="service-panel"></div>
			<?php
			}
			?>
		</div>
        <?php
        if ( !empty($authUser) ) {
            echo $this->element('menu');
		}
		?>
		<div id="content">
			<?php  
			if ($session->check('Message.flash')): $session->flash(); endif;
			$session->flash('auth');
			echo $content_for_layout;
			?>		
		</div>
		<div id="footer">
            System by 
            <a target="_blank" href="http://www.twihorses.com">Total Web Internacional C.A.</a>
            
		</div>
		<div id="panel_look" style="text-align: justify"></div>
	</body>		   
</html>
