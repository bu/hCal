<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../hCal.php';

class hCalTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var hCal
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
	$this->object = new hCal(file_get_contents('test.ics'));
    }

    public function testCreateFrom()
    {
	$result = hCal::createFrom('test.ics');

	$this->assertEquals(true, is_a($result, 'hCal'));
    }

    public function testGetEventsWithoutOptions()
    {
	// normal grab
	$result = $this->object->getEvents();

	// the test.ics contains 61 events, so the result should be 61, too.
	$this->assertEquals(61, sizeof($result));

	// each of the result item should be a instance of hCalEvent
	foreach ($result as $result_item)
	{
	    $this->assertEquals(true, is_a($result_item, 'hCalEvent'));
	}

	// test for file-based order
	$this->assertEquals('New Year\'s Eve', $result[0]->getTitle());
	$this->assertEquals(array(20031231000000, null), $result[0]->getTime());
    }

    public function testGetEventsWithOptions()
    {
	$result = $this->object->getEvents(array('order' => 'time', 'count' => '5'));

	// test for the count option
	$this->assertEquals(5, sizeof($result));

	// test for time-based order
	$this->assertEquals('Yom Kippur', $result[0]->getTitle());
	$this->assertEquals(array(20100918000000, null), $result[0]->getTime());
    }

}
?>
