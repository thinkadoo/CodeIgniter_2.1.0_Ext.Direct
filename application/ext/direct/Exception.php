<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Version 17-09-2010

/**
 * Based on http://www.sencha.com/forum/showthread.php?79211-Ext.Direct-for-CodeIgniter/
 */

/**
 * Class_Exception Class
 */
class Class_Exception
{
	/**
	 * Make Error
	 * @remotable
	 */
	public function makeError()
	{
		throw new Exception('A server-side thrown exception');
	}
}

/* End of file Class_Exception.php */
/* Location: ./application/ext/direct/Class_Exception.php */