<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Version 17-09-2010

/**
 * Based on http://www.sencha.com/forum/showthread.php?79211-Ext.Direct-for-CodeIgniter/
 */

/**
 * File Class
 */
class File
{
	/**
	 * List Files
	 * @remotable
	 * @remoteName list
	 * @param ??? $folder
	 * @return mixed
	 */
	public function listFiles($folder)
	{
		if (substr($folder, 0, 3) === '../')
		{
			return 'Nice try buddy';
		}

		return array_slice(scandir($folder), 2);
	}

	/**
	 * Add
	 * @remotable
	 * @formHandler
	 * @param ??? $post
	 * @param ??? $files
	 * @return mixed
	 */
	public function add($post, $files)
	{
		if ($files && isset($files[$post['formField']]))
		{
			$file = $files[$post['formField']];

			move_uploaded_file($file['tmp_name'], 'data/'.$file['name']);

			return pathinfo('data/'.$file['name']);
		}
	}
}

/* End of file File.php */
/* Location: ./application/ext/direct/File.php */