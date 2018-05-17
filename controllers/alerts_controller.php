<?php
class AlertsController extends AppController {

	var $name = 'Alerts';
	var $helpers = array('Html', 'Form');

	function beforeFilter(){
		parent::beforeFilter();	
	}
	
	function isAuthorized(){
		
		$ret = true;
		
		$actions_adm = array(
			"admin_set","admin_index"
		);
		
		if($this->isAdmin() && in_array($this->action, $actions_adm)){
			$ret = true;	
		}else{
			$ret = false;
		}
				
		if($ret == false)
			$this->Session->setFlash("Direccion NO permitida");
		
		return $ret;
	}
	
	function admin_index($race_id){
		$race = ClassRegistry::init('Race');
		$horse = ClassRegistry::init('Horse');
		
		$race_dets = $race->find('first',array(
			'conditions' => array("Race.id"=>$race_id)
		));
		//pr($race_dets);die();
		$carr = $race_dets['Race']['number'];
		$date = $race_dets['Race']['race_date'];
		$hip_name = $race_dets['Hipodrome']['name'];
		$hip_id = $race_dets['Hipodrome']['id'];
		
		$hors_ids = $horse->find('list',array(
			'conditions' => array("race_id"=>$race_id),
			'fields' => 'Horse.id'
		));
		
		$horses = $horse->find('all',array(
			'conditions' => array("race_id"=>$race_id),
			'recursive' => -1
		));
		
		$alerts = $this->Alert->get_als($hors_ids);
		
		$this->set('carr',$carr);
		$this->set('alerts',$alerts);
		$this->set('date',$date);
		$this->set('horses',$horses);
		$this->set('hip_name',$hip_name);
		$this->set('hip_id',$hip_id);
	}
	
	function admin_set() {
		if (!empty($this->data)) {
			//pr($this->data);
			$horses_ids = array();
			$horseTik = ClassRegistry::init('HorsesTicket');
			
			$this->Alert->create();
			foreach($this->data['Alert'] as $al){
				if(!empty($al['id'])){					
					$this->Alert->updateAll(
						array('amount' => $al['amount'],'suspend' => $al['suspend']),
						array('Alert.id' => $al['id'])
					);
				}else{
					if($al['amount'] != ""){
						$horse_totals = $horseTik->find('first',array(
							'conditions' => array("horse_id"=>$al['horse_id']),
							'fields' => array("SUM(units) as sumun")
						));
					
						$al['total_now'] = $horse_totals[0]['sumun'];
						$this->Alert->save($al);
						unset($this->Alert->id);	
					}	
				}			
			}
			$this->redirect($this->referer());
		}
	}
	
}
?>