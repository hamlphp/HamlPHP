<?php

define('HAMLPHP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);

class Config
{
	/**
	 * New line format
	 * @var string
	 */
	public static $NL = PHP_EOL;
	
	/**
	 * Determines the output format.
	 *
	 * The default is :xhtml. Other options are :html4 and :html5,
	 * which are identical to :xhtml except there are no self-closing tags,
	 * the XML prolog is ignored and correct DOCTYPEs are generated.
	 *
	 * When the :format option is set to :html5, the doctype is always <!DOCTYPE html>
	 *
	 * http://haml.info/docs/yardoc/file.HAML_REFERENCE.html#format-option
	 *
	 * @var string
	 */
	public static $format = 'xhtml';
}

?>