<?php

/**
 * hCal common functions
 *
 * @category  CategoryName
 * @package   hCal
 * @author    bu <bu@hax4.in>
 * @copyright 2010 hahahaha studio
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link      http://hcal.hax4.in
 */
class hCalCore
{

    /**
     * parse iCal Componments property
     *
     * @param  string   Attribute that be parsed.
     * @return array    A array that contains property, params, and param-values
     * @access protected
     */
    protected function parseProperty($attribute)
    {
	$temp = explode(':', $attribute);
	if (sizeof($temp) == 2)
	{
	    if (strpos($attribute, ';') === false)
	    {
		return array(
		    'name' => $temp[0],
		    'value' => array('value' => $temp[1])
		);
	    }
	    else
	    {

		$params = explode(';', $temp[0]);

		$attr_array = array();

		foreach ($params as $attr)
		{
		    if (strpos($attr, '=') !== false)
		    {
			$attr_tmp = explode('=', $attr);
			$attr_array[$attr_tmp[0]] = $attr_tmp[1];
		    }
		}

		return array(
		    'name' => $params[0],
		    'value' => array_merge(array('value' => $temp[1]), $attr_array)
		);
	    }
	}
    }

}