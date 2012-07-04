<?php

/**
 * Utility class for multi-byte safe string operations 
 * @author Saulo Vallory <me@saulovallory.com>
 */
class StringHelper
{
	/**
	 * @var string
	 */
	private $value;
	
	/**
	 * Cache of string length
	 * @var int
	 */
	private $length;
	
	public function __construct($str)
	{
		$this->value = $str;
		$this->length = mb_strlen($str);
	}
	
	/**
	 * @param string $str
	 * @return boolean
	 */
	public function startsWith($str)
	{
		return mb_substr($this->value, 0, mb_strlen($str)) == $str;
	}
	
	/**
	 * @param string $str
	 * @return boolean
	 */
	public function endsWith($str)
	{
		$sLen = mb_strlen($str);
		return mb_substr($this->value, $this->length - $sLen) == $str;
	}
	
	/**
	 * 
	 * @param unknown_type $openChar
	 * @param unknown_type $closeChar
	 * @return StringHelper
	 */
	public function trimBalanced($openChar, $closeChar)
	{
		$stLen = mb_strlen($openChar);
		$endLen = mb_strlen($closeChar);
		
		while($this->startsWith($openChar) && $this->endsWith($closeChar))
		{
			$this->value = mb_substr($this->value, $stLen, $this->length - ($stLen + $endLen));
			$this->length -= $stLen + $endLen;
		}
		
		return $this;
	}
	
	public function __toString()
	{
		return $this->value;
	}
}

/**
 * Shortcut method for easy chaining
 * E.g.: s($someString)->startsWith('"');
 * 
 * @param string $str The string to be expanded
 * @return StringHelper
 */
function s($str)
{
	return new StringHelper($str);
}