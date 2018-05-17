<?php 
/* SVN FILE: $Id$ */
/* HipodromesController Test cases generated on: 2010-10-20 00:10:31 : 1287549691*/
App::import('Controller', 'Hipodromes');

class TestHipodromes extends HipodromesController {
	var $autoRender = false;
}

class HipodromesControllerTest extends CakeTestCase {
	var $Hipodromes = null;

	function startTest() {
		$this->Hipodromes = new TestHipodromes();
		$this->Hipodromes->constructClasses();
	}

	function testHipodromesControllerInstance() {
		$this->assertTrue(is_a($this->Hipodromes, 'HipodromesController'));
	}

	function endTest() {
		unset($this->Hipodromes);
	}
}
?>