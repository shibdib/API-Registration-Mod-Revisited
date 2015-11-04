<?php

// Prepare the script to work within phpBB's environment.
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();


// Delete old eveapi_items
$sql = "DROP TABLE IF EXISTS `eveapi_systems`";
$db->sql_query($sql);

// Create new eveapi_items
$sql = "CREATE TABLE `eveapi_systems` 
		(
			`systemID` INT(11) NOT NULL DEFAULT 0,
			`systemName` VARCHAR(100) NOT NULL DEFAULT '',
			PRIMARY KEY  (`systemID`)
		)";
$db->sql_query($sql);

// Fill new table
$sql = "INSERT INTO `eveapi_systems` (`systemID`, `systemName`)
		SELECT solarSystemID as systemID, solarSystemName as systemName
		FROM mapSolarSystems
		ORDER BY systemID";
$db->sql_query($sql);

// Create output as needed by Umil
$sql = "SELECT systemID, systemName
		FROM eveapi_systems
		ORDER BY systemID";
$result = $db->sql_query($sql);

echo chr(36) . "umil->table_row_insert('eveapi_systems', array(\r\n";

while ($row = $db->sql_fetchrow($result))
{
	echo "    array(\r\n";
	echo "        'systemID'  => " . $row['systemID'] . ",\r\n";
	echo "        'systemName'  => '" . $row['systemName'] . "',\r\n";
	echo "    ),\r\n";
}
$db->sql_freeresult($result);

echo "));\r\n";
echo "\r\n\r\nDone\r\n";

?>