<?php

/**
 * Event parsing
 *
 * @category  HCal
 * @package   HCal
 * @author    bu <bu@hax4.in>
 * @copyright 2010 hahahaha studio
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link      http://hcal.hax4.in/
 */
class hCalEvent extends hCalCore
{

    /**
     * Event Properties
     * @var    array
     * @access private
     */
    private $_Properties = array();
    /**
     * Event iCal content
     * @var    unknown
     * @access private
     */
    private $_Content;

    /**
     * process after init
     *
     * @param  string $event_text The iCal text to be parsed
     * @return void
     * @access public
     */
    public function __construct($event_text)
    {
	$this->_Content = $event_text;
	$this->parse();
    }

    /**
     * Parse the properties and put them into $this->_Properities[]
     *
     * @return void
     * @access protected
     */
    protected function parse()
    {
	$event_lines = explode(HCAL_LINE_SPLITER, $this->_Content);

	foreach ($event_lines as $line)
	{
	    $result = $this->parseProperty($line);
	    $this->_Properties[$result['name']] = $result['value'];
	}
    }

    /**
     * Return title for the event
     *
     * @return string Event title
     * @access public
     */
    public function getTitle()
    {
	return $this->get('SUMMARY');
    }

    /**
     * parse time field (adjust with the timezone)
     *
     * @param  string $field_name the name of the requested time properity
     * @return string the time string after timezone adjust
     * @access public
     */
    public function parseTimeField($field_name)
    {
	if (!isset($this->_Properties[$field_name]))
	{
	    return null;
	}

	if (isset($this->_Properties[$field_name]['TZID']))
	{
	    $date = new DateTime($this->_Properties[$field_name]['value'], new DateTimeZone($this->_Properties[$field_name]['TZID']));
	    return $date->format('YmdHis');
	}
	else
	{
	    if (isset($this->_Properties[$field_name]['VALUE']) && $this->_Properties[$field_name]['VALUE'] == 'DATE')
	    {
		return $this->_Properties[$field_name]['value'] . '000000';
	    }
	    if (strpos($this->_Properties[$field_name]['value'], 'Z') !== false)
	    {
		return date('YmdHis', strtotime($this->_Properties[$field_name]['value']));
	    }
	    return date('YmdHis', strtotime($this->_Properties[$field_name]['value']));
	}
    }

    /**
     * return event start, and end
     *
     * if start or end is not set(eg Repeative events), return null.
     *
     * @return array {DTSTART, DTEND}
     * @access public
     */
    public function getTime()
    {
	return array($this->parseTimeField('DTSTART'), $this->parseTimeField('DTEND'));
    }

    public function getStartTime()
    {
	return $this->parseTimeField('DTSTART');
    }

    public function getEndTime()
    {
	$this->parseTimeField('DTEND');
    }

    /**
     * return event description
     *
     * @return string Return description (if any) ...
     * @access public
     */
    public function getDescription()
    {
	return str_replace('\n',"\n",$this->get('DESCRIPTION'));
    }

    /**
     * return event uid
     *
     * @return string Event UID
     * @access public
     */
    public function getUID()
    {
	return $this->get('UID');
    }

    /**
     * return event location
     *
     * @return string Event UID
     * @access public
     */
    public function getLocation()
    {
	return $this->get('LOCATION');
    }

    /**
     * retreieve the specific properity
     *
     * @param  string $Properity_Name Properity name
     * @return mixed  the value of the requested properity
     * @access public
     */
    public function get($Properity_Name = '')
    {
	if (isset($this->_Properties[$Properity_Name]['value']))
	{
	    return $this->_Properties[$Properity_Name]['value'];
	}
	else
	{
	    return null;
	}
    }

}
