<?php
class HipodromesController extends AppController {

	var $name = 'Hipodromes';
	
	function beforeFilter(){
		parent::beforeFilter();
	}
	
	function isAuthorized(){
		
		$ret = false;
		
		$actions_root = array(
			"admin_index","admin_add","admin_edit","admin_enable"
		);
		
		if($this->isRoot() && in_array($this->action, $actions_root)){
			$ret = true;
		}else{
			$ret = false;
		}	
		
		return $ret;
	}

	function admin_index($nat = 2, $name = '') {
		
        $this->paginate['order'] = array('enable' => 'DESC', 'name' => 'ASC');
        
        $conds = array();
        
        if ($nat != 2) {
            $conds['national'] = $nat;
        }
        
        if ($name != '') {
            $conds["name LIKE"] = "%$name%";
        }
        
        $this->paginate['conditions'] = $conds;
        
        $this->set('hipodromes',$this->paginate());
        
		$this->set(compact('nat','name'));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Hipodrome->create();
			if ($this->Hipodrome->save($this->data)) {
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(3,$this->authUser['profile_id'],"Hipodromo",$this->Hipodrome->id,$this->data['Hipodrome']['name']." Creado");			
			
				$this->Session->setFlash("Hipodromo Guardado");
			} else {
				$this->Session->setFlash("Hipodromo NO Guardado");
			}
			$this->redirect($this->referer());
		}
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->flash("", array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Hipodrome->save($this->data)) {
					
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(3,$this->authUser['profile_id'],"Hipodromo",$this->data['Hipodrome']['id'],$this->data['Hipodrome']['name']." Editado");			
				
				$this->Session->setFlash("Hipodromo Guardado");
			} else {
				$this->Session->setFlash("Hipodromo NO Guardado");
			}
			$this->redirect($this->referer());
		}
		if (empty($this->data)) {
			$this->data = $this->Hipodrome->read(null, $id);
		}
	}
    
    function admin_enable($id,$stat)
    {
        $this->Hipodrome->updateAll(
            array('enable' => $stat),
            array('id'     => $id)
        );
        
        $this->redirect($this->referer());
    }
    
}
?>