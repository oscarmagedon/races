<?php
class HorsesController extends AppController {

	var $name = 'Horses';
	var $helpers = array('Html', 'Form');

	function beforeFilter(){
		parent::beforeFilter();	
	}
	
	function isAuthorized(){
		
		$ret = true;
		
		$actions_root = array(
			"admin_details","admin_add","admin_delete"
		);
		
		$actions_adm = array(
			"admin_details","admin_add","admin_view","admin_delete"
		);
		
		$actions_taq = array(
			"admin_list_ajax","admin_view","admin_data_pick"
		);
		
		$actions_onl = array(
			"admin_list_ajax","admin_view","admin_data_pick"
		);
		
		if($this->isRoot() && in_array($this->action, $actions_root)){
			$ret = true;
		}elseif($this->isAdmin() && in_array($this->action, $actions_adm)){
			$ret = true;
		}elseif($this->isTaquilla() && in_array($this->action, $actions_taq)){
			$ret = true;	
		}elseif($this->isOnline() && in_array($this->action, $actions_onl)){
			$ret = true;	
		}elseif($this->isAuto() && in_array($this->action, $actions_onl)){
			$ret = true;	
		}else{
			$ret = false;
		}
				
		if($ret == false)
			$this->Session->setFlash("Direccion NO permitida");
		
		return $ret;
	}

	function admin_details($race_id = null) {
		
        if(!empty($this->data)){
			
            $familyInst = ClassRegistry::init('Family');
            $raceSons   = $familyInst->find('list',array(
                            'conditions' => array(
                                'race_id' => $this->data['Horse'][0]['race_id']),
                            'fields' => 'race_son'
                        ));
            
            //pr($this->data);
            //pr($raceSons);
            
            $horsesRace = $this->Horse->find('all',array(
                            'conditions' => array('race_id'=>$raceSons),
                            'recursive' => -1,
                            'fields' => array('id','number','race_id','name','enable')
                        ));
            
            //pr($horsesRace);
            
            $arrangeSons = array();
            $pos = 0;
            $rid = 0;
            foreach ($horsesRace as $hr) {
                
                if ($rid != $hr['Horse']['race_id'])
                    $pos = 0;
                
                $arrangeSons[$hr['Horse']['race_id']][$pos] =  $hr['Horse']['id']
                                    . "-" .$hr['Horse']['name'];
                                            
                $pos ++;
                               
                $rid = $hr['Horse']['race_id'];
                
            }
            //pr($arrangeSons);
            
            foreach($this->data['Horse'] as $hk => $horse){
				$this->Horse->save($horse);
                
                //save his sons
                foreach ($raceSons as $rs) {
                    $this->Horse->updateAll(
                        array(
                            'number' => $horse['number'],
                            'name'   => "\"" . $horse['name'] . "\"",
                            'enable' => $horse['enable']
                        ),
                        array('Horse.id' => $arrangeSons[$rs][$hk])
                    );
                }
                
			}
			//die();
            
			$operInst = ClassRegistry::init('Operation');
			$operInst->ins_op(4,$this->authUser['profile_id'],"Caballos",$this->data['Horse'][0]['race_id'],"Edicion Detalle caballos");	
			
			$this->Session->setFlash('Caballos Guardados');
			$this->redirect($this->referer());
		}
		
		$horses = $this->Horse->find('all',array(
                    'conditions' => array('race_id'=>$race_id),
                    'recursive' => -1,
                    'fields' => array('id','number','race_id','name','enable')
                ));
		
		$this->set('horses', $horses);
		$this->set('race_id', $race_id);
	}
	
	function admin_add($race_id = null) {
		if (!empty($this->data)) {
			//pr($this->data);die();
			$this->Horse->create();
			foreach($this->data['Horse'] as $horse){
				$horse['race_id'] = $this->data['General']['race_id'];
				unset($this->Horse->id);
				$this->Horse->save($horse);
			}
			
			$allhors = count($this->data['Horse']);
			
			$operInst = ClassRegistry::init('Operation');
			$operInst->ins_op(3,$this->authUser['profile_id'],"Caballos",$this->data['General']['race_id'],"$allhors caballos inscritos");	
			
			$this->Session->setFlash('Caballos Guardados');
			$this->redirect($this->referer());
		}
		
		$this->set('race_id',$race_id);
	}
    
	function admin_list_ajax($race_id,$json = 0){
		$horses = $this->Horse->find('all',array(
                    'conditions' => array('race_id'=>$race_id,'enable'=>1),
                    'fields'     => array('id','number','name'),
                    'recursive'  => -1
                    //,'order'      => 'number'
                ));
		//pr($horses);die();
        
        $horses = $this->Horse->orderThem($horses);
        
		if($json == 1){
			
            $hors = array();
			
            $hors[$h['Horse']['id']] = $h['Horse']['number'];
            
            echo json_encode($hors);
			
            die();	
            
		}else{
			$this->set('horses',$horses);
			$this->layout = 'ajax';	
		}	
	}

	function admin_data_pick($race_id){
		$horses = $this->Horse->find('all',array(
			'conditions'=>array('race_id'=>$race_id,'enable'=>1),
			'recursive' => -1, 'fields' => array('id','number','name')
		));
		//pr($horses);die();
		$hors = array();
		foreach ($horses as $h) {
			$hors[$h['Horse']['id']] = $h['Horse']['number'];
		}
		echo json_encode($hors);
		die();	
	}
	
	function admin_view($race_id){
		$horses = $this->Horse->find('all',array(
			'conditions'=>array('race_id'=>$race_id),
			'recursive' => -1, 'fields' => array('id','number','name','enable')
		));
		$this->set('horses',$horses);
		$this->layout = 'ajax';
	}
	
	function admin_delete($id){
		$horses_ticket = ClassRegistry::init('HorsesTicket');
		
		$by_horses = $horses_ticket->find('count',array('conditions' => array('horse_id' => $id)));
		
		if($by_horses > 0){
			$this->Session->setFlash("Caballo NO PUEDE SER BORRADO, ya tiene apuestas en el.");
		}else{
			if($this->Horse->del($id)){
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(5,$this->authUser['profile_id'],"Caballos",$id,"Caballo borrado");	
			
				$this->Session->setFlash("Caballo Borrado con exito.");
			}	
			else
				$this->Session->setFlash("Error borrando.");
		}
		
		$this->redirect($this->referer());	
	}

}
?>