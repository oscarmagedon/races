<?php 
/* SVN FILE: $Id$ */
/* OperationsController Test cases generated on: 2010-10-20 00:10:54 : 1287549654*/
App::import('Controller', 'Operations');

class TestOperations extends OperationsController {
	var $autoRender = false;
}

class OperationsControllerTest extends CakeTestCase {
	var $Operations = null;

	function startTest() {
		$this->Operations = new TestOperations();
		$this->Operations->constructClasses();
	}

	function testOperationsControllerInstance() {
		$this->assertTrue(is_a($this->Operations, 'OperationsController'));
	}

	function endTest() {
		unset($this->Operations);
	}
}
?>