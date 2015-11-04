<?php

define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$sql = "DROP TABLE IF EXISTS `eveapi_tmp`";
$db->sql_query($sql);

$sql = "CREATE TABLE `eveapi_tmp` (
	`user_id` MEDIUMINT(8) NOT NULL DEFAULT 0,
	`eveapi_keyid` INT UNSIGNED NOT NULL DEFAULT 0,
	`eveapi_vcode` VARCHAR(64) NOT NULL DEFAULT '',
	`eveapi_ts` VARCHAR(28) NOT NULL DEFAULT '',
	PRIMARY KEY (`user_id`)
)";
$db->sql_query($sql);

$sql = "INSERT INTO eveapi_tmp (user_id, eveapi_keyid, eveapi_vcode, eveapi_ts) 
SELECT user_id, eveapi_keyId, eveapi_vCode, eveapi_TS
FROM " . USERS_TABLE;
$db->sql_query($sql);

$sql = "ALTER TABLE " . USERS_TABLE . "
	DROP `eveapi_keyId`,
	DROP `eveapi_vCode`,
	DROP `eveapi_TS`";
$db->sql_query($sql);

echo "DONE!";

?>