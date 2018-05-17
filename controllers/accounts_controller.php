<?php
class AccountsController extends AppController {

	var $name = 'Accounts';
	
	function beforeFilter(){
		parent::beforeFilter();	
	}
	
	function isAuthorized(){
		
		$actions_adm = array("admin_index","admin_add");
		$actions_taq = array("admin_taquilla","admin_addtaq");
		$actions_onl = array("admin_my_index");
				
		if($this->isAdmin() && in_array($this->action, $actions_adm)){
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
	
	function admin_index($profileId = 0, $since = null, $until = null, $atitle = null)
    {
		$accounts = array();

		if($since == null){
			$since = date('Y-m-d'); $until = date('Y-m-d');
		}
		
		$this->paginate['order']      = array('created'=>'DESC');
		$this->paginate['recursive']  = 0;
		$this->paginate['limit']      = 50;
		$this->paginate['conditions'] = array(
                        'date(created) BETWEEN ? AND ?'=>array($since,$until));
		




		if ( $profileId != 0 ) {
            
            
            
            $this->paginate['conditions']['profile_id'] = $profileId;
        	
        	$totals = array(
                'baln' => $this->Account->Profile->getBalance($profileId), 
                'wins' => $this->Account->getTots($profileId,$since,$until,'PREMIO'), 
                'rels' => $this->Account->getTots($profileId,$since,$until,'RECARGA'),
                'bets' => $this->Account->getTots($profileId,$since,$until,'APUESTA'), 
                'rets' => $this->Account->getTots($profileId,$since,$until,'RETIROS'),
                'anul' => $this->Account->getTots($profileId,$since,$until,'ANULACION')
            );    
			
		} else {
			
			$myOnlines = $this->Account->Profile->getMyOnlines($this->authUser['center_id']);
			
			$profIds   = array_keys($myOnlines);
			
			$this->paginate['conditions']['profile_id'] = $profIds;

			$totals = array(
                'baln' => 0, 
                'wins' => $this->Account->getTots($profIds,$since,$until,'PREMIO'), 
                'rels' => $this->Account->getTots($profIds,$since,$until,'RECARGA'),
                'bets' => $this->Account->getTots($profIds,$since,$until,'APUESTA'), 
                'rets' => $this->Account->getTots($profIds,$since,$until,'RETIROS'),
                'anul' => $this->Account->getTots($profIds,$since,$until,'ANULACION')
            );
            
		}
        
		if ( $atitle != null ) {
            $this->paginate['conditions']['title'] = $atitle;
        }

        //pr($this->paginate['conditions']);

        


		$accounts = $this->paginate();

		$profiles = $this->Account->Profile->find('list',array(
                        'conditions' => array('User.role_id' => 4,
                                        'center_id' => $this->authUser['center_id']),
                        'order' => array('Profile.name'=>'ASC'),'recursive' => 2 ) );
		$titles   = $this->Account->getTitles();

		$this->set(compact('profileId','since','until','atitle',
                    'profiles','accounts','titles','totals'));
	}

	function admin_taquilla($profileId = 0, $since = null, $until = null, $atitle = null)
    {
        $myconfs = $this->Account->Profile->find('first',array(
			'conditions' => array('Profile.id' => $this->authUser['profile_id']),
			'fields' => array('onl_perms')
		));
		
		if($myconfs['Profile']['onl_perms'] == 0){
		    $this->Session->setFlash("Ud. no tiene permitido esta accion.");
			$this->redirect($this->referer());
		}
		
		if($since == null){
			$since = date('Y-m-d'); $until = date('Y-m-d');
		}
		
		$accounts = array();
		$this->paginate['order']      = array('created'=>'DESC');
		$this->paginate['recursive']  = 0;
		$this->paginate['limit']      = 50;
		$this->paginate['conditions'] = array(
                        'date(created) BETWEEN ? AND ?'=>array($since,$until));
		
		if ( $profileId != 0 ) {
            $totals = array(
                'baln' => $this->Account->Profile->getBalance($profileId), 
                'wins' => $this->Account->getTots($profileId,$since,$until,'PREMIO'), 
                'rels' => $this->Account->getTots($profileId,$since,$until,'RECARGA'),
                'bets' => $this->Account->getTots($profileId,$since,$until,'APUESTA'), 
                'rets' => $this->Account->getTots($profileId,$since,$until,'RETIROS'),
                'anul' => $this->Account->getTots($profileId,$since,$until,'ANULACION')
            );
            $this->paginate['conditions']['profile_id'] = $profileId;
            if ( $atitle != null ) {
                $this->paginate['conditions']['title'] = $atitle;
            }
			$accounts = $this->paginate();
		}
        
		$profiles = $this->Account->Profile->find('list',array(
                        'conditions' => array('User.role_id' => 4,
                                        'center_id' => $this->authUser['center_id']),
                        'order' => array('Profile.name'=>'ASC'),'recursive' => 2 ) );
		$titles   = $this->Account->getTitles();
		$this->set(compact('profileId','since','until','atitle',
                    'profiles','accounts','titles','totals'));
	}

	function admin_my_index($since = null, $until = null, $atitle = null ) 
    {
		if ( $since == null ) {
			$since = date('Y-m-d'); $until = date('Y-m-d');
		}
        
		$conds = array( 'date(created) BETWEEN ? AND ?' => array($since,$until),
                        'profile_id' => $this->authUser['profile_id']);
        
		$totals = array(
			'baln' => $this->Account->Profile->getBalance($this->authUser['profile_id']),
			'bets' => $this->Account->getTots($this->authUser['profile_id'],$since,$until,'APUESTA'), 
            'wins' => $this->Account->getTots($this->authUser['profile_id'],$since,$until,'PREMIO'), 
            'rels' => $this->Account->getTots($this->authUser['profile_id'],$since,$until,'RECARGA'),
            'rets' => $this->Account->getTots($this->authUser['profile_id'],$since,$until,'RETIRO'),
            'anul' => $this->Account->getTots($this->authUser['profile_id'],$since,$until,'ANULACION') );
		       
        if ( $atitle != null ) {
            $conds['title'] = $atitle;
        }
        
		$this->paginate['conditions'] = $conds;
		$this->paginate['order']      = array('created'=>'DESC');
		$this->paginate['recursive']  = 0;
		$this->paginate['limit']      = 50;
        
		$titles   = $this->Account->getTitles();
		$this->set('accounts',$this->paginate());
		$this->set(compact('since','until','atitle','totals','titles'));
	}

	function admin_add($pid = null) {
		if ( ! empty ( $this->data ) ) {
			//pr($this->data); die();
			//new function to add movement
            $flsh = $this->Account->addMovem($this->data['Account']);
            $this->Session->setFlash($flsh);
			$this->redirect($this->referer());
		}
        $titles = $this->Account->getTitles(1);
		
		$this->set(compact('pid','titles'));
	}

	function admin_addtaq($pid = null) {
		if ( ! empty ( $this->data ) ) {
			//pr($this->data); die();
			//new function to add movement
            $flsh = $this->Account->addMovem($this->data['Account']);
            $this->Session->setFlash($flsh);
			$this->redirect($this->referer());
		}
		
		$myconfs = $this->Account->Profile->find('first',array(
			'conditions' => array('Profile.id' => $this->authUser['profile_id']),
			'fields' => array('onl_perms')
		));
		
		if($myconfs['Profile']['onl_perms'] == 0){
		    $this->Session->setFlash("Ud. no tiene permitido esta accion.");
			$this->redirect($this->referer());
		}
		
        $titles = $this->Account->getTitles(1);
		
		$this->set(compact('pid','titles'));
	}
}