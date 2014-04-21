<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'Config.php';
require_once 'Storage/IStorage.php';
require_once 'Compiler.php';

class HamlPHP
{
	/**
	 * @var Compiler
	 */
	private $_compiler = null;
	
	/**
	 * @var Storage
	 */
	private $_storage = null;
	
	/**
	 * @var NodeFactory
	 */
	private $_nodeFactory = null;
	
	/**
	 * @var FilterContainer
	 */
	private $_filterContainer = null;
	
	/**
	 * @var bool
	 */
	private $_cacheEnabled = true;

	// Placeholder until config gets properly implemented
	/**
	 * @var Config
	 */
	public static $Config = array(
		'escape_html' => false
	);

	public function __construct(IStorage $storage = null)
	{
		$this->_compiler = $this->getCompiler();
		if(empty($storage))
			$this->_cacheEnabled = false;
		else
			$this->_storage = $storage;
	}

	/**
	 * Sets a filter container and updates the node factory to use it.
	 *
	 * @param FilterContainer $container
	 */
	public function setFilterContainer(FilterContainer $container)
	{
		$this->_filterContainer = $container;
		$this->getNodeFactory()->setFilterContainer($this->getFilterContainer());
	}

	/**
	 * Returns a filter container object. Initializes the filter container with
	 * default filters if it's null.
	 *
	 * @return FilterContainer
	 */
	public function getFilterContainer()
	{
		if ($this->_filterContainer === null) {
			$filterContainer = new FilterContainer();
			$filterContainer->addFilter(new CssFilter());
			$filterContainer->addFilter(new PlainFilter());
			$filterContainer->addFilter(new JavascriptFilter());
			$filterContainer->addFilter(new PhpFilter());
			$filterContainer->addFilter(new MarkdownFilter());
			$filterContainer->addFilter(new MarkdownExtraFilter());
				
			$this->_filterContainer = $filterContainer;
		}

		return $this->_filterContainer;
	}

	/**
	 * Sets a node factory.
	 *
	 * @param NodeFactory $factory
	 */
	public function setNodeFactory(NodeFactory $factory)
	{
		if($factory === null)
			throw new Exception("Parameter \$factory can not be null");
		
		$this->_nodeFactory = $factory;
		$this->_nodeFactory->setFilterContainer($this->getFilterContainer());
	}

	/**
	 * Returns a node factory object.
	 *
	 * @return NodeFactory
	 */
	public function getNodeFactory()
	{
		if ($this->_nodeFactory === null) {
			$this->setNodeFactory(new NodeFactory());
		}

		return $this->_nodeFactory;
	}

	/**
	 * Sets a compiler.
	 *
	 * @param Compiler $compiler
	 */
	public function setCompiler(Compiler $compiler)
	{
		$this->_compiler = $compiler;
	}

	/**
	 * Returns a compiler object.
	 *
	 * @return Compiler
	 */
	public function getCompiler()
	{
		if ($this->_compiler === null) {
			$this->_compiler = new Compiler($this);
		}

		return $this->_compiler;
	}

	/**
	 * Enables caching.
	 */
	public function enableCache()
	{
		$this->_cacheEnabled = true;
	}

	/**
	 * Disables caching.
	 */
	public function disableCache()
	{
		$this->_cacheEnabled = false;
	}

	/**
	 * Returns true if caching is enabled.
	 *
	 * @return bool
	 */
	public function isCacheEnabled()
	{
		return $this->_cacheEnabled;
	}

	/**
	 * Parses a haml file and returns the compile result.
	 *
	 * @param string $fileName
	 */
	public function parseFile($fileName)
	{
		if($this->_cacheEnabled)
		{
			if ($this->_storage === null) {
				throw new Exception('Storage not set');
			}

			$fileId = $this->_storage->generateContentId($fileName);
			
			if ($this->isCacheEnabled()
				&& $this->_storage->isFresh($fileId, $fileName)) {
				return $this->_storage->fetch($fileId);
			}

			// file is not fresh, so compile and cache it
			$this->_storage->cache($fileId, $this->_compiler->parseFile($fileName));
			return $this->_storage->fetch($fileId);
		}
		
		// not using cache
		return $this->_compiler->parseFile($fileName);
	}
	
	public function evaluate($content, array $contentVariables = array())
	{
		
		$tempFileName = tempnam("/tmp", "foo");
		$fp = fopen($tempFileName, "w");
		fwrite($fp, $content);
		
		// @todo: why not use eval()?
		ob_start();
		extract($contentVariables); 
		require $tempFileName;
		$result = ob_get_clean();
	
		fclose($fp);
		unlink($tempFileName);
		
		return $result;
	}
}
