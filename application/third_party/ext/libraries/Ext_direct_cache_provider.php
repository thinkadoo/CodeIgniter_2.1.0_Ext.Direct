<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Version 17-09-2010

/**
 * Based on http://www.sencha.com/forum/showthread.php?79211-Ext.Direct-for-CodeIgniter/
 */

/**
 * Ext_direct_cache_provider Library Class
 */
class Ext_direct_cache_provider
{
	var $_filePath	= null;
	var $_cache		= FALSE;

    /**
	 * Constructor method
	 * @access public
	 * @return void
	 */
	public function __construct($params)
	{
		if (is_string($params['filePath']))
		{
			$this->_filePath = APPPATH.ltrim($params['filePath'], '/');

			if ( ! file_exists($this->_filePath) && ! touch($this->_filePath))
			{
				throw new Exception('Unable to create or access '.$params['filePath']);
			}
		}

		log_message('debug', "Ext_direct_cache_provider Class Initialized");
	}

    function getAPI()
	{
		$this->_parse();
		
		return $this->_cache['api'];
	}

	function isModified($apiInstance)
	{
		$this->_parse();

		if ( ! $apiInstance instanceof Ext_direct_api)
		{
			throw new Exception('You have to pass an instance of ExtDirect_API to isModified function');
		}

		return ! (
			$apiInstance->isEqual($this->_cache['classes'], $apiInstance->getClasses()) &&
			// even if the classes are the same we still have to check if they have been modified
			$apiInstance->isEqual($this->_cache['modifications'], $this->_getModifications($apiInstance))
		);
	}

	function save($apiInstance)
	{
		if ( ! $apiInstance instanceof Ext_direct_api)
		{
			throw new Exception('You have to pass an instance of ExtDirect_API to save function');
		}

		$cache = json_encode(array(
			'classes'		=> $apiInstance->getClasses(),
			'api'			=> $apiInstance->getAPI(),
			'modifications' => $this->_getModifications($apiInstance)
		));

		$this->_write($cache);
	}

	function _getModifications($apiInstance)
	{
		if ( ! $apiInstance instanceof Ext_direct_api)
		{
			throw new Exception('You have to pass an instance of ExtDirect_API to _getModifications function');
		}

		$modifications = array();
		$classesPaths = $apiInstance->getClassesPaths();

		foreach ($classesPaths as $path)
		{
			if (file_exists($path))
			{
				$modifications[$path] = filemtime($path);
			}
		}

		return $modifications;
	}

	function _write($content = '', $append = false)
	{
		file_put_contents($this->_filePath, $content, $append ? FILE_APPEND : 0);
	}

	function _parse()
	{
		if ($this->_cache === false)
		{
			$content = file_get_contents($this->_filePath);
			
			if (strlen($content) === 0)
			{
				$this->_cache = array(
					'classes'		=> array(),
					'api'			=> array(),
					'modifications' => array()
				);

				return;
			}

			$this->_cache = json_decode($content, true);
		}
	}
}

/* End of file Ext_direct_cache_provider.php */
/* Location: ./application/third_party/ext/libraries/Ext_direct_cache_provider.php */