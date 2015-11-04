<?php

// Prepare the script to work within phpBB's environment.
define('IN_PHPBB', true);
$phpbb_root_path = '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . '/includes/functions_user.' . $phpEx);

// Hidden secret to confirm we are allowed to request this data (might be handled differently in the future)
$secret	= md5(md5($config['eveapi_ejabber_code']));

$type = request_var('type', "");
$result = false;

if($type != "" && $config['eveapi_jabber_masterswitch'] && $config['eveapi_ejabber_switch'])
{
    $challenge = request_var('challenge', '');

    if($challenge == $secret)
    {
        if($type == "checkAuth")
        {
            $user = urldecode(request_var('user', ''));
            $pass = urldecode(request_var('pass', ''));

            $user = str_replace("_", " ", base64_decode($user));
            $pass = base64_decode($pass);

            $result = checkAuth($user, $pass);
        }
        elseif($type == "isUser")
        {
            $user = urldecode(request_var('user', ''));
            $user = base64_decode($user);
			$userArray = array($user);

            $id = user_get_id_name(array(), $userArray, array(0, 3));
            if($id === false)
            {
                $result = true;
            }
        }
    }
}

$response_text = ($result) ? "true" : "false";

header("Content-Type:text/xml");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<result>\n";
echo "<response>{$response_text}</response>\n";
echo "</result>\n";

// --------------------------------------------

function checkAuth($username, $password)
{
	global $db, $config;

	// do not allow empty password
	if (!$password)
	{
		return false;
	}

	if (!$username)
	{
		return false;
	}

	$username_clean = utf8_clean_string($username);

	$sql = 'SELECT user_id, username, user_password, user_passchg, user_pass_convert, user_email, user_type, user_login_attempts
		FROM ' . USERS_TABLE . "
		WHERE username_clean = '" . $db->sql_escape($username_clean) . "'";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	if (!$row)
	{
		return false;
	}
	
	if($config['max_login_attempts'] && $row['user_login_attempts'] >= $config['max_login_attempts'])
	{
		return false;
	}

	// Check password ...
	if (!$row['user_pass_convert'] && phpbb_check_hash($password, $row['user_password']))
	{
		$sql = 'DELETE FROM ' . LOGIN_ATTEMPT_TABLE . '
			WHERE user_id = ' . $row['user_id'];
		$db->sql_query($sql);

		if ($row['user_login_attempts'] != 0)
		{
			// Successful, reset login attempts (the user passed all stages)
			$sql = 'UPDATE ' . USERS_TABLE . '
				SET user_login_attempts = 0
				WHERE user_id = ' . $row['user_id'];
			$db->sql_query($sql);
		}

		// User inactive...
		if ($row['user_type'] == USER_INACTIVE || $row['user_type'] == USER_IGNORE)
		{
			return false;
		}

		// Successful login... set user_login_attempts to zero...
		// Now search the Jabber table to determine access
		$sql = 'SELECT userID
			FROM eveapi_jabber
			WHERE userID = ' . $row['user_id'];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		// Row found, we are allowed to use Jabber
		if(count($row) == 1)
		{
			return true;
		}
		
		return false;
	}

	// Password incorrect - increase login attempts
	$sql = 'UPDATE ' . USERS_TABLE . '
		SET user_login_attempts = user_login_attempts + 1
		WHERE user_id = ' . (int) $row['user_id'] . '
			AND user_login_attempts < ' . LOGIN_ATTEMPTS_MAX;
	$db->sql_query($sql);

	// Give status about wrong password...
	return false;
}

?>