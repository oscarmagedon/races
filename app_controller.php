<?php 
class AppController extends Controller {
    
    var $components = array('Authed','Session','RequestHandler');
	
    var $helpers    = array('Html','Form','Session','Javascript','Dtime','Barcode');
	
	var $paginate   = array('limit' => 30);
    
    var $systemOn   = 1;
	
	function beforeFilter(){
		
		$this->Authed->fields          = array('username' => 'username', 'password' => 'password');
		$this->Authed->loginAction     = array('controller' => 'users', 'action' => 'login','admin'=>0);
		$this->Authed->logoutRedirect  = array('controller' => 'pages');
		$this->Authed->authorize       = 'controller';
		$this->Authed->loginError      = 'Combinacion login/password no valida';
		$this->Authed->authError       = 'Direccion negada por el sistema.';
		$this->Authed->userScopeRules       = array(
                                            'enable' => array(
                                                'expected' => 1,
                                                'message'  => 'Usuario desactivado, revise su email para detalles.'
                                            )
                                        );
		$this->Authed->allow('display');
		$this->disableCache();
			
		$this->authUsr  = $this->Authed->user();
		$this->authUser = $this->authUsr['User'];
		
        
        
		if ($this->authUser != null) {
        	$this->set("authUser", $this->authUser);				
        }
		
        //SPECIAL URLS
        if ($this->_isSpecialUrl($this->params['controller'],$this->params['action'])) {
            //echo 'special';
            $this->exitOnIPDifference();
            
            //echo $this->params['action'];
            //OUT if mainteinance ON
            if ( SYS_MAINT == 1 && $this->params['action'] != 'logout' && 
                $this->authUser['center_id'] < 2) {
                //echo $this->params['action'];
                $this->Session->setFlash("Sistema en mantenimiento. Disculpe.");
                $this->redirect(array('controller'=>'users','action'=>'logout','admin'=>0));
            }
        }
        
        
		//pr($this->params);
		
        if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'htracks_list') {
			$hipoInst = ClassRegistry::init("Hipodrome");
			$htracks  = $hipoInst->find('list',array('order' => array('name' => 'ASC')));
			$this->set(compact('htracks'));
		}
        
        /** NEW QUERIES 
    
        INSERT INTO `totalhipico`.`roles` (`id`, `name`) VALUES (NULL, 'AUTOTAQ');
        ALTER TABLE `profiles` ADD `autopin` INT(4) NOT NULL DEFAULT '0' AFTER `phone_number`;
        ALTER TABLE `tickets` ADD `via` ENUM('TAQ', 'AUTO','SMS','ONL') 
          NOT NULL DEFAULT 'TAQ' AFTER `play_type_id`;
         */
		
	}
	
	function isAuthorized() {
		return true;
	}
	
	function isRoot(){
		return ($this->Authed->user('role_id') == ROLE_ROOT);
	}
	function isAdmin(){
		return ($this->Authed->user('role_id') == ROLE_ADMIN);
	}
	function isTaquilla(){
		return ($this->Authed->user('role_id') == ROLE_TAQUILLA);
	}
	function isOnline(){
		return ($this->Authed->user('role_id') == ROLE_ONLINE);
	}
        function isAuto(){
		return ($this->Authed->user('role_id') == ROLE_AUTO);
	}
	
        function verifyLastIP()
	{
		$operInst = ClassRegistry::init("Operation");
		$lastIP   = $operInst->getLastLoginIP($this->Authed->user('profile_id'));
		$nowIP    = $operInst->getuserIP();
		$valid    = true;
		
		if ( $lastIP != $nowIP ) {
			$valid = false;
		}
		
		return $valid;
	}
	
	function exitOnIPDifference()
	{
		if ($this->verifyLastIP() === false) {
			$this->Session->setFlash("Su usuario inicio sesion en otra Dir. IP.");
			$this->redirect(array('controller'=>'users','action'=>'logout','admin'=>0));
		}
	}
	
    function _getUserIP()
    {
    	$operInst = ClassRegistry::init("Operation");
    	return $operInst->getuserIP();
    }
	
	function _hourInfo($diff)
	{
		$nowTime = date("H:i:s");
		$altHour = 0;
	
		if ($diff != 0) {
			$altHour = date("H:i:s",strtotime("$diff hour"));
		}
		 
		return array('regular'=>$nowTime,'alternate'=>$altHour);
	}
	
    function _isSpecialUrl ( $contr, $action )
    {
        $specials = array(  'tickets' => array(
                                        'admin_index','admin_sales',
                                        'admin_add','admin_taquilla','admin_salestaq',
                                        'admin_pay','admin_paybarc'),
                            'profiles' => array(
                                        'view_taquilla'
                            ));
        $denied   = false;
        foreach ( $specials as $cntr => $actions ) {
            if ( $contr == $cntr ) {
                foreach ($actions as $act) {
                    if ($action == $act) {
                        $denied = true;
                        break;
                    }
                }
            }
        }
        
        return $denied;
    }
    /*
    function _getMenuActions($role)
    {
        switch ($role) {
            case ROLE_ROOT:
                $theMenu = $this->User->menuRoot;
                break;
            case ROLE_ADMIN:
                $theMenu = $this->User->menuCenter;
                break;
            case ROLE_ONLINE:
                $theMenu = $this->User->menuOnline;
                break;
            case ROLE_AUTO:
                $theMenu = $this->User->menuAutotaq;
                break;
            default:
                $theMenu = $this->User->menuTaq;
                break;
        }
        
        return $theMenu;
    }
	*/
    
}
?>