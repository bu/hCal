<?php
define('HCAL_LINE_SPLITER' , '&loz;');
define('HCAL_LINE_DELIMITER' , "\r\n");

class hCal extends hCalCore
{
    protected $_Content = '';
    protected $_Events = array();
    
    public function __construct($content)
    {
        $content = str_replace(HCAL_LINE_DELIMITER, HCAL_LINE_SPLITER, $content);
        
        $this->_Content = $content;
        
        $this->parseEvents();
    }
    
    //X-WR-TIMEZONE
    
    protected function parseTimezone()
    {
        $pattern = '/BEGIN:VTIMEZONE(.*?)END:VTIMEZONE/';
        
        preg_match_all($pattern, $this->_Content, $matches, PREG_PATTERN_ORDER);
        
        $zone =array();
        
        foreach($matches[1] as $match)
        {
            $zone_lines = explode(HCAL_LINE_SPLITER, $match);
            
            $zone_attr = array();
            foreach($zone_lines as $line)
            {
                $result = $this->parseAttribute($line);
                if(is_array($result))
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
        
        foreach($matches[1] as $event)
        {
            $this->_Events[] = new hCalEvent($event);
        }
    }
  
  public function getEvents()
  {
    return $this->_Events;
  }
}

class hCalCore
{
    protected function parseAttribute($attribute)
    {
        $temp = explode(':' , $attribute);
        
        if(sizeof($temp) == 2)
        {
            if(strpos($attribute, ';') === FALSE)
            {
                return array( 'name' => $temp[0] , 'value' => array('value' => $temp[1]));
            }
            else
            {
                $attribute_attr = explode(';', $temp[0]);
                
                $attr_array = array();
                
                foreach($attribute_attr as $attr)
                {
                    if(strpos($attr,'=') !== FALSE)
                    {
                        $attr_tmp = explode('=',$attr);
                        
                        $attr_array[$attr_tmp[0]] = $attr_tmp[1];
                    }
                }
                
                return array( 'name' => $attribute_attr[0] , 'value' => array_merge(array('value' => $temp[1]), $attr_array));
            }
        }
    }
}

class hCalEvent extends hCalCore
{
    private $timeStart, $timeEnd, $retrieveTime, $UID, $description, $location, $summary;
    
    private $column = array()   ;
    
    private $_Content;
    
    public function __construct($event_text)
    {
        $this->_Content = $event_text;
        
        $this->parse();
    }
    
    protected function parse()
    {
        $event_lines = explode(HCAL_LINE_SPLITER, $this->_Content);
            
        foreach($event_lines as $line)
        {
            $result = $this->parseAttribute($line);
            
            $this->column[$result['name']] = $result['value'];
        }
    }
    

    
    public function printColumns()
    {
        var_dump($this->column);
    }
    
    public function getTitle()
    {
        return $this->get('SUMMARY');
    }
    
    public function parseTimeField($field_name)
    {
        if(isset($this->column[$field_name]['TZID']))
        {
             $date = new DateTime($this->column[$field_name]['value'], new DateTimeZone($this->column[$field_name]['TZID']));
             
             return $date->format('YmdHis');
        }
        else
        {
            if(isset($this->column[$field_name]['VALUE']) &&  $this->column[$field_name]['VALUE'] == 'DATE')
            {
                return $this->column[$field_name]['value'].'000000';
            }
            
	    if(strpos($this->column[$field_name]['value'],'Z') !== FALSE)
	    {
		return date('YmdHis',strtotime($this->column[$field_name]['value']));
	    }

    	return date('YmdHis',strtotime($this->column[$field_name]['value']));
    
        }
    }
    
    public function getTime()
    {
        return array($this->parseTimeField('DTSTART'), $this->parseTimeField('DTEND'));
    }
    
    public function getDescription()
    {
        return $this->get('DESCRIPTION');
    }
    
    public function getUID()
    {
        return $this->UID;
    }
    
    public function get($iCal_FieldName = '')
    {
        if(isset($this->column[$iCal_FieldName]['value']))
	{
	    return $this->column[$iCal_FieldName]['value'];
	}
	else
	{
	    return null;
	}
    }
}
