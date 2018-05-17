<?php
class ProfilesController extends AppController {

	var $name = 'Profiles';

	function beforeFilter(){
		parent::beforeFilter();
	}
	
	function isAuthorized(){
		
		$ret = true;
		
		$actions_root = array(
			"admin_index","admin_add","admin_edit","admin_change_pass","admin_create_all",
            "admin_generatepin"
		);
		
		$actions_adm = array(
			"admin_index_center","admin_edit_center","admin_change_pass_center","admin_confs_adv",
			"admin_generatepin","admin_add_online"
		);
		
		$actions_taq = array(
			"admin_view_taquilla"
		);
        
        $actions_onl = array(
			"admin_viewonl"
		);
		
		if($this->isRoot() && in_array($this->action, $actions_root)){
			$ret = true;
		}elseif($this->isAdmin() && in_array($this->action, $actions_adm)){
			$ret = true;
		}elseif($this->isTaquilla() && in_array($this->action, $actions_taq)){
			$ret = true;	
		}elseif($this->isOnline() && in_array($this->action, $actions_onl)){
			$ret = true;	
		}else{
			$ret = false;
		}
		
		
		return $ret;
	}
	
	function admin_index($role_id = 0, $enable = 2, $center_id = 0, $user_like = "") {
		
		$conditions = array();
		
		if($role_id != 0)
			$conditions['User.role_id'] = $role_id;
		
		if($enable != 2)
			$conditions['User.enable'] = $enable;
		
		if($center_id != 0)
			$conditions['Center.id'] = $center_id;
				
		
		if($user_like != ""){
			$conditions['OR'] = array(
				'Profile.name LIKE' => "%$user_like%",
				'User.username LIKE' => "%$user_like%"
			);
			
		}
		
		$this->Profile->recursive = 1;
		
		$this->Profile->unbindModel(array('belongsTo'=>array('User','Center')),false);
		
		$this->Profile->bindModel(array('belongsTo'=>array(
			'User' => array(
				'fields' => array('created','username','enable','role_id')
			),
			'Center' => array(
				'fields' => array('name')
			)
		)),false);
		
		$this->paginate['conditions'] = $conditions;
				
		$roles = $this->Profile->User->Role->find('list');
		$centers = $this->Profile->Center->find('list');
		$enables = array(2 =>"Todos", 0 => "No Disp.", 1 => "Disponibles");
		
		$this->set(compact('roles','enables','centers'));
		$this->set('role_id',$role_id);
		$this->set('enable',$enable);
		$this->set('center_id',$center_id);
		$this->set('user_like',$user_like);
		$this->set('profiles', $this->paginate());
	}
	
	function admin_index_center($role_id = 0, $enable = 2, $user_like = "none") {
		
		$conditions['Center.id'] = $this->authUser['center_id'];
		
		if($role_id != 0)
			$conditions['User.role_id'] = $role_id;
		
		if($enable != 2)
			$conditions['User.enable'] = $enable;
		
		if($user_like != "none"){
			$conditions['OR'] = array(
				'Profile.name LIKE' => "%$user_like%",
				'User.username LIKE' => "%$user_like%"
			);
			
		}else
			$user_like = "";
		
		
		$this->Profile->recursive = 1;
		
		$this->Profile->unbindModel(array('belongsTo'=>array('User','Center')),false);
		
		$this->Profile->bindModel(array('belongsTo'=>array(
			'User' => array(
				'fields' => array('created','username','enable','role_id')
			),
			'Center' => array(
				'fields' => array('name')
			)
		)),false);
		
		$this->paginate['conditions'] = $conditions;
				
		$roles = $this->Profile->User->Role->find('list',array('conditions'=>"id<>1"));
		
		$enables = array(2 =>"Todos", 0 => "No Disp.", 1 => "Disponibles");
		
		$this->set(compact('roles','enables'));
		$this->set('role_id',$role_id);
		$this->set('enable',$enable);
		$this->set('user_like',$user_like);
		$this->set('profiles', $this->paginate());
	}
	
	function admin_add($center_id = null) {				
		if (!empty($this->data)) {
			unset($this->data['User']['repassword']);
			$this->data['User']['enable'] = 1;
			$this->data['User']['logged'] = 0;
			//pr($this->data); die();
			$this->Profile->User->create();
			if($this->Profile->User->save($this->data['User'])){
				$this->data['Profile']['user_id'] = $this->Profile->User->id;
				$this->Profile->create();
				if ($this->Profile->save($this->data['Profile'])) {
					$operInst = ClassRegistry::init('Operation');
					$operInst->ins_op(3,$this->authUser['profile_id'],"Usuario",$this->Profile->id,"Usuario ".$this->data['Profile']['name']." Creado");			
			
					$this->Session->setFlash("Usuario Salvado");
				} else {
					$this->Session->setFlash("ERROR: Usuario NO salvado");
				}	
			}else{
				$this->Session->setFlash("ERROR: Usuario NO salvado");
			}
			
			$this->redirect($this->referer());
		}
		
		$roles = $this->Profile->User->Role->find('list',array('conditions'=>"id<>1"));
	
		$this->set('center_id',$center_id);
		$this->set('roles',$roles);
	}
	
	function admin_create_all(){		
		if(!empty($this->data)){
			//pr($this->data); die();
			if ($this->Profile->User->exist_user($this->data['Profs'][0]['User']['username']) === false) {
				$allOk = true;
				$this->Profile->Center->create();
				$this->Profile->Center->save($this->data['Center']);
				$cid = $this->Profile->Center->id;
				$this->Profile->create();
				$this->Profile->User->create();		
					
				foreach ($this->data['Profs'] as $p) {
					if ($p['User']['username'] != '') {
						if ($this->Profile->User->save($p['User'])) {
							$p['Profile']['user_id']   = $this->Profile->User->id;
							$p['Profile']['center_id'] = $cid;
							
							if ($this->Profile->save($p['Profile'])) {
								unset($this->Profile->User->id);
								unset($this->Profile->id);	
							} else {
								$allOk = false;
							}				
						}else{
							$allOk = false;
						}
					}	
				}
				
				$errs = "";
				
				if (!$allOk) {
					$errs = " (con errores).";
				}
				
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(3,$this->authUser['profile_id'],"Usuario",$cid,"Centro ".$this->data['Center']['name']." Completo Creado $errs");			
				$this->Session->setFlash("Centro y Usuarios Salvados $errs");	
			}else{
				$this->Session->setFlash("Centro NO PUDO SER CREADO. Nombre de usuario admin ya existe.");
			}
			
			$this->redirect(array('action'=>'index'));
		}
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash("Error Usuario");
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Profile->saveAll($this->data)) {
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(4,$this->authUser['profile_id'],"Usuario",$this->data['Profile']['id'],"Usuario ".$this->data['Profile']['name']." Editado");			
				$this->Session->setFlash("Usuario Salvado");
			} else {
				$this->Session->setFlash("ERROR: Usuario NO Salvado");
			}
			$this->redirect(array('action'=>'index'));
		}
		if (empty($this->data)) {
			$this->data = $this->Profile->read(null, $id);
		}
		$roles = $this->Profile->User->Role->find('list');
		$this->set(compact('roles'));
	}
	
	function admin_edit_center($id = null) {
		
		if(!($this->Profile->checkMyCenter($id,$this->authUser['center_id']))){
			die("El Usuario No es uno de los suyos");
		}
		
		if (!empty($this->data)) {
			if ($this->Profile->saveAll($this->data)) {
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(4,$this->authUser['profile_id'],"Usuario",$this->data['Profile']['id'],"Usuario ".$this->data['Profile']['name']." Editado");			
				
				$this->Session->setFlash("Usuario Salvado");
				$this->redirect(array('action'=>'index_center'));
			} else {
				$this->Session->setFlash("ERROR: Usuario NO Salvado");
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Profile->read(null, $id);
		}
		
		$role = $this->Profile->User->Role->find('first',array('conditions'=>"id=".$this->data['User']['role_id']));
		$this->set('role',$role['Role']['name']);
		$this->set('username',$this->data['User']['username']);
	}

	function admin_change_pass($id = null) {

		if (!$id && empty($this->data)) {
			$this->Session->setFlash("Usuario Invalido");
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			//pr($this->data);die();
			if ($this->Profile->User->updateAll(
				array('User.password' => "'".$this->Authed->password($this->data['Profile']['new_pass'])."'"),
				array('User.id' => $this->data['Profile']['user_id'])
			)) {
				
				$pr = $this->Profile->find('first',array(
					'conditions' => array('user_id'=>$this->data['Profile']['user_id']),
					'fields'=>'Profile.name'
				));				
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(4,$this->authUser['profile_id'],"Usuario",$this->data['Profile']['user_id'],"Cambio Pass ".$pr['Profile']['name']);	
		
				$this->Session->setFlash("Password Cambiado");
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash("ERROR: Password NO Cambiado");
			}
		}
		if (empty($this->data)) {
			$user_id = $this->Profile->find('first',array(
				'conditions' => array('Profile.id'=>$id),
				'fields' => 'user_id', 'recursive' => -1
			));
			$this->set('user_id',$user_id['Profile']['user_id']);
		}
		
	}
	
	function admin_change_pass_center($id = null) {
		if(!($this->Profile->checkMyCenter($id,$this->authUser['center_id']))){
			die("El Usuario No es uno de los suyos");
		}
		
		if (!$id && empty($this->data)) {
			$this->Session->setFlash("Usuario Invalido");
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			//pr($this->data);die();
			if ($this->Profile->User->updateAll(
				array('User.password' => "'".$this->Authed->password($this->data['Profile']['new_pass'])."'"),
				array('User.id' => $this->data['Profile']['user_id'])
			)) {
				
				$pr = $this->Profile->find('first',array(
					'conditions' => array('user_id'=>$this->data['Profile']['user_id']),
					'fields'=>'Profile.name'
				));				
				$operInst = ClassRegistry::init('Operation');
				$operInst->ins_op(4,$this->authUser['profile_id'],"Usuario",$this->data['Profile']['user_id'],"Cambio Pass ".$pr['Profile']['name']);	
		
				
				$this->Session->setFlash("Password Cambiado");
				$this->redirect(array('action'=>'index_center'));
			} else {
				$this->Session->setFlash("ERROR: Password NO Cambiado");
			}
		}
		if (empty($this->data)) {
			$user_id = $this->Profile->find('first',array(
				'conditions' => array('Profile.id'=>$id),
				'fields' => 'user_id', 'recursive' => -1
			));
			$this->set('user_id',$user_id['Profile']['user_id']);
		}
		
	}

	function admin_add_online() 
	{				
		if ( !empty ( $this->data ) ) {

			unset($this->data['User']['repassword']);
			$this->data['User']['enable']       = 1;
			$this->data['User']['logged']       = 0;
			$this->data['User']['role_id']      = 4;
			$this->data['Profile']['center_id'] = $this->authUser['center_id'];
			//pr($this->data); die();
			
			$this->Profile->User->create();

			if( $this->Profile->User->save($this->data['User'] ) ) {
				
				$this->data['Profile']['user_id'] = $this->Profile->User->id;
				
				$this->Profile->create();
				
				if ($this->Profile->save($this->data['Profile'])) {
					$operInst = ClassRegistry::init('Operation');
					$operInst->ins_op(3,$this->authUser['profile_id'],"Usuario",$this->Profile->id,"Usuario ".$this->data['Profile']['name']." Creado");			
			
					$this->Session->setFlash("Usuario Salvado");
				} else {
					$this->Session->setFlash("ERROR: Usuario NO salvado");
				}	
			}else{
				$this->Session->setFlash("ERROR: Usuario NO salvado");
			}
			
			$this->redirect($this->referer());
		}
		
	}

	
	function admin_view_taquilla()
    {
        $id = $this->authUser['profile_id'];
        
		$race_ins = ClassRegistry::init('Race');
		$rest_ins = ClassRegistry::init('Restriction');
		
		$profile = $this->Profile->find("first",array('conditions'=>array("Profile.id"=>$id)));
		
		$races_id = $race_ins->find('list',array(
			'fields'=>'id','conditions'=>array('race_date' => date('Y-m-d'),'center_id' => $this->authUser['center_id'])
		));
		
		$rest_ins->bindModel(array('belongsTo'=>array(
			'Race'=>array('fields'=>array('Race.number','Race.race_time','Race.hipodrome_id')))
		));
		
		$restr = $rest_ins->find('all',array(
			'conditions'=>array('profile_id'=>$id,'race_id'=>$races_id),
			'fields' => array('play_type_id','race_id'),
			'recursive' => 2,'order'=>array('Race.hipodrome_id'=>"DESC")
		));
		
		$restrictions = array();
		foreach($restr as $r){
			if(!empty($restrictions[$r['Race']['Hipodrome']['name']][$r['Race']['number']]))
				array_push($restrictions[$r['Race']['Hipodrome']['name']][$r['Race']['number']],$r['Restriction']['play_type_id']);
			else
				$restrictions[$r['Race']['Hipodrome']['name']][$r['Race']['number']] = array(0 => $r['Restriction']['play_type_id']);
		}
		
		$this->set('profile',$profile);
		$this->set('restrictions',$restrictions);
	}
    
    function admin_viewonl()
    {
    
        if (!empty($this->data)) {
			//pr($this->data);die();
			$this->Profile->User->updateAll(
				array('User.password' => "'".$this->Authed->password($this->data['Profile']['new_pass'])."'"),
				array('User.id' => $this->data['Profile']['user_id'])
			);
				
            $operInst = ClassRegistry::init('Operation');
            $operInst->ins_op(4,$this->authUser['profile_id'],"Usuario",$this->data['Profile']['user_id'],"Cambio Propio Pass ");	


            $this->Session->setFlash("Password Cambiado");
            $this->redirect($this->referer());
			
		}
        
        $id       = $this->authUser['profile_id'];
        
		$profile  = $this->Profile->find("first",array('conditions'=>array("Profile.id"=>$id)));
	
		$this->set('profile',$profile);
	}
	
	function admin_confs_adv(){
		if(!empty($this->data)){
			//pr($this->data); die();
			foreach($this->data['Profile'] as $k => $v) {
				
                if ( !isset ($v['reprint_last']) ) {
                    $v['reprint_last'] = 0;
                }
                
                if (!isset($v['barcode'])) {
                    $v['barcode'] = 0;
                }

                if (!isset($v['onl_perms'])) {
                    $v['onl_perms'] = 0;
                }

                $this->Profile->updateAll(
					array(
						'bet_tracks'   => $v['bet_tracks'],
						'anull_last'   => $v['anull_last'],
						'reprint_last' => $v['reprint_last'],
						'barcode'      => $v['barcode'],
						'onl_perms'    => $v['onl_perms']
					),
					array('Profile.id' => $k)
				);
			}
			
			$operInst = ClassRegistry::init('Operation');
			$operInst->ins_op(4,$this->authUser['profile_id'],"Configuracion","","Restricciones Esp. ");	
		
			$this->Session->setFlash("Configuraciones Salvadas");
			$this->redirect($this->referer());
		}
	}
    
    function admin_generatepin ()
    {
        //$except = array(1234,4567,8910); 
        //tomar de los pins que ya existen
        $autopins = $this->Profile->find('list',array(
                    'conditions' => array('autopin <>' =>0),
                    'fields' => 'autopin'
                ));
        do {
            $random = rand(1000,9999);
        } while ( in_array($random,$autopins));
        
        die ( json_encode ( array ( 'pin' => $random ) ) ) ;
    }
}
?>