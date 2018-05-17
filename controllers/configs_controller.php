<?php
class ConfigsController extends AppController {

	var $name = 'Configs';

	function beforeFilter(){
		parent::beforeFilter();	
	}
	
	function isAuthorized(){
		
		$ret = true;
		
		$actions_root = array(
			"admin_main","admin_winter"
		);
        
        $actions_adm = array(
			"admin_add","admin_edit","admin_setvalues","admin_delete"
		);
		
		if ($this->isRoot() && in_array($this->action, $actions_root)) {
			$ret = true;	
		} elseif ($this->isAdmin() && in_array($this->action, $actions_adm)) {
			$ret = true;	
		} else {
			$ret = false;
		}
				
		if($ret == false)
			$this->Session->setFlash("Direccion NO permitida");
		
		return $ret;
	}
    
    function admin_main()
    {
        $confs = $this->Config->find('first',
                        array('conditions' => 'center_id = 1'));
        
        //pr($confs); die();
        $this->set(compact('confs'));
    }
	
    function admin_winter($setTo) 
    {
        $this->Config->updateAll(
            array('actual'         => $setTo),
            array('config_type_id' => 6)
        );
        
        $this->redirect($this->referer());
	}
    
    //INSERT INTO `totalhipico`.`config_types` (`id`, `name`) 
    //VALUES (NULL, 'Maximo Monto Ticket'), (NULL, 'Maximo Monto Caballo'); 
	function admin_add() {
		if (!empty($this->data)) {
			$this->data['Config']['center_id'] = $this->authUser['center_id'];
			//pr($this->data);die();
			$this->Config->create();
			if ($this->Config->save($this->data)) {
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(3,$this->authUser['profile_id'],"Configuracion",
                    $this->Config->id,"Nueva conf");	
				
				$this->Session->setFlash("Configuracion Guardada.");
			} else {
				$this->Session->setFlash("ERROR: Configuracion NO Guardada.");
			}
			$this->redirect($this->referer());
		}
		
		$profs_ins = ClassRegistry::init('Profile');
		
		$profs = $profs_ins->find('all',array(
                    'conditions' => array('center_id' => $this->authUser['center_id'],
                                            'User.role_id'=> array(3,4)),
                    'fields' => array('Profile.name','Profile.id')
                ));
		
		$profiles = array(0 => "Todos");
        
 		foreach($profs as $pro){
 			$profiles[$pro['Profile']['id']] = $pro['Profile']['name'];
 		}
		
		$conf_types = $this->Config->ConfigType->find('list',
                        array('conditions' => 'id != 6'));
		
		$this->set('conf_types',$conf_types);
		
        $this->set('profiles',$profiles);
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->flash(__('Invalid Unit', true), array('action'=>'index'));
		}
		if (!empty($this->data)) {
			//pr($this->data); die();
			if ($this->Config->save($this->data)) {
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(4,$this->authUser['profile_id'],"Configuracion",$this->data['Config']['id'],"Cambio");	
					
				$this->Session->setFlash("Configuracion Editada.");
				$this->redirect($this->referer());
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Config->read(null, $id);
		}
	}
    
    function admin_setvalues()
    {
        if (!empty($this->data)) {
			
            //pr($this->data); die();
            
            if (isset($this->data['Config']['new'])) {
                if (  $this->data['Config']['new']['profile_id'] == 0 || 
                    $this->data['Config']['new']['amount'] == "") {

                    unset($this->data['Config']['new']);
                } else {
                    $this->data['Config']['new']['center_id'] = $this->data['Center']['id'];
                    $this->data['Config']['new']['from']      = date('Y-m-d');
                    $this->Config->save($this->data['Config']['new']);
                    unset($this->Config->id);
                    unset($this->data['Config']['new']);
                }
            }
            
            
            foreach ($this->data['Config'] as $key => $config) {
                
                if ( $config['amount'] != "" ) {
                     
                    if ( ! isset ( $config['center_id'] ) ) {
                        $config['center_id'] = $this->data['Center']['id'];
                    }

                    if ( ! isset ( $config['config_type_id'] ) && 
                         ! isset ( $config['id'] )) {
                        $config['config_type_id'] = $key;
                    }
                    
                    $this->Config->save($config);
                    unset($this->Config->id);
                    //pr($config);
                }
                
                
                
            }
            //die();
            
            $this->Session->setFlash('Valores Cambiados');
            $this->redirect($this->referer());
        }
    }

    function admin_delete($id)
    {
        $this->Config->delete(array('Config.id'=>$id));
        $this->Session->setFlash('Config. Borrada');
        $this->redirect($this->referer());
    }
}
?>