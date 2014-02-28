<?php

require_once HAMLPHP_ROOT . 'ContentEvaluator/IContentEvaluator.php';
require_once 'IStorage.php';

class FileStorage implements IStorage
{
	protected $_path = null;
	protected $_extension = null;
	protected $_requirePath = null;
	protected $_basePath = '';
	
	/**
	 * Contructor
	 *
	 * The param \c $basePath param is used to shorten the unique filename
	 * which is generated using the file real path.
	 *
	 * @param string $cachePath
	 *        	where to save the cache files
	 * @param string $extension
	 *        	the extension of the cache file
	 * @param string $basePath
	 *        	The base path from where the files come from
	 */
	public function __construct($cachePath, $extension = '.cached.php', $basePath = '')
	{
		$this->_path = rtrim($cachePath, '/\\') . DIRECTORY_SEPARATOR;
		$this->_extension = $extension;
		$this->_basePath = ltrim($basePath, '/\\');
	}
/*
	public function evaluate($content, array $contentVariables = array(), $id = null)
	{
		if(null === $id)
		{
			throw new Exception("FileStorage: Could not evaluate. ID is null.");
		}
		
		$this->_requirePath = $this->_path . $id . $this->_extension;
		
		ob_start();
		extract($contentVariables);
		require $this->_requirePath;
		$result = ob_get_clean();
		
		return $result;
	}
*/
	/**
	 *
	 * @see Storage::isFresh()
	 */
	public function isFresh($id, $fileName = false)
	{
		$path = $this->_path . $id . $this->_extension;
		if($fileName) 
		{
			$id = $fileName;
		}

		return file_exists($id) && file_exists($path) && filemtime($id) < filemtime($path);
	}

	/**
	 *
	 * @see Storage::cache()
	 */
	public function cache($id, $content)
	{
		$path = $this->_path . $id . $this->_extension;
		
		if(!file_exists($path))
		{
			@fopen($path, "x+");
		}
		
		file_put_contents($path, $content);
	}

	/**
	 *
	 * @see Storage::fetch()
	 */
	public function fetch($id)
	{
		return file_get_contents($this->_path . $id . $this->_extension);
	}

	/**
	 *
	 * @see Storage::remove()
	 */
	public function remove($id)
	{
		$path = $this->_path . $id . $this->_extension;
		return unlink($path);
	}

	public function generateContentId($filepath)
	{
		$path = str_replace($this->_basePath, '', ltrim($filepath, '/\\'));
		$id = str_replace(array(':','/','\\', ' '), '_', ltrim($filepath, '/\\'));
		
		return $id;
	}
}
