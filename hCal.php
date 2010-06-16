<?php
require_once 'hCalCore.php';
require_once 'hCalEvent.php';

/**
 * Symbol that hCal used as Line Delimetiter when parsing
 */
define('HCAL_LINE_SPLITER', '&loz;');

/**
 * File delimiter (\r\n for common)
 */
define('HCAL_LINE_DELIMITER', "\r\n");

/**
 * Process the entire iCal file and delegate the tasks of parsing
 *
 * @category  HCal
 * @package   HCal
 * @author    bu <bu@hax4.in>
 * @copyright 2010 hahahaha studio
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link      http://hcal.hax4.in/
 */
class hCal extends hCalCore
{

    /**
     * iCal file content
     * @var    string
     * @access protected
     */
    protected $_Content = '';
    /**
     * iCal events
     * @var    array
     * @access protected
     */
    protected $_Events = array();

    /**
     * providing users access to a ical file
     *
     * @param  unknown $file_location Parameter description (if any) ...
     * @return object  Return description (if any) ...
     * @access public
     * @static
     */
    public static function createFrom($file_location)
    {
	return new hCal(file_get_contents($file_location));
    }

    /**
     * actions that takes after init
     *
     * @param  unknown $content Parameter description (if any) ...
     * @return void
     * @access public
     */
    public function __construct($content)
    {
	$content = str_replace(HCAL_LINE_DELIMITER, HCAL_LINE_SPLITER, $content);
	$this->_Content = $content;
	$this->parseEvents();
    }

    //X-WR-TIMEZONE

    /**
     * parse timezone information
     *
     * @return void
     * @access protected
     */
    protected function parseTimezone()
    {
	$pattern = '/BEGIN:VTIMEZONE(.*?)END:VTIMEZONE/';
	preg_match_all($pattern, $this->_Content, $matches, PREG_PATTERN_ORDER);
	$zone = array();
	foreach ($matches[1] as $match)
	{
	    $zone_lines = explode(HCAL_LINE_SPLITER, $match);
	    $zone_attr = array();
	    foreach ($zone_lines as $line)
	    {
		$result = $this->parseProperty($line);
		if (is_array($result))
		{
		    $zone_attr[$result['name']] = $result['value'];
		}
	    }
	    $zone[] = $zone_attr;
	}
	print_r($zone);
    }

    /**
     * Parse events and put them into $this->_Events[]
     *
     * @return void
     * @access protected
     */
    protected function parseEvents()
    {
	$pattern = '/BEGIN:VEVENT(.*?)END:VEVENT/';
	preg_match_all($pattern, $this->_Content, $matches, PREG_PATTERN_ORDER);
	foreach ($matches[1] as $event)
	{
	    $this->_Events[] = new hCalEvent($event);
	}
    }

    /**
     * Provide access for users to retrieve events
     *
     * @param  array  $options reterieve options that could be optionally assigned
     * @return array  the events according to the requested options.
     * @access public
     */
    public function getEvents($options = null)
    {
	$events = $this->_Events;

	/**
	 *  If there is no options
	 */
	if($options == null)
	{
	    $options = array();
	}

	if (isset($options['order']) && is_string($options['order']) && $options['order'] == 'time')
	{
	    $event_by_time = array();

	    foreach ($events as $event)
	    {
		$time = $event->getTime();
		$event_by_time[$time[0]] = $event;
	    }

	    krsort($event_by_time);

	    $events = $event_by_time;

	    unset($event_by_time);
	}
	 
	if (isset($options['count']) && $options['count'] > 0)
	{
	    $events = array_slice($events, 0, $options['count']);
	    
	}

	return $events;
    }

}

