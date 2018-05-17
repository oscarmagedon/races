<?php
class Signal extends AppModel {

	var $name = 'Signal';

	
	function logoutSess($profile_id){
		if ($profile_id !== null){
			$this->updateAll(
						array('enable'     => 0),
						array('profile_id' => $profile_id)
			);	
		}	
		
	}
}
?>