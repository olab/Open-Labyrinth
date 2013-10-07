<?php defined('SYSPATH') or die('No direct script access.');

class Security {

	public static function token($key = null, $host = null)
	{
        if ($key == null){
            $key = (string)rand(0,100);
        }
        if ($host == null){
            $pageHttp = (@$_SERVER['HTTPS']) ? 'https://' : 'http://';
            $url = parse_url($pageHttp.$_SERVER['HTTP_HOST']);
            $host = $url['host'];
        }

        $secret_key = 0;
        $define = 279;
        for($i = 0; $i < strlen($key); $i++)
        {
            $secret_key += $key[$i] * $define;
        }

        $md5 = md5($secret_key.$host);
        return $key.'.'.$md5;
	}

	public static function check($_token)
	{
        $token = explode(".", $_token);
        $key = $token[0];
        if (isset($_SERVER['HTTP_REFERER'])){
            $url = parse_url($_SERVER['HTTP_REFERER']);
        }

        $md5 = Security::token($key, $url['host']);
        return ($md5 != $_token) ? false : true;
	}
}
