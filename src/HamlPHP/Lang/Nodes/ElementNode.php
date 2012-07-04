<?php

require_once HAMLPHP_ROOT . 'Lang/Element.php';

class ElementNode extends HamlNode
{
	private $_phpVariable;
	
	/**
	 *
	 * @var Element
	 */
	private $_el;

	public function __construct($line, $compiler)
	{
		parent::__construct($line);
		$this->_phpVariable = false;
		$this->_el = new Element($this->getHaml(), $compiler);
		$this->_phpVariable = $this->_el->isPhpVariable();
	}

	public function render()
	{
		return $this->renderHtml($this->_el);
	}

	private function renderHtml(Element $element)
	{
		$output = '';
		
		if($this->getIndentationLevel() > 0)
		{
			$output .= $this->getSpaces() . '<' . $element->getTag();
		}
		else
		{
			$output .= '<' . $element->getTag();
		}
		
		if($element->useAttsHelper())
		{
			$output .= ' <?php atts(array(';
			if(($atts = $element->getAttributes()) != null)
			{
				if(isset($atts['id']))
				{
					$output .= "'id' => " . $this->_renderArrayValue($atts['id'], '_', 'php') . ', ';
					unset($atts['id']);
				}
				
				if(isset($atts['class']))
				{
					$output .= "'class' => " . $this->_renderArrayValue($atts['class'], ' ', 'php') . ', ';
					unset($atts['class']);
				}
				
				foreach($atts as $name => $att)
				{
					$output .= "";
					switch($att['t'])
					{
						case 'str':
							$interpolation = new Interpolation($att['v'], true);
							$att_value = $interpolation->render();
							$output .= "'$name' => $att_value, ";
							continue;
						case 'php':
							if(is_array($att['v']))
								$output .= "'$name' => array(" . join(',', $att['v']) . ')';
							else
								$output .= "'$name' => {$att['v']}, ";
							
							continue;
						case 'static':
							$output .= "'$name' => '$name', ";
							continue;
						case 'function':
							$output .= "{$att['v']}, ";
							continue;
					}
				}
			}
			
			$output = rtrim($output, ', ');
			$output .= ')); ?>';
		}
		else
		{
			if(($atts = $element->getAttributes()) !== null)
			{
				if(isset($atts['id']))
				{
					$output .= ' id="' . $this->_renderArrayValue($atts['id'], '_', 'txt') . '"';
					unset($atts['id']);
				}
				
				if(isset($atts['class']))
				{
					$output .= ' class="' . $this->_renderArrayValue($atts['class'], ' ', 'txt') . '"';
					unset($atts['class']);
				}
				
				foreach($atts as $name => $att)
				{
					switch($att['t'])
					{
						case 'str':
							$output .= " {$name}={$att['v']}";
							continue;
						case 'php':
							$output .= " $name=<?php {$att['v']}; ?>";
							
							continue;
						case 'static':
							$output .= " $name=\"$name\"";
							continue;
					}
				}
			}
		}
		
		$interpolation = new Interpolation($output);
		$output = $interpolation->render();
		
		if($this->_el->isSelfClosing())
		{
			$output .= ' />';
		}
		else
		{
			// render inline content
			$content = $this->renderTagContent($element->getInlineContent());
			$output .= '>' . $content . '</' . $element->getTag() . '>';
		}
		
		return $output . "\n";
	}

	/**
	 * Joins the string $parts together using $separator but format the return according to context.
	 * This method concatenates all the parts using $separtor as glue. If the context is 'txt'
	 * and at least one part is php, the returned string will be encapsuled inside <?php ... ?>
	 * If the context is 'php' the string returned will be a valid php code.
	 * @code
	 *   _renderArrayValue(array(
	 *     array('t' => 'str', 'v' => 'a',
	 *     array('t' => 'str', 'v' => 'b'));			# -> a_b
	 *     
	 *   _renderArrayValue(array(
	 *     array('t' => 'str', 'v' => 'a',
	 *     array('t' => 'str', 'v' => 'b'), 'php');	# -> 'a_b'
	 *     
	 *   _renderArrayValue(array(
	 *     array('t' => 'str', 'v' => 'a',
	 *     array('t' => 'php', 'v' => '$b'));			# -> <?php/**
	 * Joins the string $parts together using $separator but format the return
	 * according to context.
	 * This method concatenates all the parts using $separtor as glue. If the
	 * context is 'txt'
	 * and at least one part is php, the returned string will be encapsuled
	 * inside <?php ... ?>
	 * If the context is 'php' the string returned will be a valid php code.
	 * @code
	 * _renderArrayValue(array(
	 * array('t' => 'str', 'v' => 'a',
	 * array('t' => 'str', 'v' => 'b'));			# -> a_b
	 *
	 * _renderArrayValue(array(
	 * array('t' => 'str', 'v' => 'a',
	 * array('t' => 'str', 'v' => 'b'), 'php');	# -> 'a_b'
	 *
	 * _renderArrayValue(array(
	 * array('t' => 'str', 'v' => 'a',
	 * array('t' => 'php', 'v' => '$b'));			# -> <?php echo 'a_'.$b; ?>
	 *
	 * _renderArrayValue(array(
	 * array('t' => 'str', 'v' => 'a',
	 * array('t' => 'php', 'v' => '$b'), 'php');	# -> 'a_'.$b
	 * @endcode
	 *
	 * @param $arr_parts The
	 *        	components of the id
	 * @param $context The
	 *        	context into which the value will be inserted. Can be 'txt'
	 *        	(default) or 'php'
	 */


	private function _renderArrayValue($parts, $separator = ' ', $context = 'txt')
	{
		$hasPhp = false;
		$values = array();
		
		foreach($parts as $p)
		{
			if($p['t'] == 'php')
			{
				$hasPhp = true;
				$values[] = $p['v'];
			}
			else
			{
				$values[] = "'{$p['v']}'";
			}
		}
		
		if(!$hasPhp)
		{
			$quote = '';
			if('php' == $context) $quote = "'";
			
			$value = '';
			foreach($parts as $p)
				$value .= "$separator{$p['v']}";
			
			return $quote . trim($value, $separator) . $quote;
		}
		else
		{
			$value = join(".", $values);
			$value = str_replace("'.'", $separator, $value);
			$value = str_replace(".'", ".'$separator", $value);
			$value = str_replace("'.", "$separator'.", $value);
			
			if('txt' == $context) return '<?php echo ' . $value . '; ?>';
			
			return $value;
		}
	}

	private function renderTagContent($content)
	{
		if($this->hasChildren())
		{
			$content = "\n" . $this->renderChildren() . $this->getSpaces();
		}
		
		if($content === null)
		{
			$content = '';
		}
		
		if($this->_phpVariable)
		{
			$content = "<?php echo " . $content . " ?>";
		}
		else
		{
			$interpolation = new Interpolation($content);
			$content = $interpolation->render();
		}
		
		return $content;
	}
}
