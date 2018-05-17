<?php
class Profile extends AppModel {

	var $name = 'Profile';

	var $belongsTo = array('Center','User');
	
	var $validate = array(
		'name' => array(
			'rule'    => 'notEmpty',
            'message' => 'El Nombre es obligatorio.'

		)
	);
	
	
	function checkMyCenter($id,$center_logged){
		
		$profile = $this->find("first",array(
			'conditions'=>array('id'=>$id),'fields'=>'center_id','recursive'=>-1
		));
		if($profile!=null && $center_logged != $profile['Profile']['center_id'])
			return false;
		else
			return true;
	
	}
	
	function getNationalConf($id)
	{
		$myNational =   $this->find('first',array(
							'conditions' => array('Profile.id' => $id),
							'fields' => array('bet_tracks')
						));
						
	
		return $myNational['Profile']['bet_tracks'];
		
	}
    
    function getBalance( $id ) // , $centerId, $units, $intl = false 
    {
        
        $balance = $this->find('first',array(
                        'conditions' => array('id' => $id),
                        'fields'     => array('balance'),
                        'recursive'  => -1
                    ));
        
        
        return $balance['Profile']['balance'];
        
    }
    
    function getPlayers($centerId)
    {
        $profiles = $this->find('list',array(
 			'conditions' => array(
	 				'center_id'    => $centerId,
	 				'User.role_id' => array(3,4)
			),
			
 			'order'      => array('name'=>'ASC'),
            'recursive'  => 1
 		));
        
        return $profiles;
    }
    
    function getMyOnlines($centerId)
    {
        $profiles = $this->find('list',array(
 			'conditions' => array(
	 				'center_id'    => $centerId,
	 				'User.role_id' => 4
			),
			
 			'order'      => array('name'=>'ASC'),
            'recursive'  => 1
 		));
        
        return $profiles;
    }
    
    function isOnline($id) 
    {
        $profile = $this->find('first',array(
                        'conditions' => array(
                                'Profile.id'    => $id,
                        ),
                        'fields' => array('User.role_id'),
                        'recursive'  => 1
                    ));
        
        return ($profile['User']['role_id'] == 4);
    }
    
    function canReprint($id)
    {
        $profile = $this->find('first',array(
                        'conditions' => array('id' => $id ) ,
                        'fields'     => array('reprint_last'),
                        'recursive'  => -1
                    ));
        
        return ($profile['Profile']['reprint_last'] == 1);
    }
    
    
    function getConfigs($centerId)
    {
        $configs = $this->find('all',array(
                        'conditions' => array(
                                        'center_id'    => $centerId,
                                        'User.role_id' => array(3,4)),
                        'fields'     => array('Profile.id','name','User.role_id',
                                            'bet_tracks','reprint_last','anull_last','barcode','onl_perms')
                   ));
        
        return $configs;
    }
    
    
    function _pureMobile($number)
    {
        return str_replace('+58','0',$number);
    }
    
    function getByMobile($mobile)
    {
        $theNumber = $this->_pureMobile($mobile);
        //echo $theNumber;
        $profile = $this->find('first',array(
                    'conditions' => array(
                        'phone_number' => $theNumber ),
                    'recursive' => -1,
                    'fields'    => array('id','balance','center_id') ) );
        return $profile['Profile'];
    }
    
    
    function moveBalance($data)
    {
        $sign = "-";
        if ( $data['add'] == 1 ) {
            $sign = "+";
        }

        $this->updateAll(
            array('balance'    => "balance $sign ".$data['amount']),
            array('Profile.id' => $data['profile_id'] ) );
        
    }
}
?>