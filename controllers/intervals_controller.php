<?php
class IntervalsController extends AppController {

	var $name = 'Intervals';
	
	function beforeFilter(){
		parent::beforeFilter();	
	}
	
	function isAuthorized(){
		
		$ret = true;
		
		$actions_adm = array(
			"admin_index","admin_add","admin_edit","admin_delete",'admin_four'
		);
		
		if($this->isAdmin() && in_array($this->action, $actions_adm)){
			$ret = true;	
		}else{
			$ret = false;
		}
			
		return $ret;
    }

    /**
     * ALTER TABLE `intervals` CHANGE `is4hrs` `byHorses` INT(1) NOT NULL DEFAULT '0';
     * UPDATE `intervals` set byHorses = 4 WHERE byHorses = 1 
     */
    function admin_index()
    {
        $hipodromes = $this->Interval->Hipodrome->find('list',array('conditions' => 'national = 1'));
        $intervals  = $this->Interval->getMyInts($this->authUser['center_id']);
        $this->set(compact('intervals','hipodromes'));
    }
    
    function admin_four()
    {
        if ( !empty ( $this->data )) {
            //pr($this->data); die();
            //$this->data['Interval']['center_id'] = $this->authUser['center_id'];
            foreach ( $this->data['Interval'] as $datint ) {
                $this->Interval->save($datint);
                unset($this->Interval->id);
            }
            
            $this->redirect($this->referer());
        }
        /*
        $fourhrs    = $this->Interval->getMyInts($this->authUser['center_id'],4);
        $byHorses = array(
            4 => $this->Interval->getMyInts($this->authUser['center_id'],4),
            5 => $this->Interval->getMyInts($this->authUser['center_id'],5),
            6 => $this->Interval->getMyInts($this->authUser['center_id'],6)
        );
        */
        $hipodromes = $this->Interval->Hipodrome->find('list',array('conditions' => 'national = 1'));
        $byHorses = $this->Interval->getMyInts($this->authUser['center_id'],array(4,5,6));
        $this->set(compact('hipodromes','byHorses'));
    }
    
    function admin_add ()
    {
        if ( !empty ( $this->data )) {
            //pr($this->data); die();
            $this->data['Interval']['center_id'] = $this->authUser['center_id'];
            $this->Interval->save($this->data);
            $this->redirect($this->referer());
            
        }
    }
    
    function admin_edit ()
    {
        if ( !empty ( $this->data )) {
            //pr($this->data); die();
            //$this->data['Interval']['center_id'] = $this->authUser['center_id'];
            
            foreach ( $this->data['Interval'] as $datint ) {
                $this->Interval->save($datint);
                unset($this->Interval->id);
            }
            
            $this->redirect($this->referer());
            
        }
    }
    
    function admin_delete($id)
    {
        $this->Interval->delete(array('Interval.id'=>$id));
        $this->Session->setFlash('Intervalo Borrado');
        $this->redirect($this->referer());
    }
}
?>