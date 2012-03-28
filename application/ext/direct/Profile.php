<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Version 17-09-2010

/**
 * Based on ext-3.3-beta2-7080/examples/direct/php/classes/Profile.php
 * http://www.sencha.com
 */

/**
 * Profile Class
 */
class Profile
{
    /**
	 * Constructor method
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		log_message('debug', "Ext Direct Profile Class Initialized");
	}

    /**
	 * Handler for client side form sumbit
	 * @remotable
	 * @formHandler
	 * @access public
	 * @param array $formPacket Collection of form items along with direct data
	 * @return array response packet
	 */
	public function updateBasicInfo($formPacket)
	{
		$response = array();
		
		$email = $formPacket['email'];

		if ($email == 'aaron@extjs.com')
		{
			$success = FALSE;
			
			$response['errors'] = array(
				'email' => 'already taken'
			);
		}
		else
		{
			$success = TRUE;
		}

		$response['success'] = $success;

		// return form packet for demonstration/testing purposes
		$response['debug_formPacket'] = $formPacket;

		return $response;
	}

	/**
	 * Get Basic Info
	 * put your comment there...
	 * This method configured with len=2, so 2 arguments will be sent
	 * in the order according to the client side specified paramOrder
	 * @remotable
	 * @access public
	 * @param number $userId
	 * @param string $foo
	 * @return array response packet
	 */
	public function getBasicInfo($userId, $foo)
	{
		return array(
			'success'	=> TRUE,
			'data'		=> array(
				'foo'		=> $foo,
				'name'		=> 'Aaron Conran',
				'company'	=> 'Ext JS, LLC',
				'email'		=> 'aaron@extjs.com'
			)
		);
	}

	/**
	 * Get Phone Ifo
	 * @remotable
	 * @access public
	 * @param number $userId
	 * @return array
	 */
	public function getPhoneInfo($userId)
	{
		return array(
			'success'	=> TRUE,
			'data'		=> array(
				'cell'		=> '443-555-1234',
				'office'	=> '1-800-CALLEXT',
				'home'		=> ''
			)
		);
	}

	/**
	 * Get Location Info
	 * @remotable
	 * @access public
	 * @param number $userId
	 * @return array
	 */
	public function getLocationInfo($userId)
	{
		return array(
			'success'	=> TRUE,
			'data'		=> array(
				'street'	=> '1234 Red Dog Rd.',
				'city'		=> 'Seminole',
				'state'		=> 'FL',
				'zip'		=> 33776
			)
		);
	}

}

/* End of file Profile.php */
/* Location: ./application/ext/direct/Profile.php */