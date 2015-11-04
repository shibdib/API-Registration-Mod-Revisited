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
$sql = "DROP TABLE IF EXISTS `eveapi_items`";
$db->sql_query($sql);

// Create new eveapi_items
$sql = "CREATE TABLE `eveapi_items` (
			`itemID` INT(6) UNSIGNED NOT NULL DEFAULT 0,
			`itemName` VARCHAR(100) NOT NULL DEFAULT '',
			`categoryID` INT(6) UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY  (`itemID`)
		)";
$db->sql_query($sql);

// Fill new table
$sql = "INSERT INTO `eveapi_items` (`itemID`, `itemName`, `categoryID`)
		SELECT invTypes.typeId as itemID, invTypes.typeName as itemName, invGroups.categoryID
		FROM invTypes
		INNER JOIN invGroups
		ON invTypes.groupID = invGroups.groupID
		INNER JOIN invCategories
		ON invGroups.categoryID = invCategories.categoryID
		WHERE invTypes.published = 1
		AND invGroups.categoryID IN (4, 5, 6, 7, 8, 9, 16, 17, 18, 20, 22, 23, 24, 25, 30, 32, 34, 35, 39, 40, 41, 42, 43, 63)
		AND invGroups.published = 1
		AND invCategories.published = 1
		ORDER BY itemID";
$db->sql_query($sql);

// Create output as needed by Umil
$sql = "SELECT itemID, itemName, categoryID
		FROM eveapi_items
		ORDER BY itemID";
$result = $db->sql_query($sql);

echo chr(36) . "umil->table_row_insert('eveapi_items', array(\r\n";

while ($row = $db->sql_fetchrow($result))
{
	$item_fix = str_replace("'", "\'", $row['itemName']);
	
	if($row['itemID'] == 30952)
	{
		$item_fix = "Dossier - Author Unknown";
	}
	
	echo "    array(\r\n";
	echo "        'itemID' => " . $row['itemID'] . ",\r\n";
	echo "        'itemName' => '" . $item_fix . "',\r\n";
	echo "        'categoryID' => " . $row['categoryID'] . ",\r\n";
	echo "    ),\r\n";
}
$db->sql_freeresult($result);

echo "));\r\n";
echo "\r\n\r\nDone\r\n";

?>