<?php
class User extends AppModel {

	var $name = 'User';
	
	var $belongsTo = array('Role');
	
	
	/*
	function logon($id,$ipaddress){
		$this->save(array('id'=>$id,'logged'=> 1,'last_login'=>date('Y-m-d h:i:s'),'ip_last_login'=>$ipaddress));
	}
	 
	function logoff($id){
		if(!empty($id)){
			$this->updateAll(array('logged' => 0),array('User.id' => $id));
		}
	}
	*/
	
	
	var $validate = array(
		'username' => array(
				'UserNotEmpty' => array(
				            'rule'    => 'notEmpty',
				            'message' => 'El usuario es obligatorio.',
				            'last'    => true
		        ),
		        
		        'UserMinLength' => array(
				            'rule'    => array('minLength', 6),
				            'message' => 'El usuario debe tener 6 letras minimo.'
		        ),
		        
				'UsernameTypo' => array(
					        'rule'    => 'alphaNumeric',
					        'message' => 'Usernames must only contain letters and numbers.'
			    )
		)
	);
	
	public function beforeSave($options = array()) {
    	if (isset($this->data[$this->alias]['password'])) {
        	$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
    	}
    	return true;
	}
	
	function exist_user($username) 
	{
		$exists = $this->find('count',array(
			'conditions' => array( 'username' => $username )
		));
		
		if ($exists > 0){
			return true;
		}else{
			return false;
		}
	}
	
}
?>