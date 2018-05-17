<?php
class Center extends AppModel {

	var $name = 'Center';

	function get_hour_diff($id)
	{
		$centInfo = $this->find('first',array(
			'conditions' => array('Center.id' => $id),
			'fields'	 => array('hour_diff')
		));
		
		return $centInfo['Center']['hour_diff'];
	}
}
?>