<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Exception class for RESTful API implementation
 *
 * @package		RESTful
 * @category	Exceptions
 * @author		Michał Musiał
 * @copyright	(c) 2011 Michał Musiał
 */
class RESTful_Exception extends Kohana_Exception
{
	/**
	 * Inline exception handler, displays the error message, source of the
	 * exception, and the stack trace of the error.
	 *
	 * @uses    Kohana_Exception::text
	 * @param   object   exception object
	 * @return  boolean
	 */
	public static function handler(Exception $e)
	{
		try
		{
			// Get the exception information
			$type    = get_class($e);
			$code    = $e->getCode();
			$message = $e->getMessage();
			$file    = $e->getFile();
			$line    = $e->getLine();

			// Create a text version of the exception
			$error = Kohana_Exception::text($e);

			if (is_object(Kohana::$log))
			{
				// Add this exception to the log
				Kohana::$log->add(Log::ERROR, $error);

				// Make sure the logs are written
				Kohana::$log->write();
			}
			
			if (Kohana::$is_cli)
			{
				// Just display the text of the exception
				echo "\n{$error}\n";

				return TRUE;
			}

			if ($e instanceof ErrorException)
			{
				if (isset(Kohana_Exception::$php_errors[$code]))
				{
					// Use the human-readable error name
					$code = Kohana_Exception::$php_errors[$code];
				}
			}

			if ( ! headers_sent())
			{
				Request::$current->response()->status($code);
				Request::$current->response()->headers('Content-Type', 'text/plain; charset='.Kohana::$charset);
				Request::$current->response()->send_headers();
			}

			if (Kohana::$environment === Kohana::DEVELOPMENT)
			{
				echo $error;
			}
			else
			{
				echo $message;
			}

			return TRUE;
		}
		catch (Exception $e)
		{
			// Clean the output buffer if one exists
			ob_get_level() and ob_clean();

			// Make sure the proper content type is sent with a 500 status
			header('Content-Type: text/plain; charset='.Kohana::$charset, TRUE, 500);
			// Display the exception message
			if (Kohana::$environment === Kohana::DEVELOPMENT)
			{
				echo Kohana_Exception::text($e), "\n";
			}
			else
			{
				echo $e->getMessage(), "\n";
			}

			// Exit with an error status
			// exit(1);
		}	
	}
}
