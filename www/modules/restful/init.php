<?php defined('SYSPATH') or die('No direct script access.');

RESTful_Request::register_parser('application/json',						'RESTful_Request_Parser_JSON::parse');
RESTful_Request::register_parser('application/php-serialized',				'RESTful_Request_Parser_PHP::parse');
RESTful_Request::register_parser('application/x-www-form-urlencoded',		'RESTful_Request_Parser_URLENC::parse');
RESTful_Request::register_parser('text/plain',								'RESTful_Request_Parser_PLAIN::parse');

RESTful_Response::register_renderer('application/json',						'RESTful_Response_Renderer_JSON::render');
RESTful_Response::register_renderer('application/php-serialized',			'RESTful_Response_Renderer_PHP::render');
RESTful_Response::register_renderer('application/php-serialized-array',		'RESTful_Response_Renderer_PHP_Array::render');
RESTful_Response::register_renderer('application/php-serialized-object',	'RESTful_Response_Renderer_PHP_Object::render');
RESTful_Response::register_renderer('text/php-printr',						'RESTful_Response_Renderer_PRINTR::render');
RESTful_Response::register_renderer('text/plain',							'RESTful_Response_Renderer_PLAIN::render');