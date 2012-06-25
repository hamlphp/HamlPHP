<?php

/** 
 * Class BaseException 
 * 
 * This is the base exception class for all classes in the framework. 
 * This class provides core funcionality, like automatically replacing
 * available placeholders.
 * 
 * The placeholders provided by this class are:
 * 
 * {{code}} - The user defined exception code
 * {{file}} - The file where the exception was thrown
 * {{guiltyFile}} - The file guilty for the exception
 * {{line}} - The line where the exception was thrown
 * {{guiltyLine}} - The line guilty for the exception
 * {{function}} - The function or method where the exception was thrown
 * {{guiltyFunction}} - The function or method where the exception was thrown
 * {{class}} - The class, in case the exception occurs inside a method, wich the method belongs to
 * {{guiltyClass}} - The class, in case the exception occurs inside a method, wich the method belongs to
 * 
 * @author Saulo Vallory <email@saulovallory.com>
 */
class BaseException extends Exception
{
	/**
	 * The exception message with the placeholders
	 * replaced by the tokens.
	 *
	 * @var string
	 */
	protected $message = 'Unknown exception';
	
	/**
	 * The original exception message, with the
	 * placeholders intact.
	 *
	 * @var string
	 */
	protected $origMsg = 'Unknown exception';
	
	/**
	 * User defined exception code
	 *
	 * @var string
	 */
	protected $code = 0;
	
	/**
	 * Source filename of exception
	 *
	 * @var string
	 */	
	protected $file;
	
	/**
	 * Source line of exception
	 *
	 * @var string
	 */
	protected $line;
	
	/**
	 * Source function of exception
	 *
	 * @var string
	 */
	protected $function;
	
	/**
	 * Source class of exception
	 *
	 * @var string
	 */
	protected $class;	
    
	/**
	 * The file guilty for the exception
	 *
	 * @var string
	 */
	protected $guiltyFile;
	
	/**
	 * The line guilty for the exception
	 *
	 * @var int
	 */
	protected $guiltyLine;
	
	/**
	 * The line guilty for the exception
	 *
	 * @var string
	 */
	protected $guiltyFunction;
	
	/**
	 * The line guilty for the exception
	 *
	 * @var string
	 */
	protected $guiltyClass;	
	
	/**
	 * A piece of code around where the error or exception was throwed
	 *
	 * @var string
	 */
	private $source;
	
	/**
	 * Constructor
	 * 
	 * @param string $message The error message. This can be a string or an instance of 
	 * ErrorMsg. The other parameters will be used to replace placeholders 
	 * ({0:param name}, {1:param}, {2:name}, etc.) in the message.
	 */
	public function __construct($message, $tokens = array())
	{
		global $ex;
		
		foreach($tokens as $tok)
			$message = preg_replace('%\\{\\{([\\d]+:)?[\\w\\s]*\\}\\}%', (string)$tok, $message, 1);

		$this->origMsg = $message;

		$this->message = $message;
		$this->updateMessage();
		
		parent::__construct();
	}
	
	/**
	 * This function gets an array with two items
	 * the first is the placeholder found and the second is the
	 * placeholder name (ex: {{file}} and file). The purpose of 
	 * this function is replace message placeholders with its
	 * values. If you want to extend this functionality you can
	 * override this function, but you must call this function
	 * at the end of yours.
	 *
	 * @param array $match
	 */
	protected static function replacePlaceholder(array $match)
	{
		global $_nb_ex;
		
		if(!isset($match[1]))
			throw new InvalidArgumentValueException('match', $match, ErrorMsg::$BaseException_invalidPlaceholderMatch);
		
		$getter = 'get'.ucfirst($match[1]);
		
		if(method_exists($_nb_ex,$getter))
		{
			return $_nb_ex->$getter();
		}
		
		return $match[0];
	}
	
	/**
	 * Get the file guilty for the exception
	 *
	 * @return string
	 */
	public function getGuiltyFile()
	{
		return $this->guiltyFile;
	}
	
	/**
	 * Get the line guilty for the exception
	 *
	 * @return int
	 */
	public function getGuiltyLine()
	{
		return $this->guiltyLine;
	}
	/**
	 * @return string
	 */
	public function getClass()
	{
		if(isset($this->class))
			return $this->class;
		
		$this->grabTraceData();

		return $this->class;
	}
	
	/**
	 * @return string
	 */
	public function getFunction()
	{
		if(isset($this->function))
			return $this->function;
		
		$this->grabTraceData();
		
		return $this->function;
	}
	
	/**
	 * @return string
	 */
	public function getGuiltyClass () {
		return $this->guiltyClass ;
	}
	
	/**
	 * @return string
	 */
	public function getGuiltyFunction () {
		return $this->guiltyFunction ;
	}
	
	/**
	 * @return string
	 */
	public function getOrigMsg() {
		return $this->origMsg ;
	}
	
	/**
	 * This function grabs all the possible useful
	 * data from trace and put it on class properties
	 */
	protected function grabTraceData()
	{
		$this->trace = $this->getTrace();
		
		if(isset($this->trace[0]))
		{
			if(isset($this->trace[0]['class']))
				$this->class = $this->trace[0]['class'];
				
			if(isset($this->trace[0]['function']))
				$this->function = $this->trace[0]['function'];
		}
		else
		{
			$this->class = '';
			$this->function = '';
		}
	}
	
	/**
	 * @param string $class
	 */
	public function setClass($class) {
		if($this->class === $class)
			$this->class = $class ;

		if(strpos($this->origMsg,'{{class}}') !== false)
			$this->updateMessage();
	}
	
	/**
	 * @param string $function
	 */
	public function setFunction($function) {
		$this->function = $function;
	}
	
	/**
	 * @param string $guiltyClass
	 */
	public function setGuiltyClass ( $guiltyClass ) {
		$this->message = str_replace('{{guiltyClass}}', $guiltyClass, $this->message);
		$this->guiltyClass = $guiltyClass ;
	}
	
	/**
	 * @param string $guiltyFunction
	 */
	public function setGuiltyFunction ( $guiltyFunction ) {
		$this->message = str_replace('{{guiltyFunction}}', $guiltyFunction, $this->message);
		$this->guiltyFunction = $guiltyFunction ;
	}
	
	/**
	 * @param string $origMsg
	 */
	public function setOrigMsg ( $origMsg ) {
		$this->origMsg = $origMsg ;
	}
	
	/**
	 * Set the line file for the exception
	 *
	 * @param string $filePath
	 */
	public function setGuiltyFile($filePath)
	{
		$this->message = str_replace('{{guiltyFile}}', $filePath, $this->message);
		$this->guiltyFile = (string)$filePath;
	}
	
	/**
	 * Set the line guilty for the exception
	 *
	 * @param int $lineNumber
	 */
	public function setGuiltyLine($lineNumber)
	{
		$this->message = str_replace('{{guiltyLine}}', $lineNumber, $this->message);
		$this->guiltyLine = (int)$lineNumber;
	}
	
	/**
	 * This function updates the exception message based on
	 * the original message and the current object property values 
	 *
	 */
	protected function updateMessage()
	{
		$_nb_ex = $this;
		$this->message = preg_replace_callback('|\{\{([\w _]+)\}\}|', array('BaseException', 'replacePlaceholder'), $this->message);
		$_nb_ex = null;		
	}
	
	public function getSourceLines()
	{
		return $this->source;
	}
	
	public function setSourceLines($source)
	{
		$this->source = $source;
	}
		
	public function setCode($code)
	{
		$this->code = $code;
	}
}