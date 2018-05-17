<?php 
/* SVN FILE: $Id$ */
/* PlayConfigsController Test cases generated on: 2010-10-20 00:10:20 : 1287549860*/
App::import('Controller', 'PlayConfigs');

class TestPlayConfigs extends PlayConfigsController {
	var $autoRender = false;
}

class PlayConfigsControllerTest extends CakeTestCase {
	var $PlayConfigs = null;

	function startTest() {
		$this->PlayConfigs = new TestPlayConfigs();
		$this->PlayConfigs->constructClasses();
	}

	function testPlayConfigsControllerInstance() {
		$this->assertTrue(is_a($this->PlayConfigs, 'PlayConfigsController'));
	}

	function endTest() {
		unset($this->PlayConfigs);
	}
}
?>