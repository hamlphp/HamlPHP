<?php

require_once 'Storage/Storage.php';
require_once 'Compiler.php';
require_once 'ContentEvaluator/DefaultContentEvaluator.php';

class HamlPHP
{
  private $_compiler = null;
  private $_storage = null;
  private $_contentEvaluator = null;
  private $_nodeFactory = null;
  private $_filterContainer = null;
  private $_cacheEnabled = true;

  // Placeholder until config gets properly implemented
  public static $Config = array(
  	'escape_html' => false
  );

  public function __construct(Storage $storage)
  {
    $this->_compiler = $this->getCompiler();
    $this->_storage = $storage;

    if ($this->_storage instanceof ContentEvaluator) {
      $this->setContentEvaluator($this->_storage);
    } else {
      $this->setContentEvaluator(new DefaultContentEvaluator());
    }
  }

  /**
   * Sets a content evaluator.
   *
   * @param ContentEvaluator $contentEvaluator
   */
  public function setContentEvaluator(ContentEvaluator $contentEvaluator)
  {
    $this->_contentEvaluator = $contentEvaluator;
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
    $this->_nodeFactory = $factory;
    $this->getNodeFactory()->setFilterContainer($this->getFilterContainer());
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
   * Parses a haml file and returns a cached path to the file.
   *
   * @param string $fileName
   */
  public function parseFile($fileName, array $templateVars = array())
  {
    $content = $this->getContentFromStorage($fileName);

    return $this->_contentEvaluator->evaluate(
        $content, $templateVars, $this->generateFileId($fileName));
  }

  /**
   * Returns content from a storage
   *
   * @param string $fileName
   * @return string
   */
  public function getContentFromStorage($fileName)
  {
  	$fileId = $this->generateFileId($fileName);

    if ($this->_storage === null) {
        throw new Exception('Storage not set');
    }

    if ($this->isCacheEnabled()
        && $this->_storage->isFresh($fileId)) {
      return $this->_storage->fetch($fileId);
    }

    // file is not fresh, so compile and cache it
    $this->_storage->cache($fileId, $this->_compiler->parseFile($fileName));
    return $this->_storage->fetch($fileId);
  }

  private function generateFileId($filename)
  {
  	return str_replace(array(':','/','\\'), '_', ltrim($filename, '/\\'));
  }
}
