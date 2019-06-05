<?php
class Interval extends AppModel {

	var $name      = 'Interval',
        
        $belongsTo = array('Hipodrome' => array('fields' => array('name')));

	function getMyInts($cid, $byHrs = 0)
	{
        $myInts = array();
        
        $intervals = $this->find('all',array(
                        'conditions' => array('center_id' => $cid, 'byHorses' => $byHrs),
                        'order'      => array('hipodrome_id','byHorses','val_from')));
        $i = 0;
        foreach ( $intervals as $int ) {
            $myInts[$int['Interval']['hipodrome_id']][$int['Interval']['byHorses']][$i] = 
                                    array(
                                        'id'    => $int['Interval']['id'],
                                        'vfrom' => $int['Interval']['val_from'],
                                        'vto'   => $int['Interval']['val_to'],
                                        'add'   => $int['Interval']['div_add'],
                                        'amo'   => $int['Interval']['amount']
                                    );
            $i ++;
        }
        
        return $myInts;
	}
    
    function getByHtrack($cid,$hid,$byHrs = 0 )
    {
        $intervals = $this->find('all',array(
                            'conditions' => array("hipodrome_id" => $hid,
                                                  'center_id'    => $cid,
                                                  'byHorses'     => $byHrs),
                            'fields'     => array('id','val_from','val_to',
                                                'div_add','amount'),
                            'order'      => array('val_from' => 'ASC')
                     ));
        
        return $intervals;
        
    }

    public function getCleanByHipo( $centerId, $htrackid, $lastRiders)
    {
        $intervals = $this->find('all',array(
                            'conditions' => array("hipodrome_id" => $htrackid,
                                                  'center_id'    => $centerId,
                                                  'byHorses'     => $lastRiders),
                            'fields'     => array('id','val_from','val_to',
                                                'div_add','amount'),
                            'order'      => array('val_from' => 'ASC')
                     ));
        
        return $intervals;
        
    }
    
    function getFourHorses($cid)
    {
        $fourhrs = $this->find('all',array(
                            'conditions' => array('center_id' => $cid, 'is4hrs' => 1),
                            'fields'     => array('id','hipodrome_id','val_from','val_to',
                                                'div_add','amount')));
        
        /*$fourarr = array();
        foreach ( $fourhrs as $f ) {
            $fourarr[$f['Interval']['hipodrome_id']] = array(
                                            'id'     => $f['Interval']['id'],
                                            'amount' => $f['Interval']['amount']
                );
            
        }
        */
        return $fourhrs;
    }
}
?>