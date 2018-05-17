<?php 
/* SVN FILE: $Id$ */
/* UnitsController Test cases generated on: 2010-10-20 00:10:51 : 1287549531*/
App::import('Controller', 'Units');

class TestUnits extends UnitsController {
	var $autoRender = false;
}

class UnitsControllerTest extends CakeTestCase {
	var $Units = null;

	function startTest() {
		$this->Units = new TestUnits();
		$this->Units->constructClasses();
	}

	function testUnitsControllerInstance() {
		$this->assertTrue(is_a($this->Units, 'UnitsController'));
	}

	function endTest() {
		unset($this->Units);
	}
}
?>