<?php

/**
 * Short description for class
 *
 * Long description (if any) ...
 *
 * @category  CategoryName
 * @package   hCal
 * @author    Author's name <author@mail.com>
 * @copyright 2010 Author's name
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/hCal
 * @see       References to other sections (if any)...
 */
class hCalEvent extends hCalCore
{

    /**
     * Description for private
     * @var    array
     * @access private
     */
    private $column = array();
    /**
     * Description for private
     * @var    unknown
     * @access private
     */
    private $_Content;

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $event_text Parameter description (if any) ...
     * @return void
     * @access public
     */
    public function __construct($event_text)
    {
	$this->_Content = $event_text;
	$this->parse();
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
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
	    $this->column[$result['name']] = $result['value'];
	}
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @return void
     * @access public
     */
    public function printColumns()
    {
	var_dump($this->column);
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @return string Return description (if any) ...
     * @access public
     */
    public function getTitle()
    {
	return $this->get('SUMMARY');
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $field_name Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function parseTimeField($field_name)
    {
	if (isset($this->column[$field_name]['TZID']))
	{
	    $date = new DateTime($this->column[$field_name]['value'], new DateTimeZone($this->column[$field_name]['TZID']));
	    return $date->format('YmdHis');
	}
	else
	{
	    if (isset($this->column[$field_name]['VALUE']) && $this->column[$field_name]['VALUE'] == 'DATE')
	    {
		return $this->column[$field_name]['value'] . '000000';
	    }
	    if (strpos($this->column[$field_name]['value'], 'Z') !== false)
	    {
		return date('YmdHis', strtotime($this->column[$field_name]['value']));
	    }
	    return date('YmdHis', strtotime($this->column[$field_name]['value']));
	}
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @return mixed  Return description (if any) ...
     * @access public
     */
    public function getTime()
    {
	return array($this->parseTimeField('DTSTART'), $this->parseTimeField('DTEND'));
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @return string Return description (if any) ...
     * @access public
     */
    public function getDescription()
    {
	return $this->get('DESCRIPTION');
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @return string Return description (if any) ...
     * @access public
     */
    public function getUID()
    {
	return $this->get('UID');
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  string $iCal_FieldName Parameter description (if any) ...
     * @return mixed  Return description (if any) ...
     * @access public
     */
    public function get($iCal_FieldName = '')
    {
	if (isset($this->column[$iCal_FieldName]['value']))
	{
	    return $this->column[$iCal_FieldName]['value'];
	}
	else
	{
	    return null;
	}
    }

}
