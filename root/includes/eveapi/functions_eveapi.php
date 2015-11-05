<?php

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

// NO EDIT
define("EVEAPI_STANDINGSTYPE_CHARACTER", 0);
define("EVEAPI_STANDINGSTYPE_CORPORATION", 1);
define("EVEAPI_STANDINGSTYPE_ALLIANCE", 2);

define("INACTIVE_EVEAPI_INVALID", 98);
define("INACTIVE_EVEAPI_NONMEMBER", 99);

define("TBL_EVEAPI_STANDINGS", "eveapi_standings");


/**
* Sends output to the cronjob template.
*/
function cronlog($message, $character = "---")
{
	global $template;

	$template->assign_block_vars('cronloop', array(
		'TIMESTAMP'    => date("m-d-Y H:i:s"),
		'CHARACTER'    => htmlspecialchars($character),
		'ACTION'       => htmlspecialchars($message)
	));
}

/**
* Returns API info by username.
*/
function eveapi_getStandingsCharacterAPI()
{
    global $config, $db;
    
    $sql = "SELECT eveapi_keyid, eveapi_vcode 
        FROM " . USERS_TABLE . "
        WHERE username = '" . $db->sql_escape($config['eveapi_s_charactername']) . "'";
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);

    $returnArray = array(
        'characterName' => $config['eveapi_s_charactername'],
        'keyID' => $row['eveapi_keyid'],
        'vCode' => $row['eveapi_vcode'],
    );
    
    $db->sql_freeresult($result);

    return $returnArray;
}

/**
* Sets character's TeamSpeak permissions
*/
function eveapi_setTeamSpeakGroups($tsUserUid, $tsGroupsNew, $characterName = "---", $ts3_VirtualServer = false, $extraTSgroups = array())
{
    global $db, $config;
    
    $tsGroups = array();
	
    if(!is_array($tsGroupsNew))
    {
        $tsGroupsNew = array();
    }
	
	if(!empty($extraTSgroups))
	{
		$tsGroupsNew = array_merge($tsGroupsNew, $extraTSgroups);
		
		if(!empty($tsGroupsNew))
		{
			$tsGroupsNew = array_unique($tsGroupsNew);
		}
	}
    
    try
    {
        $nickname = $config['eveapi_ts_nickname'];
        if(eveapi_validateMixedalphanumeric($nickname) != 1)
        {
            $nickname = "Cyerus";
        }

        if(!$ts3_VirtualServer)
        {
            $ts3_VirtualServer = TeamSpeak3::factory("serverquery://" . $config["eveapi_ts_username"] . ":" . $config["eveapi_ts_password"] . "@" . $config["eveapi_ts_ip"] . ":" . $config["eveapi_ts_port_query"] . "/?server_port=" . $config["eveapi_ts_port_server"] . "&nickname=" . $nickname);
        }
        
        $tsClientDbId = $ts3_VirtualServer->clientFindDb($tsUserUid, true);
        foreach($tsClientDbId as $tsCurrentDbId)
        {
            $tsClientGroups = $ts3_VirtualServer->clientGetServerGroupsByDbid($tsCurrentDbId);
            foreach($tsClientGroups as $tsCurrentClientGroup)
            {
                $tsGroups[] = $tsCurrentClientGroup["sgid"];
            }
            
            foreach($tsGroups as $currentTSGroup)
            {
                if(!in_array($currentTSGroup, $tsGroupsNew))
                {
                    if($currentTSGroup == 0) continue;
                    if($currentTSGroup == $config['eveapi_ts_admin_tsgroup'] && $config['eveapi_ts_admin_switch']) continue;
                    
                    $cont = false;
                    for($i = 1; $i <= $config['eveapi_special_ts']; $i++)
                    {
                        if($currentTSGroup == $config['eveapi_ts_special_'.$i.'_tsgroup'] && $config['eveapi_ts_special_'.$i.'_switch'])
                        {
                            $cont = true;
                        }
                    }
                    
                    if(!$cont)
                    {
                        $groupInfo = $ts3_VirtualServer->serverGroupGetById($currentTSGroup);
                        if(!$groupInfo->savedb) continue;

                        cronlog("Removing user from TeamSpeak group -> " . $groupInfo->name, $characterName);
                        $ts3_VirtualServer->serverGroupClientDel($currentTSGroup, $tsCurrentDbId);
                    }
                }
            }
            
            foreach($tsGroupsNew as $currentTSGroup)
            {
				if($currentTSGroup == 0) continue;
				
				$groupInfo = $ts3_VirtualServer->serverGroupGetById($currentTSGroup);
				if(!$groupInfo->savedb) continue;
				
				if(!in_array($currentTSGroup, $tsGroups))
                {
                    cronlog("Adding user to TeamSpeak group -> " . $groupInfo->name, $characterName);
                    $ts3_VirtualServer->serverGroupClientAdd($currentTSGroup, $tsCurrentDbId);
                }
				else
				{
					cronlog("User already part of TeamSpeak group -> " . $groupInfo->name, $characterName);
				}
            }
        }
    }
    catch(Exception $e)
    {
        return $e->getCode() . " -> " . $e->getMessage();
    }
    
    return true;
}

/**
* Sets character's Jabber permissions
*/
function eveapi_setJabberAccess($userId, $jabber = false, $extraJabber = false, $characterName = "---")
{
	global $db;
	
    $sql = "SELECT userID
            FROM eveapi_jabber
            WHERE userID = " . $userId;
    $result = $db->sql_query($sql);
	$data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if(($jabber || $extraJabber) && !isset($data['userID']))
	{
		cronlog("Enabling Jabber access", $characterName);
		
		$sql = 'INSERT INTO eveapi_jabber ' . $db->sql_build_array('INSERT', array(
			"userID" => $userId
		));
		$db->sql_query($sql);
	}
}

/*
 * Creates a random string 
 */
function eveapi_randomString($length) 
{
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	return substr(str_shuffle($chars), 0, $length);
}

/**
* Sets character's OpenFire permissions
*/
function eveapi_setOpenFireAccess($userId, $jabber = array(), $extraJabber = array(), $characterName = "---")
{
	global $db, $config, $phpbb_root_path, $phpEx;
	
	// Merge both jabber group arrays
	$merge = array_merge($jabber, $extraJabber);
	
	// Create the OpenFireUserService object.
	$pofus = new OpenFireUserService();

	// Set the required config parameters
	$pofus->secret = $config['eveapi_openfire_code'];
	$pofus->host = $config['eveapi_openfire_host'];
	$pofus->port = (isset($config['eveapi_openfire_port']) && $config['eveapi_openfire_port'] > 0) ? (string)$config['eveapi_openfire_port'] : "9090";

	// Get characters (forum username)
	$sql = "SELECT username, user_email, user_lang
			FROM " . USERS_TABLE . "
			WHERE user_id = " . $userId;
	$result = $db->sql_query($sql);
	$data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	// Clean username for support with Jabber
	// Changing ' to |
	// and <space> to _
	$clean_username = str_replace(" ", "_", str_replace("'", "|", $data['username']));
	
	// Check if we have any groups or not, determines whether or not we should disable the jabber account
	if(!empty($merge))
	{
		// Remove duplicates
		$merge = array_unique($merge);
		$jabberGroups = array();

		// Loop to remove empty strings
		foreach($merge as $currentMerge)
		{
			if(!empty($currentMerge))
			{
				$jabberGroups[] = $currentMerge;
			}
		}
		
		// Now that we have the information we need, let's try to update the jabber groups of the selected user
		$result = $pofus->updateUser($clean_username, false, false, false, $jabberGroups);
		
		// Check if we have an initial result
		if($result)
		{
			// We have a result, but check what the actual result type is
			if($result['result'])
			{
				// Cronlog it
				cronlog("Updating OpenFire groups => " . implode(", ", $jabberGroups), $characterName);

				// Command was a success, no further action required
				return true;
			}
			else
			{
				if($result['message'] == 'UserNotFoundException')
				{
					// User doesn't exist yet, time to create the user				
					// Create a random password
					$password = eveapi_randomString(8);

					// Create the actual user
					$pofus->addUser($clean_username, $password, $data['username'], $data['user_email'], $jabberGroups);
					
					// Cronlog it
					cronlog("Adding OpenFire account with groups => "  . implode(", ", $jabberGroups), $characterName);

					// Email the newly created password to the user
					// Load phpBB's Messenger class
					include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
					$messenger = new messenger();

					// Load email text from template
					$messenger->template('openfire_added', $data['user_lang']);
					
					// Add recipient
					$messenger->to($data['user_email'], $data['username']);

					// Assign variables using in template
					$messenger->assign_vars(array(
						'USERNAME'	=> htmlspecialchars_decode($clean_username),
						'PASSWORD'	=> htmlspecialchars_decode($password),
						'HOST'		=> htmlspecialchars_decode($config['eveapi_openfire_host'])
					));

					// Send the actual email
					$messenger->send();
					$messenger->save_queue();

					// And we are done
					return true;
				}
			}
		}
	}
	else
	{
		// No groups found, remove the user from jabber
		$result = $pofus->deleteUser($clean_username);
		
		if($result && $result['result'])
		{
			cronlog("Removing OpenFire account", $characterName);
		}
	}
	
	return false;
}

/**
* Sets character's Forum permissions
*/
function eveapi_setForumGroups($userId, $forumGroupsNew, $characterName = "---")
{
    global $db, $config, $user;
	
    if(!is_array($forumGroupsNew))
    {
        $forumGroupsNew = array();
    }
    
    $forumGroups = array();
    $sql = "SELECT group_id
            FROM " . USER_GROUP_TABLE . "
            WHERE user_id = " . $userId;
    $result = $db->sql_query($sql);
    
    while ($row = $db->sql_fetchrow($result))
    {
        $forumGroups[] = $row['group_id'];
    }
    $db->sql_freeresult($result);
	
	// Grab all the forumgroups and put them into an array to be able to easily request values for EVE related stuffz
	$forumGroupInfo = array();
	$sql = "SELECT group_id, group_name, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire
			FROM " . GROUPS_TABLE;
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$forumGroupInfo[$row['group_id']]['group_name'] = $row['group_name'];
		$forumGroupInfo[$row['group_id']]['group_eveapi_special'] = $row['group_eveapi_special'];
		$forumGroupInfo[$row['group_id']]['group_eveapi_ts3'] = $row['group_eveapi_ts3'];
		$forumGroupInfo[$row['group_id']]['group_eveapi_jabber'] = $row['group_eveapi_jabber'];
		$forumGroupInfo[$row['group_id']]['group_eveapi_openfire'] = $row['group_eveapi_openfire'];
		
		// Check if the group name is actually a language identifier
		// If so, grab the correct language string from the language file instead
		// ( group prefix is G_ )
		if(isset($user->lang["G_" . $row['group_name']]))
		{
			$forumGroupInfo[$row['group_id']]['group_name'] = $user->lang["G_" . $row['group_name']];
		}
	}
	$db->sql_freeresult($result);
	
	// Set array for TeamSpeak groups if Forum Groups are set to immune.
	$returnGroups = array();
	
	// Set jabber access to default (just in case)
	$returnGroups["jabber"] = false;
	$returnGroups["openfire"] = array();
    
	// Loop groups to determine if we need to remove some permissions
    foreach($forumGroups as $currentForumGroup)
    {
        // User should not be in this group, or is the group special? Hhmmm...
		if(!isset($forumGroupsNew[$currentForumGroup]))
        {
            // Check if group is the default Registered Users group and ignore if it is.
			// TODO: Needs better check
			if($currentForumGroup == 2) continue;
			
			// Check if group is set as immune (special group), if not, remove user from group
			if(!$forumGroupInfo[$currentForumGroup]['group_eveapi_special'])
			{
                group_user_del($currentForumGroup, $userId);
                cronlog("Removing user from Forum group -> " . $forumGroupInfo[$currentForumGroup]['group_name'], $characterName);
			}
			else
			{
				// Group is immune, so don't remove it. Add TeamSpeak groupId to the list if the groupId >0
				if($forumGroupInfo[$currentForumGroup]['group_eveapi_ts3'])
				{
					$returnGroups["TS"][] = $forumGroupInfo[$currentForumGroup]['group_eveapi_ts3'];
				}
				
				// Add ejabberd access aswell
				if($forumGroupInfo[$currentForumGroup]['group_eveapi_jabber'])
				{
					$returnGroups["jabber"] = true;
				}
				
				// Add OpenFire access aswell
				if($forumGroupInfo[$currentForumGroup]['group_eveapi_openfire'] != "")
				{
					$returnGroups["openfire"][] = $forumGroupInfo[$currentForumGroup]['group_eveapi_openfire'];
				}
			}
        }
		else
		{
			// User is part of this group, add the TeamSpeak groupId to the list if the group  >0
			if($forumGroupInfo[$currentForumGroup]['group_eveapi_ts3'])
			{
				$returnGroups["TS"][] = $forumGroupInfo[$currentForumGroup]['group_eveapi_ts3'];
			}
				
			// Add ejabberd access aswell
			if($forumGroupInfo[$currentForumGroup]['group_eveapi_jabber'])
			{
				$returnGroups["jabber"] = true;
			}
			
			// Add OpenFire access aswell
			if($forumGroupInfo[$currentForumGroup]['group_eveapi_openfire'] != "")
			{
				$returnGroups["openfire"][] = $forumGroupInfo[$currentForumGroup]['group_eveapi_openfire'];
			}
		}
    }

    if(!empty($forumGroupsNew))
    {
        // Maybe we need to add groups
		foreach($forumGroupsNew as $currentForumGroup => $defaultGroup)
        {
            if(!in_array($currentForumGroup, $forumGroups))
            {
                $tempDefault = ($defaultGroup) ? true : false;
                group_user_add($currentForumGroup, $userId, false, false, $tempDefault);
                cronlog("Adding user to Forum group -> " . $forumGroupInfo[$currentForumGroup]['group_name'], $characterName);
            }
			else
			{
				cronlog("User already part of Forum group -> " . $forumGroupInfo[$currentForumGroup]['group_name'], $characterName);
			}
			
			// User should be part of this group, add the TeamSpeak groupId to the list if the group  >0
			if($forumGroupInfo[$currentForumGroup]['group_eveapi_ts3'])
			{
				$returnGroups["TS"][] = $forumGroupInfo[$currentForumGroup]['group_eveapi_ts3'];
			}
				
			// Add ejabberd access aswell
			if($forumGroupInfo[$currentForumGroup]['group_eveapi_jabber'])
			{
				$returnGroups["jabber"] = true;
			}
			
			// Add OpenFire access aswell
			if($forumGroupInfo[$currentForumGroup]['group_eveapi_openfire'] != "")
			{
				$returnGroups["openfire"][] = $forumGroupInfo[$currentForumGroup]['group_eveapi_openfire'];
			}
        }
    }
	
	// Automaticly re-add users to the Registered Users group (phpBB function checks if user is already part of this group).
	// Added this line to hopefully fix the issue of users not being added to groups when all "Default Groups"-boxes have been set to disabled.
	group_user_add(2, $userId, false, false, true);

	// User should always be part of this group, add the TeamSpeak groupId to the list if the group  >0
	if($forumGroupInfo[2]['group_eveapi_ts3'])
	{
		$returnGroups["TS"][] = $forumGroupInfo[2]['group_eveapi_ts3'];
	}
	
	// And ejabberd
	if($forumGroupInfo[2]['group_eveapi_jabber'])
	{
		$returnGroups["jabber"] = $forumGroupInfo[2]['group_eveapi_jabber'];
	}
	
	// And OpenFire
	if($forumGroupInfo[2]['group_eveapi_openfire'] != "")
	{
		$returnGroups["openfire"][] = $forumGroupInfo[2]['group_eveapi_openfire'];
	}
    
    return $returnGroups;
}

/**
* Determines whether or not account should be disabled based on supplied API error nr.
*/
function eveapi_checkDisableOnApiError($errorNr)
{
    if(($errorNr >= 222 && $errorNr < 300) ||  $errorNr == 201 ||  $errorNr == 521)
    {
        return true;
    }
    
    return false;
}

/**
* Determines whether or not account should be disabled based on supplied HTTP error nr.
*/
function eveapi_checkDisableOnHttpError($errorNr)
{
    // CCP PrismX has once again changed the way the API resport back it's errors.
	// It now returns a HTTP statuscode with API error as body.
	// Updated fail should forward those errors as PhealAPIException without PhealHTTPException for the known HTTP statuscodes
	// Unknown status codes should be sent to this function, and most likely will have to do with a failing cluster.
	// In other words; Regardless of errorcode, account should not become disabled, so always return false.
	/*if($errorNr == 403)
    {
        return true;
    }*/
    
    return false;
}

/**
* Grabs and returns accessMask
*/
function eveapi_getAccessMask()
{
    global $config;
	
	// Module system is a bit limited, so we have to do it the /wrong/ way.
	// Each accessMask possibility is stored as boolean, so now we have to make it an integer (of sorts).
	$accessMask = 0;
	for($i = 1; $i <= 134217728; $i *= 2)
	{
		if($config['eveapi_accessmask_' . $i])
		{
			$accessMask += $i;
		}
	}
    
    return $accessMask;
}

/*
* Simple function to determine if the user has the correct accessMask, or if the user is using an accessMask with more access, like private characterinfo.
*/
function eveapi_validateAccessmask($accessMask, $checkCurrent = true)
{
	global $config;
	
	// 16777216 <- Private  - CharacterInfo
	// 8388608  <- Public   - CharacterInfo
	// 64       <- Public   - FacWarStats
	if(($checkCurrent && (($accessMask & eveapi_getAccessMask()) == eveapi_getAccessMask())) || (!$checkCurrent))
	{
		// Faction Warfare check is enabled, meaning the user should have flag 64 for FacWarStats enabled on their API key as well as CharacterInfo.
		if($config['eveapi_fw_masterswitch'] && $accessMask & 64 && ($accessMask & 8388608 || $accessMask & 16777216))
		{
			return true;
		}

		// Faction Warfare is disabled, so only check for CharacterInfo.
		if(!$config['eveapi_fw_masterswitch'] && ($accessMask & 8388608 || $accessMask & 16777216))
		{
			return true;
		}
	}
	
	return false;
}

/**
* Checks the Character's API to determine access to the forum.
*/
function eveapi_checkThisCharacter($keyID, $vCode, $characterName)
{
    global $config, $db;
    
    $charArray = array();
    $characterID = 0;
    
    $pheal = new Pheal($keyID, $vCode);

    try 
    {
        $result = $pheal->accountScope->APIKeyInfo();
        if(eveapi_validateAccessmask($result->key->accessMask))
        {
            $charArray["info"]["accessMask"] = $result->key->accessMask;
            
            foreach($result->key->characters as $character)
            {
                if(strtolower($character->characterName) == strtolower($characterName))
                {
                    $characterID = $character->characterID;
                    $characterName = $character->characterName;
                }
            }
        }
        else
        {
            $charArray["error"][] = "Wrong accessMask -> " . $characterName;
            $charArray["disable"] = true;
            cronlog("Wrong accessMask", $characterName);
            return $charArray;
        }
    } 
    catch(PhealHTTPException $e)
    {
        // HTTP error, so skip rest of the check.
        $charArray["error"][$e->getCode()] = $e->getMessage();
        $charArray["disable"] = eveapi_checkDisableOnHttpError($e->getCode());
        cronlog('EVE API HTTP error: ' . $e->getCode() . ' -> ' . $e->getMessage(), $characterName);
        return $charArray;
    }
    catch(PhealAPIException $e)
    {
        // API error, skip check and determine whether or not to disable on error nr.
        $charArray["error"][$e->getCode()] = $e->getMessage();
        $charArray["disable"] = eveapi_checkDisableOnApiError($e->getCode());
        cronlog('EVE API Key error: ' . $e->getCode() . ' -> ' . $e->getMessage(), $characterName);
        return $charArray;
    }
    catch(PhealConnectionException $e) 
    {
        // Connection error, skip rest of the check
        $charArray["error"][$e->getCode()] = $e->getMessage();
        $charArray["disable"] = false;
        cronlog('EVE API Connection error: ' . $e->getCode() . ' -> ' . $e->getMessage(), $characterName);
        return $charArray;
    }
    catch(PhealException $e) 
    {
        // Any other error, in other words "Unknown error". Disable is a must to determine what may be causing this
        $charArray["error"][$e->getCode()] = $e->getMessage();
        $charArray["disable"] = true;
        cronlog('EVE API Generic error: ' . $e->getCode() . ' -> ' . $e->getMessage(), $characterName);
        return $charArray;
    }
    
    if($characterID > 0)
    {
		$result = $pheal->eveScope->characterInfo(array("characterID" => $characterID));
		$userCorporationName = $result->corporation;
		$userAllianceName = $result->alliance;

		$charArray["info"]["characterId"] = $characterID;
		$charArray["info"]["characterName"] = $characterName;
		$charArray["info"]["corporation"] = $userCorporationName;
		$charArray["info"]["alliance"] = $userAllianceName;
		
		$charArray["forumGroups"] = array();
		$charArray["TSGroups"] = array();
		$charArray["jabber"] = false;
		$charArray["openfire"] = array();
		
        // Standings
        if($config['eveapi_s_masterswitch'])
        {
            // Before we begin, we will remove the reports the user issued.
            $sql = 'SELECT standingsName, standingsAmount, standingsType FROM ' . TBL_EVEAPI_STANDINGS;
            $result = $db->sql_query($sql);

            while ($row = $db->sql_fetchrow($result))
            {
                if($row['standingsType'] == EVEAPI_STANDINGSTYPE_CHARACTER && !$config['eveapi_s_character']) continue;
                if($row['standingsType'] == EVEAPI_STANDINGSTYPE_CORPORATION && !$config['eveapi_s_corporation']) continue;
                if($row['standingsType'] == EVEAPI_STANDINGSTYPE_ALLIANCE && !$config['eveapi_s_alliance']) continue;
                
                if($row['standingsType'] == EVEAPI_STANDINGSTYPE_CHARACTER)
                {
                    $cronlogType = "Character";
                }
                elseif($row['standingsType'] == EVEAPI_STANDINGSTYPE_CORPORATION)
                {
                    $cronlogType = "Corporation";
                }
                elseif($row['standingsType'] == EVEAPI_STANDINGSTYPE_ALLIANCE)
                {
                    $cronlogType = "Alliance";
                }
                
                $foundStandings = false;
                if(strlen($userAllianceName) > 0 && $row['standingsName'] == $userAllianceName)
                {
                    $cronlogCharType = "Alliance";
                    $foundStandings = true;
                }
                elseif(strlen($userCorporationName) > 0 && $row['standingsName'] == $userCorporationName)
                {
                    $cronlogCharType = "Corporation";
                    $foundStandings = true;
                }
                elseif($row['standingsName'] == $characterName)
                {
                    $cronlogCharType = "Character";
                    $foundStandings = true;
                }
                
                if($foundStandings)
                {
                    if($config['eveapi_s_red_switch'] && $row['standingsAmount'] == -10)
                    {
                        cronlog("Standings found -> {$cronlogType}-level has Terrible standings towards user's {$cronlogCharType}", $characterName);
                        $charArray["forumGroups"][$config['eveapi_s_red_group']] = $config['eveapi_s_red_default'];
                        $charArray["TSGroups"][] = $config['eveapi_s_red_tsgroup'];
						$charArray["jabber"] = ($config['eveapi_s_red_jabber']) ? true : $charArray["jabber"];
						
						if(isset($config['eveapi_s_red_openfire']) && !empty($config['eveapi_s_red_openfire']))
						{
							$charArray["openfire"][] = $config['eveapi_s_red_openfire'];
						}
                    }
                    elseif($config['eveapi_s_orange_switch'] && $row['standingsAmount'] == -5)
                    {
                        cronlog("Standings found -> {$cronlogType}-level has Bad standings towards user's {$cronlogCharType}", $characterName);
                        $charArray["forumGroups"][$config['eveapi_s_orange_group']] = $config['eveapi_s_orange_default'];
                        $charArray["TSGroups"][] = $config['eveapi_s_orange_tsgroup'];
						$charArray["jabber"] = ($config['eveapi_s_orange_jabber']) ? true : $charArray["jabber"];
						
						if(isset($config['eveapi_s_orange_openfire']) && !empty($config['eveapi_s_orange_openfire']))
						{
							$charArray["openfire"][] = $config['eveapi_s_orange_openfire'];
						}
                    }
                    elseif($config['eveapi_s_neutral_switch'] && $row['standingsAmount'] == 0)
                    {
                        cronlog("Standings found -> {$cronlogType}-level has Neutral standings towards user's {$cronlogCharType}", $characterName);
                        $charArray["forumGroups"][$config['eveapi_s_neutral_group']] = $config['eveapi_s_neutral_default'];
                        $charArray["TSGroups"][] = $config['eveapi_s_neutral_tsgroup'];
						$charArray["jabber"] = ($config['eveapi_s_neutral_jabber']) ? true : $charArray["jabber"];
						
						if(isset($config['eveapi_s_neutral_openfire']) && !empty($config['eveapi_s_neutral_openfire']))
						{
							$charArray["openfire"][] = $config['eveapi_s_neutral_openfire'];
						}
                    }
                    elseif($config['eveapi_s_lightblue_switch'] && $row['standingsAmount'] == 5)
                    {
                        cronlog("Standings found -> {$cronlogType}-level has Good standings towards user's {$cronlogCharType}", $characterName);
                        $charArray["forumGroups"][$config['eveapi_s_lightblue_group']] = $config['eveapi_s_lightblue_default'];
                        $charArray["TSGroups"][] = $config['eveapi_s_lightblue_tsgroup'];
						$charArray["jabber"] = ($config['eveapi_s_lightblue_jabber']) ? true : $charArray["jabber"];
						
						if(isset($config['eveapi_s_lightblue_openfire']) && !empty($config['eveapi_s_lightblue_openfire']))
						{
							$charArray["openfire"][] = $config['eveapi_s_lightblue_openfire'];
						}
                    }
                    elseif($config['eveapi_s_blue_switch'] && $row['standingsAmount'] == 10)
                    {
                        cronlog("Standings found -> {$cronlogType}-level has Excellent standings towards user's {$cronlogCharType}", $characterName);
                        $charArray["forumGroups"][$config['eveapi_s_blue_group']] = $config['eveapi_s_blue_default'];
                        $charArray["TSGroups"][] = $config['eveapi_s_blue_tsgroup'];
						$charArray["jabber"] = ($config['eveapi_s_blue_jabber']) ? true : $charArray["jabber"];
						$charArray["openfire"][] = $config['eveapi_s_blue_openfire'];
						
						if(isset($config['eveapi_s_blue_openfire']) && !empty($config['eveapi_s_blue_openfire']))
						{
							$charArray["openfire"][] = $config['eveapi_s_blue_openfire'];
						}
                    }
                }
            }
            $db->sql_freeresult($result);
        }

        // Faction Warfare
        if($config['eveapi_fw_masterswitch'])
        {
            try 
            {
                $foundFactionWarfare = false;
                $result = $pheal->charScope->FacWarStats(array("characterID" => $characterID));
                
                //cronlog($result->factionName);
                if($result->factionName == "Amarr Empire" && $config['eveapi_fw_a_switch'])
                {
                    $foundFactionWarfare = true;
                    $charArray["forumGroups"][$config['eveapi_fw_a_group']] = $config['eveapi_fw_a_default'];
                    $charArray["TSGroups"][] = $config['eveapi_fw_a_tsgroup'];
					$charArray["jabber"] = ($config['eveapi_fw_a_jabber']) ? true : $charArray["jabber"];
					
					if(isset($config['eveapi_fw_a_jabber']) && !empty($config['eveapi_fw_a_jabber']))
					{
						$charArray["openfire"][] = $config['eveapi_fw_a_jabber'];
					}
                }
                elseif($result->factionName == "Caldari State" && $config['eveapi_fw_c_switch'])
                {
                    $foundFactionWarfare = true;
                    $charArray["forumGroups"][$config['eveapi_fw_c_group']] = $config['eveapi_fw_c_default'];
                    $charArray["TSGroups"][] = $config['eveapi_fw_c_tsgroup'];
					$charArray["jabber"] = ($config['eveapi_fw_c_jabber']) ? true : $charArray["jabber"];
					
					if(isset($config['eveapi_fw_c_jabber']) && !empty($config['eveapi_fw_c_jabber']))
					{
						$charArray["openfire"][] = $config['eveapi_fw_c_jabber'];
					}
                }
                elseif($result->factionName == "Gallente Federation" && $config['eveapi_fw_g_switch'])
                {
                    $foundFactionWarfare = true;
                    $charArray["forumGroups"][$config['eveapi_fw_g_group']] = $config['eveapi_fw_g_default'];
                    $charArray["TSGroups"][] = $config['eveapi_fw_g_tsgroup'];
					$charArray["jabber"] = ($config['eveapi_fw_g_jabber']) ? true : $charArray["jabber"];

					if(isset($config['eveapi_fw_g_jabber']) && !empty($config['eveapi_fw_g_jabber']))
					{
						$charArray["openfire"][] = $config['eveapi_fw_g_jabber'];
					}
                }
                elseif($result->factionName == "Minmatar Republic" && $config['eveapi_fw_m_switch'])
                {
                    $foundFactionWarfare = true;
                    $charArray["forumGroups"][$config['eveapi_fw_m_group']] = $config['eveapi_fw_m_default'];
                    $charArray["TSGroups"][] = $config['eveapi_fw_m_tsgroup'];
					$charArray["jabber"] = ($config['eveapi_fw_m_jabber']) ? true : $charArray["jabber"];
					
					if(isset($config['eveapi_fw_m_openfire']) && !empty($config['eveapi_fw_m_openfire']))
					{
						$charArray["openfire"][] = $config['eveapi_fw_m_openfire'];
					}
                }
                
                if($foundFactionWarfare)
                {
                    cronlog("User is part of Faction Warfare -> " . htmlentities($result->factionName), $characterName);
                }
            } 
            catch(PhealHTTPException $e)
            {
				// HTTP error, so skip rest of the check.
				$charArray["error"][$e->getCode()] = $e->getMessage();
				$charArray["disable"] = eveapi_checkDisableOnHttpError($e->getCode());
				cronlog('EVE API HTTP error: ' . $e->getCode() . ' -> ' . $e->getMessage(), $characterName);
				return $charArray;
            }
            catch(PhealAPIException $e)
            {
                // Skip error 124 since it's just the message that the character you are checking doesn't do Faction Warfare
                if($e->getCode() != 124)
                {
                    // API error, skip check and determine whether or not to disable on error nr.
                    $charArray["error"][$e->getCode()] = $e->getMessage();
                    $charArray["disable"] = eveapi_checkDisableOnApiError($e->getCode());
                    cronlog('EVE API Key error: ' . $e->getCode() . ' -> ' . $e->getMessage(), $characterName);
                    return $charArray;
                }
            }
            catch(PhealConnectionException $e) 
            {
                // Connection error, skip rest of the check
                $charArray["error"][$e->getCode()] = $e->getMessage();
                $charArray["disable"] = false;
                cronlog('EVE API Connection error: ' . $e->getCode() . ' -> ' . $e->getMessage(), $characterName);
                return $charArray;
            }
            catch(PhealException $e) 
            {
                // Any other error, in other words "Unknown error". Disable is a must to determine what may be causing this
                $charArray["error"][$e->getCode()] = $e->getMessage();
                $charArray["disable"] = true;
                cronlog('EVE API Generic error: ' . $e->getCode() . ' -> ' . $e->getMessage(), $characterName);
                return $charArray;
            }
        }
		
        // Alliances
        if($config['eveapi_a_masterswitch'])
        {
            $foundAlliance = false;
            
            for($i = 1; $i <= $config['eveapi_special_alliances']; $i++)
            {
                if(!$config['eveapi_a_'.$i.'_switch']) continue;

                if(strtolower($config['eveapi_a_'.$i.'_name']) == strtolower($userAllianceName) && strlen($userAllianceName) > 0)
                {
                    $foundAlliance = true;
                    
                    $charArray["forumGroups"][$config['eveapi_a_'.$i.'_group']] = $config['eveapi_a_'.$i.'_default'];
                    $charArray["TSGroups"][] = $config['eveapi_a_'.$i.'_tsgroup'];
					$charArray["jabber"] = ($config['eveapi_a_'.$i.'_jabber']) ? true : $charArray["jabber"];
					
					if(isset($config['eveapi_a_'.$i.'_openfire']) && !empty($config['eveapi_a_'.$i.'_openfire']))
					{
						$charArray["openfire"][] = $config['eveapi_a_'.$i.'_openfire'];
					}
                }
            }
            
            if($foundAlliance)
            {
                cronlog("User is part of alliance -> " . htmlentities($userAllianceName), $characterName);
            }
        }
		
		// Corporations
        if($config['eveapi_c_masterswitch'])
        {
            $foundCorporation = false;
            
            for($i = 1; $i <= $config['eveapi_special_corporations']; $i++)
            {
                if(!$config['eveapi_c_'.$i.'_switch']) continue;

                if(strtolower($config['eveapi_c_'.$i.'_name']) == strtolower($userCorporationName) && strlen($userCorporationName) > 0)
                {
                    $foundCorporation = true;
                    
                    $charArray["forumGroups"][$config['eveapi_c_'.$i.'_group']] = $config['eveapi_c_'.$i.'_default'];
                    $charArray["TSGroups"][] = $config['eveapi_c_'.$i.'_tsgroup'];
					$charArray["jabber"] = ($config['eveapi_c_'.$i.'_jabber']) ? true : $charArray["jabber"];
					
					if(isset($config['eveapi_c_'.$i.'_openfire']) && !empty($config['eveapi_c_'.$i.'_openfire']))
					{
						$charArray["openfire"][] = $config['eveapi_c_'.$i.'_openfire'];
					}
                }
            }
            
            if($foundCorporation)
            {
                cronlog("User is part of corporation -> " . htmlentities($userCorporationName), $characterName);
            }
        }

		
        // This piece of *#^! code is kept for legacy purposes, as it took me over a year to figure out what was causing the bug
        // where Users were not always being added to the different groups if no Default-group was set.
        // I'm going MAD I tell you! GGRRRRR MAAADDDDD!!
        // OOoohhh, cookies! NOM NOM NOM
        /*
        if(!empty($charArray["forumGroups"]))
        {
            $charArray["forumGroups"] = array_unique($charArray["forumGroups"]);
        }
        */
        if(!empty($charArray["TSGroups"]))
        {
            $charArray["TSGroups"] = array_unique($charArray["TSGroups"]);
        }
        if(!empty($charArray["openfire"]))
        {
            $charArray["openfire"] = array_unique($charArray["openfire"]);
        }
		
        if(empty($charArray["forumGroups"]) && $config['eveapi_nonmember'])
        {
            cronlog("User is a Non-Member", $characterName);
        }
    }
    else
    {
        $charArray["error"][] = "Character not found -> " . $characterName;
        $charArray["disable"] = true;
        cronlog("Character not found", $characterName);
    }
    
    return $charArray;
}

/**
* Returns the Characters ID.
*/
function eveapi_getCharacterID($characterName)
{
    $pheal = new Pheal();

    try 
    {
        $result = $pheal->eveScope->CharacterID(array("names" => $characterName));
        return $result->characters[0]->characterID;
    } 
    catch(PhealException $e) 
    {
        return false;
    }
}

/**
* Disables the forum account for this user.
*/
function eveapi_disableForumAccount($userId, $username, $eveapi_ts, $reason = INACTIVE_EVEAPI_INVALID, $ts3_VirtualServer = false)
{
    global $config;
	
	if($config['eveapi_ts_masterswitch'] && !empty($eveapi_ts))
    {
        // Removing TeamSpeak permissions
        eveapi_setTeamSpeakGroups($eveapi_ts, array(), $username, $ts3_VirtualServer, array());
    }
	
	if($config['eveapi_openfire_switch'])
	{
		// Removing OpenFire permissions
		eveapi_setOpenFireAccess($userId, array(), array(), $username);
	}
    
    // Removing Forum permissions
    eveapi_setForumGroups($userId, array(), $username);

    //remove sessions (effectively logging out the user)
    cronlog("Removing user sessions and disabling account", $username);
    user_active_flip('deactivate', $userId, $reason);
}

/**
* Downloads the character portrait from EVE as the new user's avatar.
* @param mixed $id The ID of the character whose portrait is being retrieved.
* @param int $user_id User ID of the account to apply the avatar to.  Optional.  Used during registration. 
* @return filename for the newly downloaded avatar.
*/
function eveapi_getPortrait($id, $user_id = false)
{
	global $config, $user, $phpbb_root_path;
	
        $user_id = ($user_id) ? $user_id : $user->data['user_id'];
	$input = 'http://image.eveonline.com/Character/' . $id . '_256.jpg';
	$db_filename = $user_id . "_" . time() . ".jpg";
	$filename = get_avatar_filename($db_filename);
	$output = (!defined('ADMIN_START')) ? $config['avatar_path'] . "/" . $filename : $phpbb_root_path . $config['avatar_path'] . "/" . $filename;
        
	$fgc = curl_file_get_contents($input);
	
	if(!$fgc) 
	{
            return false;
	}
	if(file_exists($output)) 
	{
            unlink($output);
	}
	file_put_contents($output, $fgc);
	return $db_filename;
}

/**
* Uses cURL to access EVE API
*
* @param string $url The url for the XML file to request.
* @return the page
*/
function curl_file_get_contents($url)
{
	$ch = curl_init();
	$timeout = 5; // set to zero for no timeout
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

	ob_start();
	curl_exec($ch);
	curl_close($ch);
	$file_contents = ob_get_contents();
	ob_end_clean();
	
	return $file_contents;
}

/**
* Validate string to only consist of letters and numbers
*
* @param string $str String to check.
* @return 1 for found, 0 for not-found, and FALSE on error.
*/
function eveapi_validateMixedalphanumeric($str) 
{
    return preg_match('/^[a-zA-Z0-9]+$/',$str);
}

/**
* Function to correctly create the IGB link for fittings
*/
function eveapi_ingameLinkLoop($slotArray, $slotType, $switch = true)
{
    $partOfLink = "";
    
    if(isset($slotArray[$slotType]))
    {
        foreach($slotArray[$slotType] as $item => $amount)
        {
            if($switch)
            {
                $partOfLink .= ":" . $item . ";" . $amount;
            }
            else
            {
                $partOfLink .= ":" . $item;
            }
        }
    }
    else
    {
        $partOfLink = ":";
    }
    
    return $partOfLink;
}

?>
