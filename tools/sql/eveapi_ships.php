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
$sql = "DROP TABLE IF EXISTS `eveapi_ships`";
$db->sql_query($sql);

// Create new eveapi_items
$sql = "CREATE TABLE `eveapi_ships` 
		(
		  `typeID` int(10) NOT NULL DEFAULT 0,
		  `typeName` varchar(255) NOT NULL DEFAULT '',
		  `Low` int(10) NOT NULL DEFAULT 0,
		  `Medium` int(10) NOT NULL DEFAULT 0,
		  `High` int(10) NOT NULL DEFAULT 0,
		  `Drone` int(10) NOT NULL DEFAULT 0,
		  `Rig` int(10) NOT NULL DEFAULT 0,
		  `Subsystem` int(10) NOT NULL DEFAULT 0,
		  `raceID` int(10) NOT NULL DEFAULT 0,
		  `raceName` varchar(255) NOT NULL DEFAULT '',
		  `Tech` varchar(255) NOT NULL DEFAULT '',
		  `groupName` varchar(255) NOT NULL DEFAULT '',
		  `marketGroupName` varchar(255) NOT NULL DEFAULT '',
		  `Icon` varchar(255) NOT NULL DEFAULT '',
		  PRIMARY KEY  (`typeID`)
		)";
$db->sql_query($sql);

// Fill new table
$sql = "INSERT INTO `eveapi_ships` (`typeID`, `typeName`, `Low`, `Medium`, `High`, `Drone`, `Rig`, `Subsystem`, `raceID`, `raceName`, `Tech`, `groupName`, `marketGroupName`)
		SELECT it.typeID, it.typeName, IFNULL(low.valueInt, IFNULL(low.valueFloat,0)) AS Low, IFNULL(med.valueInt, IFNULL(med.valueFloat,0)) AS Medium, IFNULL(high.valueInt, IFNULL(high.valueFloat,0)) AS High, IFNULL(drone.valueInt, IFNULL(drone.valueFloat,0)) AS Drone, IFNULL(rig.valueInt, IFNULL(rig.valueFloat,0)) AS Rig, IFNULL(sub.valueInt, IFNULL(sub.valueFloat,0)) AS Subsystem, it.raceID, r.raceName, IFNULL(tech.valueInt, IFNULL(tech.valueFloat,1)) AS Tech, g.groupName, mg.marketGroupName
		FROM invTypes AS it
		JOIN invGroups g on it.groupID = g.groupID
		JOIN invCategories c ON c.categoryID = g.categoryID
		JOIN chrRaces r ON it.raceID = r.raceID
		JOIN invMarketGroups mg ON it.marketGroupID = mg.marketGroupID
		LEFT JOIN dgmTypeAttributes AS low ON it.typeID = low.typeID AND low.attributeID = 12
		LEFT JOIN dgmTypeAttributes AS med ON it.typeID = med.typeID AND med.attributeID = 13
		LEFT JOIN dgmTypeAttributes AS high ON it.typeID = high.typeID AND high.attributeID = 14
		LEFT JOIN dgmTypeAttributes AS drone ON it.typeID = drone.typeID AND drone.attributeID = 283
		LEFT JOIN dgmTypeAttributes AS rig ON it.typeID = rig.typeID AND rig.attributeID = 1137
		LEFT JOIN dgmTypeAttributes AS sub ON it.typeID = sub.typeID AND sub.attributeID = 1367
		LEFT JOIN dgmTypeAttributes AS tech ON it.typeID = tech.typeID AND tech.attributeID = 422
		WHERE it.published = 1
		AND c.categoryId = 6";
$db->sql_query($sql);

// Manual fixes of data
manual_fix_data();

// Create output as needed by Umil
$sql = "SELECT typeID, typeName, Low, Medium, High, Drone, Rig, Subsystem, raceID, raceName, Tech, groupName, marketGroupName, Icon
		FROM eveapi_ships
		ORDER BY typeID";
$result = $db->sql_query($sql);

echo chr(36) . "umil->table_row_insert('eveapi_ships', array(\r\n";

while ($row = $db->sql_fetchrow($result))
{
	$typeName_fix = str_replace("'", "\'", $row['typeName']);
	
	echo "    array(\r\n";
	echo "        'typeID' => " . $row['typeID'] . ",\r\n";
	echo "        'typeName' => '" . $typeName_fix . "',\r\n";
	echo "        'Low' => " . $row['Low'] . ",\r\n";
	echo "        'Medium' => " . $row['Medium'] . ",\r\n";
	echo "        'High' => " . $row['High'] . ",\r\n";
	echo "        'Drone' => " . $row['Drone'] . ",\r\n";
	echo "        'Rig' => " . $row['Rig'] . ",\r\n";
	echo "        'Subsystem' => " . $row['Subsystem'] . ",\r\n";
	echo "        'raceID' => " . $row['raceID'] . ",\r\n";
	echo "        'raceName' => '" . $row['raceName'] . "',\r\n";
	echo "        'Tech' => '" . $row['Tech'] . "',\r\n";
	echo "        'groupName' => '" . $row['groupName'] . "',\r\n";
	echo "        'marketGroupName' => '" . $row['marketGroupName'] . "',\r\n";
	echo "        'Icon' => '" . $row['Icon'] . "',\r\n";
	echo "    ),\r\n";
}
$db->sql_freeresult($result);

echo "));\r\n";
echo "\r\n\r\nDone\r\n";


function manual_fix_data()
{
	global $db;
	
	$sql = 'UPDATE `eveapi_ships` SET `Tech` = "Tech III" WHERE `Tech` = "3"';
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Tech` = "Faction" WHERE `marketGroupName` = "Navy Faction" OR `marketGroupName` = "Pirate Faction" OR `marketGroupName` = "Special Frigates" OR `marketGroupName` = "Faction Carrier"';
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Tech` = "Tech II" WHERE `Tech` = "2"';
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Tech` = "Tech I" WHERE `Tech` = "1"';
	$db->sql_query($sql);
	
	// ------
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Caldari" WHERE `marketGroupName` = "Caldari"';
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Caldari" WHERE `marketGroupName` = "Caldari"';
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Amarr" WHERE `marketGroupName` = "Amarr"';
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Gallente" WHERE `marketGroupName` = "Gallente"';
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Minmatar" WHERE `marketGroupName` = "Minmatar"';
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Jove" WHERE `raceName` = "Jove"';
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "ORE" WHERE `marketGroupName` = "ORE"';
	$db->sql_query($sql);
	
	// ------
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Amarr_Navy" WHERE `raceName` = "Amarr" AND `marketGroupName` = "Navy Faction"';
	$db->sql_query($sql);

	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Caldari_Navy" WHERE `raceName` = "Caldari" AND `marketGroupName` = "Navy Faction"';
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Gallente_Navy" WHERE `raceName` = "Gallente" AND `marketGroupName` = "Navy Faction"';
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Minmatar_Navy" WHERE `raceName` = "Minmatar" AND `marketGroupName` = "Navy Faction"';
	$db->sql_query($sql);

	// ------
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Guristas" WHERE `typeID` IN (17715, 17918, 17930)';		// Gila, Rattlesnake, Worm
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Serpentis", `raceName` = "Gallente" WHERE `typeID` IN (17722, 17740, 17928)';	// Vigilant, Vindicator, Daredevil (also fix for Daredevil `raceName`)
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Angel_Cartel" WHERE `typeID` IN (17720, 17738, 17932)';		// Cynabal, Machariel, Dramiel
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Blood_Raiders" WHERE `typeID` IN (17920, 17922, 17926)';	// Bhaalgorn, Ashimmu, Cruour
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Sansha" WHERE `typeID` IN (3514, 3532, 17718, 17736, 17924)';	// Revenant, Echelon, Phantasm, Nightmare, Succubus
	$db->sql_query($sql);
	
	$sql = 'UPDATE `eveapi_ships` SET `Icon` = "Gallente" WHERE `typeID` = 2078';	// Zephyr
	$db->sql_query($sql);
}

?>