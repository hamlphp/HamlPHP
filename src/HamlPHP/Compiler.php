<?php

require_once 'NodeFactory.php';

require_once 'Filter/FilterContainer.php';
require_once 'Filter/CssFilter.php';
require_once 'Filter/PlainFilter.php';
require_once 'Filter/JavascriptFilter.php';
require_once 'Filter/PhpFilter.php';

class Compiler
{
	private $_hamlphp = null;
	private $_lines;
	private $_currLine;

	public function __construct(HamlPHP $hamlphp)
	{
		$this->setHamlPhp($hamlphp);
	}

	public function setHamlPhp(HamlPHP $hamlphp)
	{
		$this->_hamlphp = $hamlphp;
	}

	/**
	 * Compiles haml from a file.
	 *
	 * @param string $fileName        	
	 */
	public function parseFile($fileName)
	{
		return $this->parseString(file_get_contents($fileName));
	}

	/**
	 * Compiles haml from a string.
	 *
	 * @param string $rawString        	
	 */
	public function parseString($rawString)
	{
		$lines = explode("\n", trim((string) $rawString));
		return $this->parseLines($lines);
	}
	
	/**
	 * Returns the indent level of a line.
	 * 
	 * The indent level is the number of characters used to indent the line.
	 * So, a tab = 1, a space, also equals 1.
	 * 
	 * @param string $line The line
	 * 
	 * @return int
	 */
	private function getIndentLevel($line)
	{
		return mb_strlen($line) - mb_strlen(trim($line));
	}

	/**
	 * Compiles haml from an array of lines.
	 *
	 * @param array $rawLines        	
	 */
	public function parseLines(array $rawLines = array())
	{
		$this->_currLine = 0;
		$this->_lines = $this->removeEmptyLines($rawLines);
		$rootNode = new RootNode();
		$rootNode->setCompiler($this);
		$rootNode->setLineNumber(0);
		$nodeFactory = $this->_hamlphp->getNodeFactory();
		
		for($len = count($this->_lines); $this->_currLine < $len; ++$this->_currLine)
		{
			try
			{
				if($this->getIndentLevel($this->_lines[$this->_currLine]) <= $filterIndentLevel)
					$filterContext = false;
				
				if($filterContext)
					$nd = new HamlNode($this->_lines[$this->_currLine]);
				else
					$nd = $nodeFactory->createNode($this->_lines[$this->_currLine], $this->_currLine, $this);
				
				if($nd instanceof FilterNode) {
					if($filterContext)
						throw new SyntaxErrorException('You cannot nest filters.');
					
					$filterContext = true;
					$filterIndentLevel = $this->getIndentLevel($this->_lines[$this->_currLine]);
				}
				
				$rootNode->addNode($nd);
			}
			catch(Exception $e)
			{
				$line = "UNKNOWN (sorry!)";
				
				if(isset($this->_lines[$this->_currLine]))
					$line = $this->_lines[$this->_currLine];
				else if(isset($this->_lines[$this->_currLine-1]))
					$line = $this->_lines[$this->_currLine-1];
				
				throw new SyntaxErrorException("Error parsing line:\n{$line}\n" . $e->getMessage(), $e->getCode());
			}
		}
		
		return $rootNode->render();
	}

	public function getLines()
	{
		return $this->_lines;
	}

	public function getLine($index)
	{
		if(isset($this->_lines[$index]))
		{
			return $this->_lines[$index];
		}
		
		return null;
	}

	public function getNextLine()
	{
		$index = ++$this->_currLine;
		
		if(isset($this->_lines[$index]))
		{
			return $this->_lines[$index];
		}
		
		return null;
	}

	// @todo We CANNOT remove empty lines inside some filters (eg the planned markdown filter), actually, i think we shouldn't do this at all 
	private function removeEmptyLines(array $rawLines)
	{
		$codeLines = array();
		
		for($i = 0, $len = count($rawLines); $i < $len; ++$i)
		{
			$line = $rawLines[$i];
			
			if(trim($line) != '')
			{
				$codeLines[] = $line;
			}
		}
		
		return $codeLines;
	}
}
