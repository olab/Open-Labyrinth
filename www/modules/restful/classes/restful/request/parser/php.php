<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PHP Request Data Parser class for applicatopm/php-serialized mime-type.
 *
 * @package		RESTful
 * @category	Parsers
 * @author		Michał Musiał
 * @copyright	(c) 2011 Michał Musiał
 */
class RESTful_Request_Parser_PHP implements RESTful_Request_IParser
{
	/**
	 * @param	string $data
	 * @return	mixed
	 */
	static public function parse($request_body)
	{
		return @unserialize($request_body);
	}
}
