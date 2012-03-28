<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Version 17-09-2010

/**
 * Based on http://www.sencha.com/forum/showthread.php?79211-Ext.Direct-for-CodeIgniter/
 */

/**
 * Ext_direct_api Library Class
 */
class Ext_direct_api
{
	var $_routerUrl			= 'router.php';
	var $_cacheProvider		= null;
	var $_defaults			= array();
	var $_classes			= array();
	var $_remoteAttribute	= '@remotable';
	var $_formAttribute		= '@formHandler';
	var $_nameAttribute		= '@remoteName';
	var $_namespace			= FALSE;
	var $_type				= 'remoting';
	var $_parsedClasses		= array();
	var $_parsedAPI			= array();
	var $_descriptor		= 'Ext.app.REMOTING_API';

    /**
	 * Constructor method
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		log_message('debug', "Ext_direct_api Class Initialized");
	}

	/**
	 * Get State
	 * @return array
	 */
	function getState()
	{
		return array(
			'routerUrl'			=> $this->getRouterUrl(),
			'defaults'			=> $this->getDefaults(),
			'classes'			=> $this->getClasses(),
			'remoteAttribute'	=> $this->getRemoteAttribute(),
			'formAttribute'		=> $this->getFormAttribute(),
			'nameAttribute'		=> $this->getNameAttribute(),
			'namespace'			=> $this->getNamespace(),
			'parsedAPI'			=> $this->_parsedAPI,
			'descriptor'		=> $this->_descriptor
		);
	}

	/**
	 * Set State
	 * @param array $state
	 * @return void
	 */
	function setState($state)
	{
		isset($state['routerUrl']) &&
			$this->setRouterUrl($state['routerUrl']);

		isset($state['defaults']) &&
			$this->setDefaults($state['defaults']);

		isset($state['classes']) &&
			$this->_classes = $state['classes'];

		isset($state['remoteAttribute']) &&
			$this->setRemoteAttribute($state['remoteAttribute']);

		isset($state['formAttribute']) &&
			$this->setFormAttribute($state['formAttribute']);

		isset($state['nameAttribute']) &&
			$this->setNameAttribute($state['nameAttribute']);

		isset($state['namespace']) &&
			$this->setNameSpace($state['namespace']);

		isset($state['descriptor']) &&
			$this->setDescriptor($state['descriptor']);

		isset($state['parsedAPI']) &&
			$this->_parsedAPI = $state['parsedAPI'];
	}

	/**
	 * Add
	 * @param array $classes default empty
	 * @param array $settings default empty
	 * @return void
	 */
	function add($classes = array(), $settings = array())
	{
		$settings = array_merge(
			array(
				'autoInclude'	=> FALSE,
				'basePath'		=> '',
				'seperator'		=> '_',
				'prefix'		=> '',
				'subPath'		=> ''
			),
			$this->_defaults,
			$settings
		);

		is_string($classes) && $classes = array($classes);
		
		foreach ($classes as $name => $cSettings)
		{
			if (is_int($name))
			{
				$name = $cSettings;
				$cSettings = array();
			}
			
			$cSettings = array_merge($settings, $cSettings);
			$cSettings['fullPath'] = $this->getClassPath($name, $cSettings);
			$this->_classes[$name] = $cSettings;
		}
	}

	/**
	 * Output
	 * @param bool $print default TRUE
	 * @return array
	 */
	function output($print = TRUE)
	{
		$saveInCache = FALSE;

		if (isset($this->_cacheProvider))
		{
			if ( ! $this->_cacheProvider->isModified($this))
			{
				$api = $this->_cacheProvider->getAPI();

				if ($print === TRUE) $this->_print($api);

				$this->_parsedClasses	= $this->_classes;
				$this->_parsedAPI		= $api;

				return $api;
			}
			$saveInCache = TRUE;
		}

		$api = $this->getAPI();

		if ($saveInCache) $this->_cacheProvider->save($this);

		if ($print === TRUE) $this->_print($api);

		return $api;
	}

	/**
	 * Is Equal
	 * @param mixed $old
	 * @param mixed $new
	 * @return string
	 */
	function isEqual($old, $new)
	{
		return serialize($old) === serialize($new);
	}

	/**
	 * Get API
	 * Loads personal ext class
	 * @return void
	 */
	function getAPI()
	{
		if ($this->isEqual($this->_classes, $this->_parsedClasses))
			return $this->getParsedAPI();
		
		$classes = array();

		foreach ($this->_classes as $class => $settings)
		{
			$methods = array();

			if ($settings['autoInclude'] === TRUE)
			{
				$path = ! $settings['fullPath']
					? $this->getClassPath($class, $settings)
					: $settings['fullPath'];
				
				$path = str_replace('\\', '/', $path);
				
				if (file_exists($path)) require_once($path);
			}
			
			// here the reflection magic begins
			if (class_exists($settings['prefix'].$class))
			{
				$rClass		= new ReflectionClass($settings['prefix'].$class);
				$rMethods	= $rClass->getMethods();
				
				foreach ($rMethods as $rMethod)
				{
					/**
					 * The class method must be public
					 * and have the atribute remotable
					 * and optional formHandler or remoteName
					 */
					if ($rMethod->isPublic() && strlen($rMethod->getDocComment()) > 0)
					{
						$doc		= $rMethod->getDocComment();
						$isRemote	= !! preg_match('/'.$this->_remoteAttribute.'/', $doc);
						
						if ($isRemote)
						{
							$method = array(
								'name'	=> $rMethod->getName(),
								'len'	=> $rMethod->getNumberOfParameters(),
							);

							if ( !! preg_match('/'.$this->_nameAttribute.' ([\w]+)/', $doc, $matches))
							{
								$method['serverMethod'] = $method['name'];
								$method['name']			= $matches[1];
							}

							if ( !! preg_match('/'.$this->_formAttribute.'/', $doc))
							{
								$method['formHandler'] = TRUE;
							}

							$methods[] = $method;
						}
					}
				}

				if (count($methods) > 0) $classes[$class] = $methods;
			}
		}

		$api = array(
			'url'		=> $this->_routerUrl,
			'type'		=> $this->_type,
			'actions'	=> $classes
		);

		if ($this->_namespace !== FALSE)
			$api['namespace'] = $this->_namespace;
		
		$this->_parsedClasses = $this->_classes;
		$this->_parsedAPI = $api;

		return $api;
	}

	function getParsedAPI() { return $this->_parsedAPI; }

	function getClassPath($class, $settings = FALSE)
	{
		if ( ! $settings) $settings = $this->_settings;

		if ($settings['autoInclude'] === TRUE)
		{
			$path = rtrim(rtrim($settings['basePath'], '/'), '\\').DIRECTORY_SEPARATOR.
					trim(trim($settings['subPath'], '/'), '\\').DIRECTORY_SEPARATOR.
					$class.EXT;
			
			$path = str_replace('\\\\', '\\', $path);
			$path = str_replace('\\', '/', $path);
		}
		else
		{
			$rClass = new ReflectionClass($settings['prefix'].$class);
			
			$path = $rClass->getFileName();
		}

		return APPPATH.$path;
	}

	function getClassesPaths()
	{
		$classesPaths = array();

		foreach ($this->getClasses() as $name => $settings)
		{
			$classesPaths[] = $this->getClassPath($name, $settings);
		}

		return $classesPaths;
	}

	function getClasses() { return $this->_classes; }

	/**
	 * Print javascript
	 * @param array $api
	 */
/*
	function _print($api)
	{
		header('Content-Type: text/javascript');

		echo ($this->_namespace
			? "Ext.ns('".substr($this->_descriptor, 0, strrpos($this->_descriptor, '.'))."');\n".$this->_descriptor
		//	: "Ext.ns('Ext.app');\nExt.app.REMOTING_API"
			: "Ext.app.REMOTING_API"
		);
		echo ' = '.json_encode($api).';';
	}
*/
	function _print($api)
	{
		$CI = & get_instance();
		
		$CI->output->set_header('Content-Type: text/javascript');

		$output = ($this->_namespace 
			? 'Ext.ns(\''.substr($this->_descriptor, 0, strrpos($this->_descriptor, '.')).'\'); '.$this->_descriptor 
		//	: 'Ext.ns(\'Ext.app\');\nExt.app.REMOTING_API'
			: 'Ext.app.REMOTING_API'
		);
		
		$output .= ' = '.json_encode($api).';';
		
		$CI->output->set_output($output);
	}

	function setRouterUrl($routerUrl = 'router.php')
	{
		isset($routerUrl) && $this->_routerUrl = $routerUrl;
	}

	function getRouterUrl() { return $this->_routerUrl; }

	function setCacheProvider($cacheProvider)
	{
		if ($cacheProvider instanceof Ext_direct_cache_provider)
		{
			$this->_cacheProvider = $cacheProvider;
		}
	}

	function getCacheProvider() { return $this->_cacheProvider; }

	function setRemoteAttribute($attribute)
	{
		if (is_string($attribute) && strlen($attribute) > 0)
		{
			$this->_remoteAttribute = $attribute;
		}
	}

	function getRemoteAttribute() { return $this->_remoteAttribute; }

	function setDescriptor($descriptor)
	{
		if (is_string($descriptor) && strlen($descriptor) > 0)
		{
			$this->_descriptor = $descriptor;
		}
	}

	function getDescriptor() { return $this->_descriptor; }

	function setFormAttribute($attribute)
	{
		if (is_string($attribute) && strlen($attribute) > 0)
		{
			$this->_formAttribute = $attribute;
		}
	}

	function getFormAttribute() { return $this->_formAttribute; }

	function setNameAttribute($attribute)
	{
		if (is_string($attribute) && strlen($attribute) > 0)
		{
			$this->_nameAttribute = $attribute;
		}
	}

	function getNameAttribute() { return $this->_nameAttribute; }

	function setNameSpace($namespace)
	{
		if (is_string($namespace) && strlen($namespace) > 0)
		{
			$this->_namespace = $namespace;
		}
	}

	function getNamespace() { return $this->_namespace; }

	function setDefaults($defaults, $clear = FALSE)
	{
		if ($clear === TRUE) $this->clearDefaults();

		is_array($defaults) &&
			$this->_defaults = array_merge($this->_defaults, $defaults);
	}

	function getDefaults() { return $this->_defaults; }

	function clearDefaults() { $this->_defaults = array(); }
}

/* End of file Ext_direct_api.php */
/* Location: ./application/third_party/ext/libraries/Ext_direct_api.php */