<?php
class UsersController extends AppController {

	var $name    = 'Users';
	var $helpers = array('Html', 'Form','Dtime');
	var $uses    = array('User','Profile');

	function beforeFilter(){
		parent::beforeFilter();	
		
		$this->Authed->allow(array("login","logout"));
	}
	
	function isAuthorized(){
		
		$ret = true;
		
		$actions_root = array(
			"admin_set_enable","admin_set_unlog"
		);
        $actions_adm = array(
            "admin_set_enable_center","admin_set_unlog_center"
        );

        $actions_onl = array(
            'admin_signal','admin_hashsignal'
        );
		
		if($this->isRoot() && in_array($this->action, $actions_root)){
			$ret = true;
		}elseif($this->isAdmin() && in_array($this->action, $actions_adm)){
            $ret = true;
        }elseif($this->isOnline() && in_array($this->action, $actions_onl)){
            $ret = true;
        }else{
			$ret = false;
		}
		
		return $ret;
	}
	
	function login(){
  	}
	
	function logout(){
		$this->redirect($this->Authed->logout());
	}

	function admin_set_enable($id,$status) {
			
		if ($this->authUser['id'] == $id) {
			$this->Session->setFlash("No puede auto desactivarse.");
		}else{
			$this->User->updateAll(array('enable'=>$status),array('User.id'=>$id));
			$pr = $this->Profile->find('first',array(
				'conditions' => array('user_id'=>$id),
				'fields'=>'Profile.name'
			));
			
			$hab = "Habilitar";
			if($status == 0)
				$hab = "Deshabilitar";
			
			$operInst = ClassRegistry::init('Operation');
			$operInst->ins_op(4,$this->authUser['profile_id'],"Usuario",$id,$hab." ".$pr['Profile']['name']);
		}
		
		$this->redirect($this->referer());
				
		
	}
	
	function admin_set_unlog($id) {
		$this->User->updateAll(array('logged'=>0),array('User.id'=>$id));
		$this->redirect($this->referer());
	}
	
	function admin_set_enable_center($id,$status) {
		if(!($this->Profile->checkMyCenter($id,$this->authUser['center_id']))){
			$this->Session->setFlash("El Usuario No es uno de los suyos");
			$this->redirect($this->referer());
		}
		
		$pr = $this->Profile->find('first',array(
			'conditions' => array('user_id'=>$id),
			'fields'=>'Profile.name'
		));
		
		$hab = "Habilitar";
		if($status == 0)
			$hab = "Deshabilitar";
		
		$operInst = ClassRegistry::init('Operation');
		$operInst->ins_op(4,$this->authUser['profile_id'],"Usuario",$id,$hab." ".$pr['Profile']['name']);
		
		$this->User->updateAll(array('enable'=>$status),array('User.id'=>$id));
		$this->redirect($this->referer());
	}
	
	function admin_set_unlog_center($id) {
		if(!($this->Profile->checkMyCenter($id,$this->authUser['center_id']))){
			$this->Session->setFlash("El Usuario No es uno de los suyos");
			$this->redirect($this->referer());
		}
		$this->User->updateAll(array('logged'=>0),array('User.id'=>$id));
		$this->redirect($this->referer());
	}

    function admin_signal()
    {
		//verifyLastIP
		$this->exitOnIPDifference();

    }

    /* SIGNAL REDIRECTION */
    function admin_hashsignal($type) {
    	
        // SIGNAL REGISTER AND VERIFY
        $signalInst  = ClassRegistry::init("Signal");

		$timeNow     = date("Y-m-d H:i:s");
		
		$deadline    = 7200; // 2 hours in secs

		$sess = $signalInst->find('first',array(
				'conditions' => array(
					'profile_id'      => $this->authUser['profile_id'],
					'ip_address'      => $this->_getUserIP(),
					'DATE(created)'   => date("Y-m-d"),
					'enable'          => 1
				)
		));
		
		$createNew = true;
		
		if (!empty($sess)) {
				
			$timeInSess = strtotime($timeNow) - strtotime($sess['Signal']['created']); 
			
			if ($timeInSess <= $deadline){
				$hashedSess = $sess['Signal']['hashed_sess'];
				$createNew  = false;
			}
				
		}
		
		if ($createNew) {
			$signalInst->updateAll(
							array('enable'     => 0),
							array('profile_id' => $this->authUser['profile_id'])
			);
			
			$hashedSess = strtoupper(substr(md5(rand(99,777)),0,10));
			$signalSave = array(
	                'profile_id'  => $this->authUser['profile_id'],
	                'ip_address'  => $this->_getUserIP(),
	                'hashed_sess' => $hashedSess
	        );
	        $signalInst->create();
	        $signalInst->save($signalSave);
		}
		
        /*/
        $baseUrl    = 'http://localhost/cakephp-1.2/signal/channels/watch/';
        /*/
        $baseUrl    = 'http://vivo.totalraces.com/channels/watch/';
        //*/
        

        //die("ENDED --->");
        $this->redirect($baseUrl . $type . '/' . $hashedSess);

    }

}
?>