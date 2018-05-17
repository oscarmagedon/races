<?php
class TopPrize extends AppModel {

	var $name = 'TopPrize';
    
    var $hclasses = array('A','B','C');
    
    var $types    = array('EX','TR','SU');
    
    function getCenterTops($cid = 1, $hcls = 0)
    {
        $conds = array('center_id' => $cid);
        
        if ( $hcls !== 0 ) {
            $conds['hclass'] = $hcls;
        }
        $tops = $this->find('all',array('conditions' => $conds));
        
        $topgr = array();
        
        foreach ( $tops as $top ) {
            $topgr[$top['TopPrize']['hclass']][$top['TopPrize']['type']] = array(
                'id'  => $top['TopPrize']['id'],
                'top' => $top['TopPrize']['top']
                
            );
        }
        
        return $topgr;
    }
    
    function getByHcls($cid,$hcls)
    {
        $centerTops = $this->getCenterTops($cid,$hcls);
        if ( empty ( $centerTops ) ) {
            $centerTops = $this->getCenterTops(1,$hcls);
        }
        
        $ctops = array();
        if ( ! empty ( $centerTops ) ) {
            $ctops = array(
                'EX' => $centerTops[$hcls]['EX']['top'],
                'TR' => $centerTops[$hcls]['TR']['top'],
                'SU' => $centerTops[$hcls]['SU']['top']
            );
        }
        
        return $ctops;
    }
    
    function saveFormObj($tops, $cid = 1)
    {
        foreach ( $tops as $hcls => $types) {
            foreach ( $types as $typ => $vals ) {
                if ( isset ($vals['id'])) {
                    $this->save($vals);
                } else {
                    if ($vals['top'] != '' ) {
                        $this->save(array('top' => $vals['top'],'center_id' => $cid, 
                            'type' => $typ, 'hclass' => $hcls ));
                    }
                    
                }
                
                unset($this->id);
            }
        }
    }
}
?>