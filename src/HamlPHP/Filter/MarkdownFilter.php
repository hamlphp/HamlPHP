<?php

require_once 'IHamlFilter.php';
require_once HAMLPHP_ROOT . 'vendor/MarkdownExtra/markdown.php';

class MarkdownFilter implements IHamlFilter
{
	/**
	 * @var Markdown_Parser
	 */
	protected $parser;
	
	public function __construct()
	{
		$this->parser = new Markdown_Parser();
	}
	
	/**
	 * @see IFilter::getIdentifier()
	 */
	public function getIdentifier()
	{
		return 'markdown';
	}

	/**
	 * @see IFilter::filter()
	 */
	public function filter(HamlNode $node)
	{
		if(null === $node)
			throw new Exception("MarkdownFilter: node is null.");
		
		$children = $node->getChildren();
		$output = '';

		$indent = 999999999;
		// gets the lowes indent among the children to set as base
		foreach($children as $child)
		{
			if($indent > ($ind = $child->getIndentationLevel()))
				$indent = $ind;
		}

		foreach($children as $childNode)
			$output .= substr($childNode->getRawHaml(), $indent)."\n";
		
		return $this->parser->transform($output);
	}
}