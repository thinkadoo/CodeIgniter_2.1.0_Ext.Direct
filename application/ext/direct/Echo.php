<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Version 17-09-2010

/**
 * Based on http://www.sencha.com/forum/showthread.php?79211-Ext.Direct-for-CodeIgniter/
 */

/**
 * Class_Echo Class
 */
class Class_Echo
{
	/**
	 * Send
	 * @remotable
	 * @param string $string
	 * @return string
	 */
	public function send($string)
	{
		return $string;
	}
}

/* End of file Class_Echo.php */
/* Location: ./application/ext/direct/Class_Echo.php */