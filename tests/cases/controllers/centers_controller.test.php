<?php 
/* SVN FILE: $Id$ */
/* CentersController Test cases generated on: 2010-10-20 00:10:37 : 1287549277*/
App::import('Controller', 'Centers');

class TestCenters extends CentersController {
	var $autoRender = false;
}

class CentersControllerTest extends CakeTestCase {
	var $Centers = null;

	function startTest() {
		$this->Centers = new TestCenters();
		$this->Centers->constructClasses();
	}

	function testCentersControllerInstance() {
		$this->assertTrue(is_a($this->Centers, 'CentersController'));
	}

	function endTest() {
		unset($this->Centers);
	}
}
?>