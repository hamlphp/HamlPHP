<?php

require_once HAMLPHP_ROOT.'Lang/Interpolation.php';

class DoctypeNode extends HamlNode
{
	
	/**
	 * XHTML 1.0 Transitional Doctype
	 * 
	 * @var string
	 */
	const XHTML10_T = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	
	/**
	 * XHTML 1.0 Strict Doctype
	 * 
	 * @var string
	 */
	const XHTML10_S = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	
	/**
	 * XHTML 1.0 Frameset Doctype
	 * 
	 * @var string
	 */
	const XHTML10_F = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
	
	/**
	 * XHTML 5
	 * 
	 * @var string
	 */
	const HTML5 = '<!DOCTYPE html>';
	
	/**
	 * XHTML 1.1 Doctype
	 * 
	 * @var string
	 */
	const XHTML11 = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
	
	/**
	 * XHTML Basic 1.1 Doctype
	 * 
	 * @var string
	 */
	const XHTML11_Basic = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">';
	
	/**
	 * XHTML Mobile 1.2 Doctype
	 * 
	 * @var string
	 */
	const XHTML12_Mobile = '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">';
	
	/**
	 * XHTML+RDFa 1.0 Doctype
	 * 
	 * @var string
	 */
	const XHTML_RDFa = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">';
	
	/**
	 * HTML 4.01 Transitional
	 * @var string
	 */
	const HTML4 = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	
	/**
	 * HTML 4.01 Strict
	 * @var string
	 */
	const HTML4_S = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
	
	/**
	 * HTML 4.01 Frameset
	 * @var string
	 */
	const HTML4_F = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
	
	/**
	 * XML Prolog
	 * 
	 * @var string
	 */
	const XML = '<?xml version="1.0" encoding="%encoding%" ?>';
	
	private $_type;
	
	private $_encoding = 'utf-8';
	
	private $_doctypeMap = array(
		'' => DoctypeNode::XHTML10_T,
		'strict' => DoctypeNode::XHTML10_S,
		'frameset' => DoctypeNode::XHTML10_F,
		'5' => DoctypeNode::HTML5,
		'1.1' => DoctypeNode::XHTML11,
		'basic' => DoctypeNode::XHTML11_Basic,
		'mobile' => DoctypeNode::XHTML12_Mobile,
		'rdfa' => DoctypeNode::XHTML_RDFa
	);

	public function __construct($line)
	{
		parent::__construct($line);
		$parts = explode(' ', trim($line));
		
		$this->_type = isset($parts[1]) ? strtolower($parts[1]) : '';
		
		if('xml' == $this->_type && isset($parts[2]))
			$this->_encoding = $parts[2];
	}

	public function render()
	{
		$interpolation = new Interpolation($this->renderDoctype());
		return $interpolation->render();
	}

	private function renderDoctype()
	{
		// When the :format option is set to :html5, !!! is always <!DOCTYPE html>
		if(Config::$format == 'html5')
			return DoctypeNode::HTML5 . Config::$NL;
		
		// When the :format option is set to :html4, ONLY the following doctypes are supported: HTML4, HTML4_S and HTML4_F 
		if(Config::$format == 'html4')
		{
			if('strict' == $this->type)
				return DoctypeNode::HTML4_S . Config::$NL;
			
			if('frameset' == $this->type)
				return DoctypeNode::HTML4_F . Config::$NL;
			
			return DoctypeNode::HTML4 . Config::$NL;
		}
		
		if('xml' == $this->_type) 
			return str_replace($this->_doctypeMap['xml'], '%encoding%', $this->_encoding) . Config::$NL;
		
		if(isset($this->_doctypeMap[$this->_type])) 
			return $this->_doctypeMap[$this->_type] . Config::$NL;

		return '';
	}
}