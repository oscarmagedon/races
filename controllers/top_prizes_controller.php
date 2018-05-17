<?php
class TopPrizesController extends AppController {

	var $name = 'TopPrizes';
	
	function beforeFilter(){
		parent::beforeFilter();	
	}
	
	function isAuthorized(){
		
        $actions_rot = array('admin_setmain');
		$actions_cnt = array("admin_index");
				
		if ($this->isRoot() && in_array($this->action, $actions_rot)){
			$ret = true;
		}elseif($this->isAdmin() && in_array($this->action, $actions_cnt)){
			$ret = true;
		}else{
			$ret = false;
		}
		
		return $ret;
	}
	
    function admin_setmain()
    {
        if ( ! empty ( $this->data ) ) {
            //pr($this->data);
            $this->TopPrize->saveFormObj($this->data['TopPrize']);
            $this->redirect($this->referer());
        }
    }
    
    function admin_index()
    {
        if ( ! empty ( $this->data ) ) {
            //pr($this->data); die();
            $this->TopPrize->saveFormObj($this->data['TopPrize'],$this->authUser['center_id']);
            $this->redirect($this->referer());
        }
        
        $rootTops   = $this->TopPrize->getCenterTops();
        $centerTops = $this->TopPrize->getCenterTops($this->authUser['center_id']);
        $topHclass  = $this->TopPrize->hclasses;
        $topTypes   = $this->TopPrize->types;
        
        $this->set(compact('rootTops','centerTops','topHclass','topTypes'));
    }

}
?>