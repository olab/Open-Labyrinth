<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Media extends Controller {

	public $config;

	public function before()
	{
		parent::before();

		$this->config = Kohana::$config->load('media');
	}

	public function action_serve()
	{
		$filepath = $this->request->param('filepath');
		$uid = $this->request->param('uid');

		$cfs_file = Kohana::find_file('media', $filepath, FALSE);

		if ( ! $cfs_file)
			throw new Kohana_Http_Exception_404;

		// Send the file content as the response
		$this->response->body(file_get_contents($cfs_file));

		if ($this->config->cache)
		{
			// Save the contents to the public directory for future requests
			$public = strtr($this->config->public_dir, array(
				'<uid>/'    => $uid ? $uid.'/' : '',
				'<filepath>' => $filepath,
			));
			$directory = dirname($public);

			if ( ! is_dir($directory))
			{
				// Recursively create the directories needed for the file
				mkdir($directory.'/', 0777, TRUE);
			}

			file_put_contents($public, $this->response->body());
		}

		// Set the proper headers to allow caching
		$this->response->headers('Content-Type', (string) File::mime_by_ext(pathinfo($cfs_file, PATHINFO_EXTENSION)));
		$this->response->headers('Content-Length', (string) filesize($cfs_file));
		$this->response->headers('Last-Modified', (string) date('r', filemtime($cfs_file)));
	}
}
