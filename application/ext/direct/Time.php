<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Version 17-09-2010

/**
 * Based on http://www.sencha.com/forum/showthread.php?79211-Ext.Direct-for-CodeIgniter/
 */

/**
 * Time Class
 */
class Time
{
	/**
	 * @remotable
	 */
	public function get()
	{
		return date('m-d-Y H:i:s');
	}
}
