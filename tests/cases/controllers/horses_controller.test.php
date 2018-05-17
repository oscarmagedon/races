<?php 
/* SVN FILE: $Id$ */
/* HorsesController Test cases generated on: 2010-10-20 00:10:58 : 1287549718*/
App::import('Controller', 'Horses');

class TestHorses extends HorsesController {
	var $autoRender = false;
}

class HorsesControllerTest extends CakeTestCase {
	var $Horses = null;

	function startTest() {
		$this->Horses = new TestHorses();
		$this->Horses->constructClasses();
	}

	function testHorsesControllerInstance() {
		$this->assertTrue(is_a($this->Horses, 'HorsesController'));
	}

	function endTest() {
		unset($this->Horses);
	}
}
?>