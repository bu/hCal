<?php

require 'hCalCore.php';
require_once 'hCalEvent.php';

/**
 * hCal, a iCal parser for PHP
 *
 * PHP Version 5
 *
 * @category  HCal
 * @package   HCal
 * @author    bu <bu@hax4.in>
 * @copyright 2010 hahahaha studio
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link      http://pear.php.net/package/hCal
 */
/**
 * Symbol that hCal used as Line Delimetiter when parsing
 */
define('HCAL_LINE_SPLITER', '&loz;');

/**
 * File delimiter (\r\n for common)
 */
define('HCAL_LINE_DELIMITER', "\r\n");

/**
 * Short description for class
 *
 * Long description (if any) ...
 *
 * @category  CategoryName
 * @package   HCal
 * @author    bu <bu@hax4.in>
 * @copyright 2010 hahahaha studio
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link      http://hcal.hax4.in/
 */
class hCal extends hCalCore
{

    /**
     * Description for protected
     * @var    string
     * @access protected
     */
    protected $_Content = '';
    /**
     * Description for protected
     * @var    array
     * @access protected
     */
    protected $_Events = array();

    /**
     * Short description for function
     *
     * Long description (if any) ...
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
     * Short description for function
     *
     * Long description (if any) ...
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
     * Short description for function
     *
     * Long description (if any) ...
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
     * Parse file
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
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  array  $options Parameter description (if any) ...
     * @return array  Return description (if any) ...
     * @access public
     */
    public function getEvents($options)
    {
	$events = $this->_Events;
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
	if (isset($options['count']) && is_int($options['count']) && $options['count'] > 0)
	{
	    $events = array_slice($events, 0, $options['count']);
	}
	return $events;
    }

}

