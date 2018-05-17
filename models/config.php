<?php
class Config extends AppModel {

	var $name = 'Config';
	
	var $belongsTo = array('ConfigType');
	
	function get_valid_ticket($center_id){
		$valid = 90;
		
		$valid_tick = $this->find('first',array(
			'conditions' => array(
				'center_id' => $center_id,
				'config_type_id' => 3,
				'actual' => 1
			),
			'recursive' => -1, 'fields' => 'amount'
		));
		
		if(!empty($valid_tick)){
			$valid = number_format($valid_tick['Config']['amount'],0);
		}
		
		return $valid;
	}
    
    function getUnitVals($centerId)
    {
        $configs = $this->find('all',array(
                        'conditions' => array(
                                            'center_id'      => $centerId,
                                            'config_type_id' => array(1,5),
                                            'actual'         => 1),
                        'recursive'  => -1, 
                        'fields'     => array('id', 'amount','config_type_id')
                    ));
        
        $cnfs = array();
        
        foreach ($configs as $conf) {
            $cnfs[$conf['Config']['config_type_id']] = array(
                                            'id'     => $conf['Config']['id'],
                                            'amount' => round($conf['Config']['amount'],0)
                                            );
        }
        
        return $cnfs;
    }
    
    function getPctVals ($centerId)
    {
        $configs = $this->find('all',array(
                        'conditions' => array(
                                            'center_id'      => $centerId,
                                            'config_type_id' => 4,
                                            'actual'         => 1),
                        'recursive'  => -1, 
                        'fields'     => array('id', 'amount','profile_id')
                    ));
        
        $cnfs = array();
        
        foreach ($configs as $conf) {
            $cnfs[$conf['Config']['id']] = array(
                                            'profile_id' => $conf['Config']['profile_id'],
                                            'amount'     => round($conf['Config']['amount'],1)
                                            );
        }
        
        return $cnfs;
    }
    
    function getLimitVals($centerId)
    {
        $configs = $this->find('all',array(
                        'conditions' => array(
                                            'center_id'      => $centerId,
                                            'config_type_id' => array(7, 8, 9),
                                            'actual'         => 1),
                        'recursive'  => -1, 
                        'fields'     => array('id','amount','config_type_id','profile_id')
                    ));
        
        $cnfs = array();
        
        foreach ($configs as $conf) {
            $cnfs[$conf['Config']['profile_id']][$conf['Config']['config_type_id']] = 
                                        array(
                                            'id'     => $conf['Config']['id'],
                                            'amount' => round($conf['Config']['amount'],1)
                                            );
        }
        
        return $cnfs;
    }
    
	function get_unit_value($centerId, $intern = false ){
		
        $valid    = 1;
        $confType = 1;
        
        if ($intern == true) {
            $confType = 5;
        }
        
		$config = $this->find('first',array(
			'conditions' => array(
                                'center_id'      => $centerId,
                                'config_type_id' => $confType,
                                'actual'         => 1),
			'recursive'  => -1, 
            'fields'     => 'amount'
		));
		
		if ( !empty ( $config ) ) {
			$valid = $config['Config']['amount'];
		}
		
		return $valid;
	}
		
	function get_pct_ticket($center_id){
        
		$valid = 1;
		
		$valid_tick = $this->find('first',array(
			'conditions' => array(
				'center_id' => $center_id,
				'config_type_id' => 2,
				'actual' => 1
			),
			'recursive' => -1, 'fields' => 'amount'
		));
		
		if(!empty($valid_tick)){
			$valid = $valid_tick['Config']['amount'];
		}
		
		return $valid;
	}
	
	function get_pct_profile($center_id){
		$valid_tick = $this->find('all',array(
			'conditions' => array(
				'center_id' => $center_id,
				'config_type_id' => 4,
				'actual' => 1
			),
			'recursive' => -1, 
			'fields' => array('amount','profile_id')
		));
		
		$pcts = array();
		
		foreach($valid_tick as $vt){
			$pcts[$vt['Config']['profile_id']] = $vt['Config']['amount'];
		}
		
		return $pcts;
	}
    
    function getPct($profileId){
		$validCnf = $this->find('first',array(
                        'conditions' => array(
                                'profile_id'     => $profileId,
                                'config_type_id' => 4,
                                'actual'         => 1
                        ),
                        'recursive' => -1, 
                        'fields' => array('amount')
                    ));

        $retVal = 0;
        
        if ( !empty ( $validCnf ) )
            $retVal = $validCnf['Config']['amount'];
        
		return $retVal;
	}
    
    function getLimitsProfile($profileId)
    {
        $limits = $this->find('list',array(
                        'conditions' => array(
                                'profile_id'     => $profileId,
                                'config_type_id' => array(7,8,9),
                                'actual'         => 1
                        ),
                        'recursive' => -1,
                        'fields' => array('config_type_id','amount')
                    ));
        
        

        return $limits;
    }
    
}

?>