<?php

/**
 * @author Saulo Vallory <email@saulovallory.com>
 */
require_once dirname(__FILE__) . '/BaseException.php';

class UndefinedPropertyException extends BaseException {
	/**
	 * @param string $class
	 * @param string $property
	 */
	public function __construct($class, $property) {
		parent::__construct("Trying to access an undefined property ($class::$property) on {{guiltyFile}} at line {{guiltyLine}}");
	}
}

/**
 * Thrown when trying to set a read-only property. 
 */
class ReadOnlyPropertyException extends BaseException {
	/**
	 * @param string $class
	 * @param string $property
	 */
	public function __construct($class, $property) {
		parent::__construct("Trying to set a read-only property ($class::$property) on {{guiltyFile}} at line {{guiltyLine}}");
	}
}

/**
 * Provides default __get and __set magic methods
 * You can define a protected array $magic_get_methods in a subclass to
 * specify a "magic property" -> "get method" relation.
 * Ex:
 * <code>
 *    protected $magic_get_methods = array(
 *    	'eos' => 'endOfStream'
 *    );
 * </code>
 */
abstract class BaseObject
{
	public function __get($name)
	{
		if(isset($this->magic_get_methods[$name])) {
			$methods = array($this->magic_get_methods[$name]);
		}
		else {
			$name = ucfirst($name);
			$methods = array("get$name", "is$name", "has$name");
		}
		
		foreach($methods as $m)
		{
			if(method_exists($this, $m)) {
				return $this->$m();
			}
		}
		
		$ex = new UndefinedPropertyException(get_class($this), $name);
		$btrace = debug_backtrace();
		$ex->setGuiltyFile($btrace[0]['file']);
		$ex->setGuiltyLine($btrace[0]['line']);
		throw $ex;
	}

	public function __set($name,$value)
	{
		$setter='set'.$name;
		$boolSetter = "is$name";
		
		if(method_exists($this,$setter))
		{
			$this->$setter($value);
		}
		else if(method_exists($this,'get'.$name))
		{
			$ex = new ReadOnlyPropertyException(get_class($this), $name);
			$btrace = debug_backtrace();
			$ex->setGuiltyFile($btrace[0]['file']);
			$ex->setGuiltyLine($btrace[0]['line']);
			throw $ex;
		}
		else
		{
			$ex = new UndefinedPropertyException(get_class($this), $name);
			$btrace = debug_backtrace();
			$ex->setGuiltyFile($btrace[0]['file']);
			$ex->setGuiltyLine($btrace[0]['line']);
			throw $ex;
		}
	}
}