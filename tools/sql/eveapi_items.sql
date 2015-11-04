/*
-- Query: 

DROP TABLE IF EXISTS `eveapi_items`;
CREATE TABLE `eveapi_items` 
(
	`itemID` INT(6) UNSIGNED NOT NULL DEFAULT 0,
	`itemName` VARCHAR(100) NOT NULL DEFAULT "",
	`categoryID` INT(6) UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY  (`itemID`)
); 

-----

INSERT INTO `eveapi_items` (`itemID`, `itemName`, `categoryID`)
SELECT invTypes.typeId as itemID, invTypes.typeName as itemName, invGroups.categoryID
FROM invTypes
INNER JOIN invGroups
ON invTypes.groupID = invGroups.groupID
INNER JOIN invCategories
ON invGroups.categoryID = invCategories.categoryID
WHERE invTypes.published = 1
AND invGroups.categoryID IN (4, 5, 6, 7, 8, 9, 16, 17, 18, 20, 22, 23, 24, 25, 30, 32, 34, 35, 39, 40, 41, 42, 43)
AND invGroups.published = 1
AND invCategories.published = 1
ORDER BY itemID


-- Date: 2012-06-30 18:46 - Cyerus
*/

