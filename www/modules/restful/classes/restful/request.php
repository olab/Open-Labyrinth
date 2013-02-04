<?php defined('SYSPATH') or die('No direct script access.');
/**
 * RESTful Request class
 *
 * @package		RESTful
 * @author		Michał Musiał
 * @copyright	(c) 2011 Michał Musiał
 */
class RESTful_Request
{
	/**
	 * @var	array
	 */
	protected static $_parsers = array();
	
	/**
	 * @param	string $type
	 * @return	mixed Returns all parsers if $type not specified, a parser callback if found or boolean FALSE otherwise
	 */
	public static function get_parser($type = NULL)
	{
		if ($type === NULL)
		{
			return self::$_parsers;
		}
		else
		{
			return Arr::get(self::$_parsers, $type, FALSE);
		}
	}
	
	/**
	 * @param	string $type Content MIME type
	 * @param	callback $callback
	 * @return	mixed Returns previous parser if one existed or TRUE otherwise
	 */
	public static function register_parser($type, $callback)
	{
		if (array_key_exists($type, self::$_parsers))
		{
			$return = self::$_parsers[$type];
		}
		else
		{
			$return = TRUE;
		}
		
		self::$_parsers[$type] = $callback;
		return $return;
	}
}
