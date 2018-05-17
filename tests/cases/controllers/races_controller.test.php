<?php 
/* SVN FILE: $Id$ */
/* RacesController Test cases generated on: 2010-10-20 00:10:40 : 1287549820*/
App::import('Controller', 'Races');

class TestRaces extends RacesController {
	var $autoRender = false;
}

class RacesControllerTest extends CakeTestCase {
	var $Races = null;

	function startTest() {
		$this->Races = new TestRaces();
		$this->Races->constructClasses();
	}

	function testRacesControllerInstance() {
		$this->assertTrue(is_a($this->Races, 'RacesController'));
	}

	function endTest() {
		unset($this->Races);
	}
}
?>