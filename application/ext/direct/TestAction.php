<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Version 17-09-2010

/**
 * Based on ext-3.3-beta2-7080/examples/direct/php/classes/TestAction.php
 * http://www.sencha.com
 */

/**
 * TestAction Class
 */
class TestAction
{
    /**
	 * Constructor method
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		log_message('debug', "Ext Direct TestAction Class Initialized");
	}

	/**
	 * Do Echo
	 * @remotable
	 * @access public
	 */
	public function doEcho($data)
	{
		return $data;
	}

	/**
	 * Multiply
	 * @remotable
	 * @access public
	 */
	public function multiply($num)
	{
		if ( ! is_numeric($num))
		{
			throw new Exception('Call to multiply with a value that is not a number');
		}

		return $num * 8;
	}

	/**
	 * Get Tree
	 * @remotable
	 * @access public
	 */
	public function getTree($id)
	{
		$out = array();

		if ($id == "root")
		{
			for ($i = 1; $i <= 5; ++$i)
			{
				array_push($out, array(
					'id'	=> 'n'.$i,
					'text'	=> 'Node '.$i,
					'leaf'	=> false
				));
			}
		}
		else if (strlen($id) == 2)
		{
			$num = substr($id, 1);

			for ($i = 1; $i <= 5; ++$i)
			{
				array_push($out, array(
					'id'	=> $id.$i,
					'text'	=> 'Node '.$num.'.'.$i,
					'leaf'	=> true
				));
			}
		}

		return $out;
	}
}

/* End of file TestAction.php */
/* Location: ./application/ext/direct/TestAction.php */