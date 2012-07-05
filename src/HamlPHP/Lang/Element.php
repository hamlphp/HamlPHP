<?php

require_once HAMLPHP_ROOT.'/Util/StringHelper.php';
require_once HAMLPHP_ROOT.'/HamlPHP.php';
require_once HAMLPHP_ROOT.'/Util/StringScanner.php';
require_once HAMLPHP_ROOT.'/Exceptions.php';

class Element
{
	//const HAML_REGEXP = '/^(?P<tag>%\w+)?(?P<id>#\w*)?(?P<classes>\.[\w\.\-]*)*(?P<attributes>\((?P<html_attrs>.+)\)|\{(?P<hash_attrs>.*)\})?(?P<php>=)?(?P<inline>[^\w\.#\{].*)?$/';

	const ELEMENT = '%';
	const ID = '#';
	const KLASS = '.';

	private $_haml = null;
	private $_tag = null;
	private $_id = null;
	private $_classes = null;
	private $_inlineContent = null;
	private $_php = false;
	private $_attributes = null;
	private $_scanner;
	private $_nukeOuterWhitespace;
	private $_nukeInnerWhitespace;

	private $_selfClosing = false;
	private $_preserveWhitespace = false;
	private $_useAttsHelper;

	/**
	 * @var Compiler
	 */
	private $_compiler;

	public function __construct($line, Compiler $compiler)
	{
		$this->_haml = $line;
		$this->_scanner = new StringScanner($this->_haml);
		$this->_compiler = $compiler;
		$this->parseHaml();
		$this->_compiler = null;
	}

	private function parseHaml()
	{
		$scanner = $this->_scanner;

		if (! $scanner->check('/(?:%(?P<tag>[\\-:\\w]+))?(?P<attrs>[\\#\\.\\-:\\w]*)?(?P<rest>.*)/'))
			throw new SyntaxErrorException("Invalid tag: \"${$this->haml}\".");

		if (preg_match('/[\.#](\.|#|\z)/', $scanner['attrs']))
			throw new SyntaxErrorException("Illegal element: classes and ids must have values.");

		if (!empty($scanner['tag']))
			$this->_tag = $scanner['tag'];
		else
			$this->_tag = 'div';

		$classAndIdAtts = $this->_parseClassAndId($scanner['attrs']);

		$scanner->string = $scanner['rest'];

		$hashAtts = $htmlAtts = $objRef = false;
		$merge_order = array(
			$classAndIdAtts
		);

		while ($scanner->rest)
		{
			switch ($scanner->rest[0])
			{
				case '{':
					if ($hashAtts)
						break;
					$hashAtts = $this->_parseHashAttrs($scanner);
					$merge_order[] = $hashAtts;
					break;
				case '(':
					if ($htmlAtts)
						break;
					$htmlAtts = $this->_parseHtmlAttrs($scanner);
					$merge_order[] = $htmlAtts;
					break;
				case '[':
					if ($objRef)
						break;
					$objRef = $this->_parseObjRef($scanner);
					$merge_order[] = $objRef;
					break;
				default:
					break 2;
			}
		}

		$this->_attributes = $this->_mergeAttributes($merge_order);

		if (! $this->_attributes)
			$this->_attributes = array();

		$this->_useAttsHelper = $this->_containsPhpAttribute($this->_attributes);

		if ($scanner->rest)
		{
			$scanner->scan('/(<>|><|[><])?([=\/\~&!])?(.*)?/');
			$nuke_whitespace = trim($scanner[1]);
			$action = $scanner[2];
			$value = $scanner[3];

			$this->_nukeOuterWhitespace = (mb_strpos($nuke_whitespace, '>') !== false);
			$this->_nukeInnerWhitespace = (mb_strpos($nuke_whitespace, '<') !== false);

			$escape_html = ($action == '&' || ($action != '!' && HamlPHP::$Config['escape_html']));

			switch ($action)
			{
				case '/':
					$this->_selfClosing = true;
					$value = trim($value);
					if(!empty($value))
						throw new SyntaxErrorException("Self closing tags can't have content.");
					break;

				case '~': // whitespace preservation
					throw new Exception("Whitespace preservation (~) is not implemented yet.");
					$parse = $this->_preserveWhitespace = true;
					break;

				case '=': //
					$this->_php = true;
					$this->_inlineContent = $this->_interpolate(mb_substr($value, 1));
					break;

				case '&': // escape html
				case '!': // unescape html
					throw new Exception("Escape (&) and unescape (!) html features are not implemented yet.");
					if ($value[0] == '=' || $value[0] == '~')
					{
						$parse = true;
						$this->_preserveWhitespace = ($value[0] == '~');
						if ($value[1] == '=')
						{
							$value = $this->_interpolate(mb_substr($value, 2, - 1), $escape_html);
							$escape_html = false;
						}
						else
							$value = mb_substr($value, 1, - 1);
					}
					else
						$this->_interpolate($value, $escape_html);

					break;

				default:
					$value = trim($value);
					if(!empty($value))
						$this->_inlineContent = $this->_interpolate($value, $escape_html);
			}

		}

	/*
    	 raise SyntaxError.new("Illegal nesting: nesting within a self-closing tag is illegal.", @next_line.index) if block_opened? && self_closing
      raise SyntaxError.new("There's no Ruby code for #{action} to evaluate.", last_line - 1) if parse && value.empty?
      raise SyntaxError.new("Self-closing tags can't have content.", last_line - 1) if self_closing && !value.empty?

      if block_opened? && !value.empty? && !is_ruby_multiline?(value)
        raise SyntaxError.new("Illegal nesting: content can't be both given on the same line as %#{tag_name} and nested within it.", @next_line.index)
      end

      self_closing ||= !!(!block_opened? && value.empty? && @options[:autoclose].any? {|t| t === tag_name})
      value = nil if value.empty? && (block_opened? || self_closing)
      value = handle_ruby_multiline(value) if parse
    	 */
	}

	private function _containsPhpAttribute(array $att_arr)
	{
		foreach ($att_arr as $att)
		{
			if(isset($att[0]))
				return true;
			
			if (isset($att['t']) && ($att['t'] == 'php' || $att['t'] == 'function'))
				return true;				
		}

		return false;
	}

	private function _mergeAttributes($att_arrays)
	{
		$merged = array();

		$count = count($att_arrays);

		if ($count == 1)
			return $att_arrays[0];

		for ($i = 0; $i < $count; $i ++)
		{
			foreach ($att_arrays[$i] as $k => $v)
			{
				if(is_array($v))
					$att = $v;
				else
					$att = array($v);

				for ($j = $i + 1; $j < $count; $j ++)
				{
					if (isset($att_arrays[$j][$k]))
					{
						if(is_array($att_arrays[$j][$k]) && is_array($v))
							$att = array_merge($att, $att_arrays[$j][$k]);
						else
							$att[] = $att_arrays[$j][$k];

						unset($att_arrays[$j][$k]);
					}
				}

				if (count($att) == 1 && !is_array($v))
					$merged[$k] = $v;
				else
					$merged[$k] = $att;
			}
		}

		return $merged;
	}

	private function _parseObjRef($scanner)
	{
		$scanner->scan('/\\[*\s*/');

		$obj = $scanner->scan('/[^,\\]]+/');
		$scanner->scan('/\s*,\s*/');
		
		$prefix = $scanner->scan('/[^\\]]*/');
		
		if (!$prefix)
			$prefix = '';
		else
			$prefix = ", '".ltrim($prefix, ':')."'";
		
		$end = trim($scanner->scan('/\s*\\]/'));

		if ($end != ']')
			throw new SyntaxErrorException("Invalid object reference.");
		
		return array(
			'id' => array(array(
				't' => 'php' , 'v' => "id_for({$obj}{$prefix})"
			)),
			'class' => array(array(
				't' => 'php' , 'v' => "class_for({$obj}{$prefix})"
			))
		);
	}

	private function _parseClassAndId($list)
	{
		$list = new StringScanner($list);
		$attributes = array();

		while ($list->scan('/([#.])([-:_a-zA-Z0-9]+)/'))
		{
			$type = $list[1];
			$prop = $list[2];

			switch ($type)
			{
				case '.':
					$attributes['class'][] = array('t' => 'str', 'v' => $prop);
					break;
				case '#':
					$attributes['id'][] = array('t' => 'str', 'v' => $prop);
					break;
			}
		}

		return $attributes;
	}

	private function _parseHtmlAttrs(StringScanner $scanner)
	{
		$atts = array(
			'class' => array(),
			'id' => array()
		);

		$scanner->scan('/\(\s*/');
		while (! $scanner->scan('/\\s*\\)/'))
		{
			$scanner->scan('/\s*/');

			if (! $name = $scanner->scan('/[-:\w]+/'))
				throw new SyntaxErrorException("Invalid attribute list: {$scanner->string}");

			$scanner->scan('/\s*/');

			// If a equal sign doesn't follow, the value is true
			if (! $scanner->scan('/=/'))
				$atts[$name] = array(
					't' => 'static' , 'v' => true
				);
			else
			{
				$scanner->scan('/\s*/');

				// if we don't find a quote, it's a php value
				// e.g: name=$avar
				if (! ($quote = $scanner->scan('/["\\\']/')))
				{
					if (! $var = $scanner->scan('/\$\w+/')) // in this mode only variables are accepted
						throw new SyntaxErrorException("Invalid attribute value for $name in list: {$scanner->string}");

					if($name == 'class' || $name == 'id')
						$atts[$name][] =  array(
							't' => 'php' , 'v' => $var
						);
					else
						$atts[$name] = array(
							't' => 'php' , 'v' => $var
						);
				}
				// e.g: checked="true"
				elseif ($scanner->scan('/true|false/i'))
				{
					if ($scanner[0] == 'true')
						$atts[$name] = array(
							't' => 'static' , 'v' => true
						);
					else
						$atts[$name] = array(
							't' => 'static' , 'v' => false
						);
				}
				else
				{
					// we've found a quote, let's scan until the ending quote
					$scanner->scan("/([^\\\\]*?)$quote/");
					$content = $scanner[1];

					if($name == 'class' || $name == 'id')
						$atts[$name][] =  array(
							't' => 'str' , 'v' => $content
						);
					else
						$atts[$name] = array(
							't' => 'str' , 'v' => $quote . $content . $quote
						);
				}
			}

			$scanner->scan('/\s*/');
			if ($scanner->eos)
			{
				$next_line = ' ' . trim($this->_compiler->getNextLine());
				$scanner->concat($next_line);
			}
		}

		if(count($atts['id']) == 0)
			unset($atts['id']);

		if(count($atts['class']) == 0)
			unset($atts['class']);

		return $atts;
	}

	/**
	 * @param StringScanner $scanner
	 * @return array an array with the parsed elements
	 */
	private function _parseHashAttrs(StringScanner $scanner)
	{
		$atts = array(
			'class' => array(),
			'id' => array()
		);
		$litRe = '/(["\\\']).*?\\2(?=[\\s|=])|:?[\\-\\w:]*/';

		$scanner->scan('/\\s*\\{?\\s*/');

		while (! $scanner->scan('/}/'))
		{
			$scanner->scan('/\\s*/');

			$name = trim($scanner->scan($litRe), ':"\'');
			if (! $name) {
				throw new SyntaxErrorException("Invalid attribute list. Expecting an attribute name");
			}

			if (! $scanner->scan('/\s*=>\s*/'))
			{
				// it's an attribute function
				if(!($scanner->rest[0] == '('))
					throw new SyntaxErrorException("Invalid attribute list. Either missing attribute function parameters or attribute value");

				list ($balanced, $value, $rest, $count) = $this->balance($scanner, '(', ')', 0);

				if(!$balanced)
					throw new SyntaxErrorException("Unbalanced brackets in attribute function");

				$value = $name.$value;
				$name = 'fn_'.count($atts);
				$atts[$name] = array(
					't' => 'function' , 'v' => $value
				);
				$scanner->scan('/\\s*,\\s*/');
				continue;
			}

			switch ($scanner->rest[0])
			{
				case '"':
				case "'":
					$quote = $scanner->scan('/["\']/');
					$scanner->scan("/(.*?[^\\\\])$quote/");
					$value = $scanner[1];
					
					if($name == 'class' || $name == 'id') {
						$atts[$name][] =  array(
							't' => 'str' , 'v' => $scanner[1]
						);
					}
					else
					{
						$atts[$name] = array(
							't' => 'str' , 'v' => $quote . $value . $quote
						);
					}
					break;

				case '[':
					$value = $scanner->scanUntil('/\]/');
					$items = mb_substr($value, 1, - 1);

					if($name == 'class' || $name == 'id')
						$atts[$name][] =  array(
							't' => 'php' , 'v' => "array($items)"
						);
					else
						$atts[$name] = array(
							't' => 'php' , 'v' => "array($items)"
						);
					break;

				case '$':
					$value = $scanner->scanUntil('/(?=[,\\}])/');

					if($name == 'class' || $name == 'id')
						$atts[$name][] =  array(
							't' => 'php' , 'v' => $value
						);
					else
						$atts[$name] = array(
							't' => 'str' , 'v' => '"#{'.$value.'}"'
						);
					break;

				case '{':
					list ($balanced, $value, $rest, $count) = $this->balance($scanner, '{', '}', 0);
					
					if($name == 'data')
					{
						$data_arr = $this->_parseHashAttrs(new StringScanner($value));
						foreach($data_arr as $key => $val)
							$atts["data-$key"] = $val;
					}
					else 
					{
						$value = mb_substr($value, 1, - 1);
	
						if($name == 'class' || $name == 'id')
							$atts[$name][] =  array(
								't' => 'php' , 'v' => $value
							);
						else
							$atts[$name] = array(
								't' => 'php' , 'v' => $value
							);
					}
					break;
					
				default:
					if ($scanner->scan('/true|false/i'))
					{
						if ($scanner[0] == 'true')
							$atts[$name] = array(
								't' => 'static' , 'v' => true
							);
						else
							$atts[$name] = array(
								't' => 'static' , 'v' => false
							);
					}
					else
					{
						$value = trim($scanner->scanUntil('/(?=[,\\}])/'));
						
						if($name == 'class' || $name == 'id')
							$atts[$name][] =  array(
								't' => 'php' , 'v' => $value
							);
						else
							$atts[$name] = array(
								't' => 'str' , 'v' => $value
							);
					}
			}

			$scanner->scan('/\s*,?\s*/');
			if ($scanner->eos)
			{
				$next_line = ' ' . trim($this->_compiler->getNextLine());
				$scanner->concat($next_line);
			}
		}

		if(count($atts['id']) == 0)
			unset($atts['id']);

		if(count($atts['class']) == 0)
			unset($atts['class']);

		return $atts;
	}

	/**
	 * Checks if the string has a balanced amount of $start_char and $finish_char pairs
	 * If you pass a scanner, the pointer of the scanner will be altered!
	 *
	 * @param $scanner_or_line The line or a scanner to balance
	 * @param $start_char The balancing start char
	 * @param $finish_char The balancing end char
	 * @param $count [Optional] The current balance count
	 * @return array [$balanced, $balanced_str, $rest, $count]
	 */
	private function balance($scanner_or_line, $start_char, $finish_char, $count = 0)
	{
		$str = '';
		$regexp = "/(.*?)[\\$start_char\\$finish_char]/";

		if (! ($scanner_or_line instanceof StringScanner))
			$scanner = new StringScanner($scanner_or_line);
		else
			$scanner = $scanner_or_line;

		while ($scanner->scan($regexp))
		{
			$str .= $scanner->matched;
			if (mb_substr($scanner->matched, - 1, 1) == $start_char)
				$count ++;
			if (mb_substr($scanner->matched, - 1, 1) == $finish_char)
				$count --;

			if ($count == 0)
				return array(
					true , trim($str) , $scanner->rest , $count
				);
		}

		return array(
			false , trim($str) , $scanner->rest , $count
		);
	}

	private function _containsInterpolation($str)
	{
		return preg_match('/(?<!\\\\)\\#\\{/', $str);
	}
	
	private function _interpolate($str)
	{
		if (! $this->_containsInterpolation($str))
			return $str;

		$nStr = '';
		$s = new StringScanner($str);
		
		$quote = $s->check(StringScanner::rQUOTE);
		
		// If it doesn't starts with a quote, it CAN'T be inside php context
		if (empty($quote) || !s($str)->endsWith($quote))
		{
			$int = new Interpolation($str);
			return $int->render();
		}
		
		$int = new Interpolation($str, true);
		return $int->render();
	}

	public function getTag()
	{
		return $this->_tag;
	}

	public function getId()
	{
		if(isset($this->_attributes['id']))
			return $this->_attributes['id'];

		return null;
	}

	public function isPhpVariable()
	{
		return $this->_php;
	}

	public function getClasses()
	{
		if(isset($this->_attributes['class']))
			return $this->_attributes['class'];

	}

	public function getAttributes()
	{
		return $this->_attributes;
	}

	public function getInlineContent()
	{
		return $this->_inlineContent;
	}

	public function useAttsHelper()
	{
		return $this->_useAttsHelper;
	}
        
	public function isSelfClosing() {
		return $this->_selfClosing;
	}
}

?>