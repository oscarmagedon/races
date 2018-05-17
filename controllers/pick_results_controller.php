<?php
class PickResultsController extends AppController {

	var $name = 'PickResults';
	var $helpers = array('Html', 'Form');

	function beforeFilter(){
		parent::beforeFilter();	
	}
	
	function isAuthorized(){
		
		$ret = true;
		
		$actions_adm = array(
			"admin_pick_prices"
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
	
	function admin_pick_prices($date = null, $hipodrome_id = null){
	
		if(!empty($this->data)){
			$ticketins = ClassRegistry::init("Ticket");
			//pr($this->data); 
			$this->PickResult->create();
			
			foreach ($this->data['Pick'] as $hipid => $picks) {
				foreach ($picks as $pick => $det) {
					if($det['prize'] > 0){
						$tosave = array(
							'date' => $this->data['PickResult']['date'],
							'hipodrome_id' => $hipid,
							'pick' => $pick,
							'prize' => $det['prize']
						);
						
						if(!empty($det['id']))
							$tosave['id'] = $det['id'];
						
						$this->PickResult->save($tosave);
						unset($this->PickResult->id);	
					}	
				}
			}
			$ticketins->set_pick_price($this->data,$this->authUser['center_id']);
			$this->redirect($this->referer());
		}
		
		if($date == null)
			$date = date('Y-m-d');
		
		$cond['race_date'] = $date;
		$cond['center_id'] = $this->authUser['center_id'];
		
		$raceins = ClassRegistry::init("Race");
		$today_hips = $raceins->find('list',array(
			'conditions' => $cond,
			'fields' => 'hipodrome_id','group'=>'hipodrome_id'
		));		
				
		$results = $this->PickResult->find('all',array(
			'conditions' => array('date'=>$date),
			'recursive' => -1
		));
		
		
		$pick_results = array();
		foreach ($results as $r) {
			$pick_results[$r['PickResult']['hipodrome_id']][$r['PickResult']['pick']]['prize'] = $r['PickResult']['prize'];
			$pick_results[$r['PickResult']['hipodrome_id']][$r['PickResult']['pick']]['id'] = $r['PickResult']['id'];
		}
		
		$this->set('date', $date);
		$this->set('pick_results', $pick_results);
		$this->set('hipodrome_id', $hipodrome_id);
		$this->set('hipodromes', $raceins->Hipodrome->find('list',array('conditions'=>array('id'=>$today_hips),'order'=>array('name' => 'ASC'))));
	}
}
?>