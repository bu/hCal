<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../../hCalCore.php';

/**
 * Mock object that extends hCalCore, providing access to protected methods.
 */
class Mock_hCalCore extends hCalCore
{

    public function _parseProperty($arguments)
    {
	return $this->parseProperty($arguments);
    }

}

/**
 * hCalCore Test
 */
class hCalCoreTest extends PHPUnit_Framework_TestCase
{

    public function testParseProperty()
    {
	$halCore = new Mock_hCalCore();

	$test_data = 'DTEND;VALUE=DATE:20060916';

	$result = $halCore->_parseProperty($test_data);

	$expected = array('name' => 'DTEND', 'value' => array('VALUE' => 'DATE', 'value' => '20060916'));

	$this->assertEquals($expected, $result);
    }

}

