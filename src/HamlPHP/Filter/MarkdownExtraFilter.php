<?php

require_once 'MarkdownFilter.php';

class MarkdownExtraFilter extends MarkdownFilter
{
	public function __construct()
	{
		$this->parser = new MarkdownExtra_Parser();
	}
	
	/**
	 * @see IFilter::getIdentifier()
	 */
	public function getIdentifier()
	{
		return 'markdownextra';
	}
}