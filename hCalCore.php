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
    protected function parseProperty($properpty_string)
    {
	/**
	 * Property Parameters should defined as:
	 *
	 * PROPERPTY;param=param_value:properpty_value
	 */
	if ($properpty_string !== '')
	{
	    $property_parts = explode(':', $properpty_string);

	    if (sizeof($property_parts) == 2)
	    {
		$property_value = $property_parts[1];

		/**
		 * Check if properpty exists any param
		 */
		if (strpos($properpty_string, ';') === false)
		{
		    return array(
			'name' => $property_parts[0],
			'value' => array('value' => $property_value)
		    );
		}
		else
		{
		    /**
		     * If params exist, split them first
		     */
		    $params = explode(';', $property_parts[0]);

		    $param_values = array();

		    foreach ($params as $param)
		    {
			/**
			 * Check if param get a value
			 */
			if (strpos($param, '=') !== false)
			{
			    $param_parts = explode('=', $param);
			    $param_values[$param_parts[0]] = $param_parts[1];
			}
		    }

		    return array(
			'name' => $params[0],
			'value' => array_merge(array('value' => $property_value), $param_values)
		    );
		}
	    }
	}
    }

}