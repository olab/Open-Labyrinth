<?php defined('SYSPATH') or die('No direct script access.');
/**
 * RESTful Request Parser Interface
 *
 * @package		RESTful
 * @category	Interfaces
 * @author		Michał Musiał
 * @copyright	(c) 2011 Michał Musiał
 */
interface RESTful_Request_IParser
{
	/**
	 * @param string $request_body
	 */
	static public function parse($request_body);
}
