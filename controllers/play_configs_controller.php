<?php
class PlayConfigsController extends AppController {

	var $name = 'PlayConfigs';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->PlayConfig->recursive = 0;
		$this->set('playConfigs', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PlayConfig', true), array('action'=>'index'));
		}
		$this->set('playConfig', $this->PlayConfig->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->PlayConfig->create();
			if ($this->PlayConfig->save($this->data)) {
				$this->Session->setFlash(__('PlayConfig saved.', true), array('action'=>'index'));
			} else {
			}
		}
		$playTypes = $this->PlayConfig->PlayType->find('list');
		$profiles = $this->PlayConfig->Profile->find('list');
		$this->set(compact('playTypes', 'profiles'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid PlayConfig', true), array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->PlayConfig->save($this->data)) {
				$this->Session->setFlash(__('The PlayConfig has been saved.', true), array('action'=>'index'));
			} else {
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PlayConfig->read(null, $id);
		}
		$playTypes = $this->PlayConfig->PlayType->find('list');
		$profiles = $this->PlayConfig->Profile->find('list');
		$this->set(compact('playTypes','profiles'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PlayConfig', true), array('action'=>'index'));
		}
		if ($this->PlayConfig->del($id)) {
			$this->Session->setFlash(__('PlayConfig deleted', true), array('action'=>'index'));
		}
	}


	function admin_index() {
		$this->PlayConfig->recursive = 0;
		$this->set('playConfigs', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PlayConfig', true), array('action'=>'index'));
		}
		$this->set('playConfig', $this->PlayConfig->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->PlayConfig->create();
			if ($this->PlayConfig->save($this->data)) {
				$this->Session->setFlash(__('PlayConfig saved.', true), array('action'=>'index'));
			} else {
			}
		}
		$playTypes = $this->PlayConfig->PlayType->find('list');
		$profiles = $this->PlayConfig->Profile->find('list');
		$this->set(compact('playTypes', 'profiles'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid PlayConfig', true), array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->PlayConfig->save($this->data)) {
				$this->Session->setFlash(__('The PlayConfig has been saved.', true), array('action'=>'index'));
			} else {
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PlayConfig->read(null, $id);
		}
		$playTypes = $this->PlayConfig->PlayType->find('list');
		$profiles = $this->PlayConfig->Profile->find('list');
		$this->set(compact('playTypes','profiles'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PlayConfig', true), array('action'=>'index'));
		}
		if ($this->PlayConfig->del($id)) {
			$this->Session->setFlash(__('PlayConfig deleted', true), array('action'=>'index'));
		}
	}

}
?>