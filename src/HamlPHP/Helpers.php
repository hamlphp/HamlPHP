<?php

/**
 * Returns a double or single quoted string. whatever works without changing the content of the string.
 * Example:
 * @code
 *   quote("McDonald's")            # -> "\"McDonad's\""
 *   quote("You \"win\", looser")     # -> "'You \"win\", looser'" 
 * @endcode
 * 
 * If it can't be done. It will return false.
 * 
 * @param $str
 * @return mixed The quoted string or false
 */
function quote($str)
{
	if(strpos($str, '"') === false)
		return "\"$str\"";
		
	if(strpos($str, "'") === false)
		return "'$str'";
	
	return false;
}

/**
 * Prints a list of attributes specified by an array of (att_name => att_value)
 * 
 * @param array $atts
 * @param bool $echo [optional] Wheter to echo the result or not (default: true)
 * 
 * @return string The list of attributes
 */
function atts($atts, $echo=true)
{
	$str = '';
	
	foreach ($atts as $name => $value)
	{
		if($value === false)
			continue;
		
		if($value === true)
		{
			$str .= " $name=\"$name\"";
		}
		else
		{
			if('id' == $name && is_array($value))
			{
				$str .= ' id="'.join('_', $value);
			}
			elseif ('class' == $name && is_array($value))
				$str .= ' class="'.join(' ', $value);
			else
				$str .= "$name=".quote($value);
		}
	}
	
	if($echo)
		echo $str;
	
	return $str;
}