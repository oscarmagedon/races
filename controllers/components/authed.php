<?php 
/**
 * Authed Component
 * 
 * Custom scope rules for AuthComponent
 * 
 * The purpose of this extension is to add a flexible rule setup to 
 * the login process. You could compare their setup to model validation.
 * The rules are applied just before the usual auth is issued. 
 * 
 * Note: This extension overwrites Auth::login()
 * 
 * Example: 
 * 
 *	$this->Authed->loginError = __("Wrong password / username. Please try again.", true);
 *	$this->Authed->authError = __("Sorry, you are not authorized. Please log in first.", true);
 * 
 *	$this->Authed->userScopeRules = array(
 *		'is_banned' => array(
 *			'expected' => 0, 
 *			'message' => __("You are banned from this service. Sorry.", true)
 *		),
 *		'is_validated' => array(
 *			'expected' => 1,
 *			'message' => __("Your account is not active yet. Click the Link in our Mail.", true)
 *		)
 *	);
 *
 * @author       Kjell Bublitz <m3nt0r.de@gmail.com>
 * @version      1.0
 * @package      app
 * @subpackage   app.app.controller.components
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 * 
 * PHP versions 4 and 5
 */
App::import('component', 'Auth');

/**
 * Authed Component 
 * 
 * @package		app
 * @subpackage	app.app.controller.components
 */
class AuthedComponent extends AuthComponent
{ 
	/**
	 * Rules
	 *
	 * @var array
	 */
	var $userScopeRules = array();
	
	/**
	 * Check variable
	 *
	 * @var boolean
	 */
	var $_scopeRuleError = false;
	
	/**
	 * Walk through all available rules and compare with row data.
	 * Break on mismatch and reset loginError to rule.message
	 *
	 * @param array $data UserModel row
	 * @return boolean True on login success, false on failure
	 * @access public
	 */
	function hasScopeRuleMismatch($user) {
		foreach ($this->userScopeRules as $field => $rule) {
			if ($user[$field] != $rule['expected']) {
				$this->loginError = $rule['message'];
				$this->_scopeRuleError = true;
				break;
			}
		}
		return $this->_scopeRuleError;
	}
	
	/**
	 * Overwrites Auth::login()user_
	 *
	 * Basicly the same method, but after identify() was successful call
	 * the above hasScopeRuleMismatch passing $user.
	 * 
	 * Only if this method returns false we will continue the login process.
	 * 
	 * @param mixed $data
	 * @return boolean True on login success, false on failure
	 * @access public
	 */
	function login($data = null) { 
		$this->__setDefaults();
		$this->_loggedIn = false;
		//$operInstance = ClassRegistry::init('Operation');
		if (empty($data)) {
			$data = $this->data;
		}
		//pr($data); die();
		if ( $user = $this->identify( $data ) ) {
            $operInst    = ClassRegistry::init('Operation');
            $profileInst = ClassRegistry::init('Profile');
            $profileData = $profileInst->find('first', array(
                                'conditions'=> array('user_id' => $user['id']),
                                'recursive' => -1 ) );
            
            //mantenimiento ON solo puede entrar si el centro es 1 o 2
            if ( SYS_MAINT == 1 && $profileData['Profile']['center_id'] > 2 ) {
                $this->_loggedIn = false;
                $this->loginError = 'Sistema en mantenimiento. Disculpe.'; 
            } else {
                if ( ! $this->hasScopeRuleMismatch ( $user ) ) {							
                    $this->_loggedIn = true;
                    $userData = array(
                            'id'           => $user['id'],
                            'role_id'      => $user['role_id'],
                            'center_id'    => $profileData['Profile']['center_id'],
                            'profile_id'   => $profileData['Profile']['id'],
                            'profile_name' => $profileData['Profile']['name'] );

                    $operInst->ins_op(1,$userData['profile_id'],"","","");	
                    $this->Session->write($this->sessionKey, $userData);
                }
            }
		}
		return $this->_loggedIn;
	}

	function logout(){
		if ( $this->user('profile_id') != null) {
            $signalInst = ClassRegistry::init('Signal');
            $operInst   = ClassRegistry::init('Operation');
            $signalInst->logoutSess($this->user('profile_id'));
    		$operInst->ins_op(2,$this->user('profile_id'),"","","");
        }
		
		return parent::logout();
	}
	
		
	
	/**
	 * Returns true if the login error was scope rules related.
	 * Maybe someone needs this to go on with.
	 * 
	 * @return boolean
	 */
	function wasScopeRuleError() {
		return $this->_scopeRuleError;
	}
	
}
?>