<?php
class Restriction extends AppModel {

	var $name = 'Restriction';
	
	function get_by_race($race_id){
		$rest = $this->find('all',array('conditions'=>array('race_id'=>$race_id)));
		
		$arranged = array();
		
		foreach ($rest as $r) {
			$re = $r['Restriction'];
			if(!empty($arranged[$re['profile_id']])){
				array_push($arranged[$re['profile_id']],$re['play_type_id']);
			}
			else {
				$arranged[$re['profile_id']] = array(0=>$re['play_type_id']);
			}
		}
		
		return $arranged;
	}
	
	function verify_bet($race_id,$profile_id,$play_type_id){
		if(in_array($play_type_id, array(1,2,3))){
			$pid = 1;	
		}elseif(in_array($play_type_id, array(4,5,6))){
			$pid = 2;
		}elseif($play_type_id == 7){
			$pid = 3;
		}elseif($play_type_id == 8){
			$pid = 4;
		}elseif($play_type_id == 9){
			$pid = 5;
		}
		
		$found = $this->find('count',array(
			'conditions' => array('race_id'=>$race_id,'profile_id'=>$profile_id,'play_type_id'=>$pid)
		));
		
		if($found > 0)
			return true;
		else 
			return false;
	}
}
?>