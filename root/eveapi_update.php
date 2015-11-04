<?php

define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if(!$user->data['is_registered'])
{
    if ($user->data['user_id'] == ANONYMOUS)
    {
        login_box($phpbb_root_path . 'eveapi_update.' . $phpEx, 'Please fill in your Charactername, Password and working EVE API information to reactivate your account.', '', false, true, true);
    }
}

header('Location: ' . $phpbb_root_path . 'index.' . $phpEx);

?>