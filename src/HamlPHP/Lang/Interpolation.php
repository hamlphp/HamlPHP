<?php

class Interpolation
{
	const REGEXP = '/(?:\#\{([^\}]+)\})+/';
	const REPLACEMENT = '<?php echo $1; ?>';
	
	private $_text = null;
	private $_phpCtx = false;
	
	/**
	 * When $inPhpContext is true, instead of putting the expression inde php tags (<?php ?>)
	 * the class will render a concatenation like: . ($text) . unless the interpolation makes the whole
	 * text. In which case, it will only the markers (#{ and }) will be removed.
	 * 
	 * @param string $text
	 * @param bool $inPhpContext (default: false) Whether this interpolation is already inside <?php ?> or not
	 */
	public function __construct($text, $inPhpContext = false)
	{
		$this->_text = $text;
		$this->_phpCtx = $inPhpContext;
	}		

	private function _containsInterpolation($str)
	{
		return preg_match('/(?<!\\\\)\\#\\{/', $str);
	}
	
	/**
	 * @throws SyntaxErrorException
	 */
	public function render()
	{
		if(!$this->_containsInterpolation($this->_text))
			return $this->_text;
		
		$pieces = array();
		
		$glueL = ($this->_phpCtx ? '.' : '<?php echo ');
		$glueR = ($this->_phpCtx ? '.' : ' ?>');
		$encapsL = ($this->_phpCtx ? '(' : '');
		$encapsR = ($this->_phpCtx ? ')' : ';');
		
		$s = new StringScanner($this->_text);
		
		$quote = $s->scan(StringScanner::rQUOTE);
		
		while(!$s->eos)
		{
			trim($s->scan('/(.*?)(?<!\\\\)\\#\\{/sm'));
			
			if(!empty($s[1]))
				$pieces[] = $quote . $s[1] . $quote;
			
			trim($s->scan('/(.*?)(?<!\\\\)}/sm'));

			if(!empty($s[1]))
			{
				if($this->_containsInterpolation($s[1]))
					throw new SyntaxErrorException("Nesting interpolation is not allowed: " . $this->_text);
				
				$pieces[] = $glueL;
				$pieces[] = $encapsL.$s[1].$encapsR;
				$pieces[] = $glueR;
			}
			else
			{
				throw new SyntaxErrorException("Unclosed interpolation in: " . $this->_text);
			}
			
			if(!$this->_containsInterpolation($s->rest))
			{
				$rest = trim($s->scan('/.*/sm'), " $quote");
				
				if(!empty($rest))
					$pieces[] = $quote . $rest . $quote;
			}
		}

		if($this->_phpCtx)
		{
			// can't have glue on the edges
			if($pieces[0] == $glueL)
				array_shift($pieces);
			
			// can't have glue on the edges
			if($pieces[count($pieces)-1] == $glueR)
				array_pop($pieces);
			
			// don't nee the parenteses if it is only one thing
			if(count($pieces) == 1)
				$pieces[0] = s($pieces[0])->trimBalanced('(',')');
		}
		
		return join($pieces);
	}
}
