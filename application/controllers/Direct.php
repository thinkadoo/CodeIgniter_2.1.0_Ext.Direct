<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Version 17-09-2010

/**
 * Based on http://www.sencha.com/forum/showthread.php?79211-Ext.Direct-for-CodeIgniter/
 */

/**
 * Direct controller
 */
class Direct extends Controller
{
    /**
	 * Constructor
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->add_package_path(APPPATH.'third_party/ext/');

		$this->load->library('session');
		$this->load->library('ext_direct_api');
		$this->load->library('ext_direct_cache_provider',
			array('filePath' => $this->config->item('cache_path').'api_cache.txt'));
	}

	/**
	 * Index
	 * @access public
	 * @return void
	 */
	public function index()
	{
		//
	}

	/**
	 * Ext Direct API
	 * @param bool $output default TRUE
	 */
	public function api($output = TRUE)
	{
		$this->ext_direct_api->setRouterUrl(site_url('direct/router'));
		$this->ext_direct_api->setCacheProvider($this->ext_direct_cache_provider);
	//	$this->ext_direct_api->setNamespace('Ext.app');
		$this->ext_direct_api->setDescriptor('Ext.app.REMOTING_API');
		$this->ext_direct_api->setDefaults(array(
		    'autoInclude'	=> TRUE,
		    'basePath'		=> 'ext/direct'
		));
		// prefix is for classname not also filename
		$this->ext_direct_api->add(
			array(
				'Echo'		=> array('prefix' => 'Class_'),
				'Exception' => array('prefix' => 'Class_'),
				'Time',
				'File',
				'TestAction',
				'Profile'
			)
		);
		
		$this->session->set_userdata(
			array('ext-direct-state' => $this->ext_direct_api->getState()));

		if ($output) $this->ext_direct_api->output();
	}

	public function router()
	{
		$state = $this->session->userdata('ext-direct-state');
		if ( ! $state) $this->api(FALSE);
		else $this->ext_direct_api->setState($state);

		$this->load->library('ext_direct_router',
			array('api' => $this->ext_direct_api));

		$this->ext_direct_router->dispatch();
		$this->ext_direct_router->getResponse(TRUE);
	}
}

/* End of file Direct.php */
/* Location: ./application/controllers/Direct.php */