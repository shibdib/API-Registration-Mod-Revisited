/*
-- Query: 

DROP TABLE IF EXISTS `eveapi_systems`;
CREATE TABLE `eveapi_systems` (
	`systemID` INT(11) NOT NULL DEFAULT 0,
	`systemName` VARCHAR(100) NOT NULL DEFAULT "",
	PRIMARY KEY  (`systemID`)
);

-----

INSERT INTO `eveapi_systems` (`systemID`, `systemName`)
SELECT solarSystemID as systemID, solarSystemName as systemName
FROM mapSolarSystems
ORDER BY systemID


-- Date: 2012-06-30 19:42 - Cyerus
*/
