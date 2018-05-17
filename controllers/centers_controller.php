<?php
class CentersController extends AppController {

	var $name    = 'Centers';
    
	var $helpers = array('Html', 'Form');

	function beforeFilter(){
		parent::beforeFilter();	
	}
	
	function isAuthorized(){
		
		$ret = true;
		
		$actions_root = array(
			"admin_index","admin_edit","admin_view","admin_add",
            "getTimeValues"
		);
		
		$actions_adm = array(
			"admin_index_center","admin_my_conf","admin_edit",'admin_set_hour',
            "getTimeValues","admin_configs"
		);
        
        $actions_taq = array('getTimeValues');
        $actions_onl = array('getTimeValues');
        $actions_aut = array('getTimeValues');
		
		if($this->isRoot() && in_array($this->action, $actions_root)){
			$ret = true;
		}elseif($this->isAdmin() && in_array($this->action, $actions_adm)){
			$ret = true;
		}elseif($this->isTaquilla() && in_array($this->action, $actions_taq)){
			$ret = true;	
		}elseif($this->isOnline() && in_array($this->action, $actions_onl)){
			$ret = true;	
		}elseif($this->isAuto() && in_array($this->action, $actions_aut)){
			$ret = true;	
		}else{
			$ret = false;
		}
				
        if($ret == false) {
            $this->Session->setFlash("Direccion NO permitida");
        }
		
		return $ret;
	}
	
	function admin_index() {
		$this->Center->recursive = 0;
		$this->set('centers', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->flash(__('Invalid Center', true), array('action'=>'index'));
		}
		$this->set('center', $this->Center->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Center->create();
			if($this->data['Center']['name'] != ""){
				if ($this->Center->save($this->data)) {
					//crear su secuencia de tickets number
					$seqInstance = ClassRegistry::init('TicketNumber');
					$seqInstance->create();
					$seq = array('center_id'=> $this->Center->id,'current_value'=>0);
					$seqInstance->save($seq);
					
					$this->Session->setFlash("Centro Guardado");
				} else {
					$this->Session->setFlash("ERROR: Centro NO Guardado");
				}	
			}else{
				$this->Session->setFlash("Nombre NO puede ser Vacio");
			}
			$this->redirect($this->referer());
		}
	}

	function admin_edit($id = null) {
		if (!empty($this->data)) {
			if($this->data['Center']['name'] != ""){
				if ($this->Center->save($this->data)) {
					$this->Session->setFlash("Centro Guardado");
					$operInst = ClassRegistry::init('Operation');
					$operInst->ins_op(4,$this->authUser['profile_id'],"Centro ",$this->Center->id,"Centro ".$this->data['Center']['name']." Editado");
					
				} else {
					$this->Session->setFlash("ERROR: Centro NO Guardado");
				}	
			}else{
				$this->Session->setFlash("Nombre NO puede ser Vacio");
			}
			$this->redirect($this->referer());
		}
		if (empty($this->data)) {
			$this->data = $this->Center->read(null, $id);
		}
	}
	
	function admin_set_hour(){
		if (!empty($this->data)) {
			$this->Center->save($this->data);
			$this->redirect($this->referer());
		}
	}
	
	function admin_my_conf(){
		$id = $this->authUser['center_id'];
		
		$config = ClassRegistry::init('Config');

		$confs = $config->find('all',array(
			'conditions' => array('center_id' => $id),
			'fields' => array('Config.id','amount','actual','from','until','profile_id','ConfigType.name'),
			'recursive' => 1,'order' => array('actual' => 'DESC')
		));
		
		$center = $this->Center->find('first',array(
			'conditions' => array('Center.id' => $id),
			'recursive' => 1,'fields' => array('commercial_name','hour_diff')
		));
		
		$hourDiff  = $center['Center']['hour_diff'];
		$center    = $center['Center']['commercial_name'];
		
		$profs_ins = ClassRegistry::init('Profile');
		
		$profs = $profs_ins->find('all',array(
			'conditions' => array(
                                'center_id'    => $this->authUser['center_id'],
                                'User.role_id' => array(3,4)
                            ),
 			'fields'     => array('Profile.name','Profile.id')
		));
		
		$profiles = array(0 => "Todos");
 		foreach($profs as $pro){
 			$profiles[$pro['Profile']['id']] = $pro['Profile']['name'];
 		}
		
		$specials = $profs_ins->find('all',array(
			'conditions' => array(
                            'center_id'    => $this->authUser['center_id'],
                            'User.role_id' => array(3,4)),
 			'fields'     => array('Profile.id','name','User.role_id',
                                'bet_tracks','reprint_last','anull_last','barcode')
		));

		$this->set(compact('id','center','confs','profiles','specials','hourDiff'));
	}
    
    function admin_configs()
    {
		$id        = $this->authUser['center_id'];
		$profsIns  = ClassRegistry::init('Profile');
		$configIns = ClassRegistry::init('Config');
        $units     = $configIns->getUnitVals($id);
        $percts    = $configIns->getPctVals($id);
        $limits    = $configIns->getLimitVals($id);
        $players   = $profsIns->getPlayers($id);
        $specials  = $profsIns->getConfigs($id);
        $center    = $this->Center->find('first',array(
                        'conditions' => array('Center.id' => $id),
                        'recursive' => 1,'fields' => array('commercial_name')));
		
        $this->set(compact('id','center','units','percts','players',
        	'limits','specials'));
	}
    
    function getTimeValues()
    {
        die(date('g:i A'));
    }
    

}
?>