<?php
class PlayConfig extends AppModel {

	var $name = 'PlayConfig';

	var $belongsTo = array('PlayType','Profile');
	
}
?>