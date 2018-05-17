<?php
class Alert extends AppModel {

	var $name = 'Alert';

	
	function get_als($horses){
		$alerts = $this->find('all',array(
			'conditions' => array('horse_id'=>$horses)
		));
		
		$als = array();
		
		foreach($alerts as $a){
			$als[$a['Alert']['horse_id']]['amount'] = $a['Alert']['amount'];
			$als[$a['Alert']['horse_id']]['suspend'] = $a['Alert']['suspend'];
			$als[$a['Alert']['horse_id']]['total_now'] = $a['Alert']['total_now'];
			$als[$a['Alert']['horse_id']]['myid'] = $a['Alert']['id'];
		}
		
		return $als;
	}
	
	function check_als($horses){
		
		$horses_ids = array();
		$horses_arr = array();
		
		foreach($horses as $h){
			array_push($horses_ids,$h['horse_id']);
			$horses_arr[$h['horse_id']] = $h['units'];
		}
		
		$alerts = $this->find('all',array(
			'conditions' => array('horse_id'=>$horses_ids)
		));
		
		//pr($horses); pr($alerts); pr($horses_arr); pr($horses_ids); 
		$horse = ClassRegistry::init('Horse');
		
		foreach($alerts as $al){
			$this->updateAll(
				array('total_now' => "total_now + ".$horses_arr[$al['Alert']['horse_id']]),
				array('horse_id' => $al['Alert']['horse_id'])
			);
			
			$alert = $this->find('first',array(
				'conditions' => array('horse_id' => $al['Alert']['horse_id'])
			));
			
			//pr($alert);
			if($alert['Alert']['total_now'] >= $alert['Alert']['amount'] && $alert['Alert']['suspend'] == 1){
				//die("alcanzado");
				$horse->updateAll(
					array('enable' => 0),
					array('Horse.id' => $alert['Alert']['horse_id'])
				);
			}
		}
		//die();
	}
	
}
?>