<?php
/**
 * Abstract policy class to provide some default method definitions
 * 
 * The Policy class lets you write advanced class or role based ACL rules for
 * your users.
 * 
 * Writing class based ACL rules
 * ===
 * 
 * Create a class called Policy_<policy_name> that extends Policy and has a
 * single method inside of it, execute(), which takes a Model_Vendo_User and an
 * array parameter.
 * 
 * The Model_Vendo_User method will be the user that the ACL rule should be
 * checked against. You can then write your advanced logic to determin if the
 * user can perform the requested action.
 * 
 * Example
 * ---
 * We might need an ACL rule that checks if a user can edit a CMS page.
 * We'd use this line to do the check:
 * 
 * 	$user->can('edit_page', array('page' => $page));
 * 
 * Then our execute method could check the role of the user to make sure they
 * are an admin and return true if so, for example. You could also use the $page
 * parameter to do more advanced checks if certain pages can only be changed by
 * specific users.
 * 
 * Writing message based ACL rules
 * ===
 * 
 * Message based ACL rules are much simpler, and only work based on the user's
 * role. Create or edit a messages/policy.php file. Each first level array key
 * should be a string containing the policy name, and the value of that key
 * should be an array containing role => boolean rules for that rule.
 * 
 * Example
 * ---
 * In our first example could be simplified if we only care about the role the
 * user has:
 * 
 * 	return array(
 * 		'edit_page' => array(
 * 			Model_Vendo_Role::LOGIN => FALSE,
 * 			Model_Vendo_Role::ADMIN => TRUE,
 * 		)
 * 	);
 * 
 * The first LOGIN line is not required (all policies default to FALSE), but
 * it is shown for example.
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
abstract class Vendo_Policy
{
	const GENERAL_FAILURE = 0;

	static $last_code;

	/**
	 * Factory method to return a specific policy
	 * 
	 * @param string $name the name of class to return
	 *
	 * @return Policy
	 */
	public static function factory($name)
	{
		// Add the model prefix
		$class = 'Policy_'.$name;

		return new $class();
	}

	/**
	 * Method to execute a policy
	 * 
	 * @param Model_ACL_User $user  the user account to run the policy on
	 * @param array          $extra an array of extra parameters that this
	 *                              policy can use
	 *
	 * @return bool/int
	 */
	abstract public function execute(Model_Leap_User $user, array $extra = NULL);
}
