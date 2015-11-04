<?php
/**
*
* acp_eveapi [English]
*
* @package language
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

// EVEAPI Settings
$lang = array_merge($lang, array(
	'ACP_CAT_EVEAPI'		=> 'EVE API modules',
	'EVEAPI_CORPORATIONS'	=> 'Corporations',
	'EVEAPI_ALLIANCES'		=> 'Alliances',
	'EVEAPI_STANDINGS'		=> 'Standings',
	'EVEAPI_FW'				=> 'Faction Warfare',
	'EVEAPI_TS'				=> 'TeamSpeak 3',
	'EVEAPI_JABBER'			=> 'Jabber',
	'EVEAPI_ACCESSMASK'		=> 'AccessMask',
	
	'LOG_CONFIG_GENERAL'		=> '<strong>Altered EVE API general settings</strong>',
	'LOG_CONFIG_CORPORATION'	=> '<strong>Altered EVE API corporation settings</strong>',
	'LOG_CONFIG_ALLIANCE'		=> '<strong>Altered EVE API alliance settings</strong>',
	'LOG_CONFIG_STANDINGS'		=> '<strong>Altered EVE API standings settings</strong>',
	'LOG_CONFIG_FACTIONWARFARE'	=> '<strong>Altered EVE API faction warfare settings</strong>',
	'LOG_CONFIG_TEAMSPEAK3'		=> '<strong>Altered EVE API TeamSpeak 3 settings</strong>',
	'LOG_CONFIG_JABBER'			=> '<strong>Altered EVE API Jabber settings</strong>',
	'LOG_CONFIG_ACCESSMASK'		=> '<strong>Altered EVE API AccessMask settings</strong>',
    
	'EVEAPI_DEFAULT'			=> 'Default group',
	'EVEAPI_DEFAULT_EXPLAIN'	=> 'Set this forum group as default',
));

$lang = array_merge($lang, array(
	'ALLOW_AVATARS'						=> 'Enable avatars',
	'ALLOW_AVATARS_EXPLAIN'				=> 'Allow general usage of avatars;<br />If you disable avatars in general or avatars of a certain mode, the disabled avatars will no longer be shown on the board, but users will still be able to download their own avatars in the User Control Panel.',
	'EVEAPI_VALIDATION'					=> 'Enable API validation',
	'EVEAPI_VALIDATION_EXPLAIN'			=> 'This enables the EVE API features for registration and member management.',
	'EVEAPI_NONMEMBER'					=> 'Allow non-member registration',
	'EVEAPI_NONMEMBER_EXPLAIN'			=> 'Allows members that do not match any criteria besides having correct API information to register on the forum.',
	
	'EVEAPI_PORTRAIT_SETTINGS'			=> 'Portrait settings',
	'EVEAPI_PORTRAIT'					=> 'EVE Portraits',
	'EVEAPI_PORTRAIT_EXPLAIN'			=> 'Allow EVE Portraits to be used as Avatars.',
	'EVEAPI_PORTRAIT_SIZE'				=> 'EVE Portrait size',
	'EVEAPI_PORTRAIT_SIZE_EXPLAIN'		=> 'Small is 64x64, Medium is 128x128, Large is 256x256.',
	'EVEAPI_PORTRAIT_SMALL'				=> 'Small',
	'EVEAPI_PORTRAIT_MEDIUM'			=> 'Medium',
	'EVEAPI_PORTRAIT_LARGE'				=> 'Large',
	'MAX_AVATAR_SIZE'					=> 'Maximum avatar dimensions',
	'MAX_AVATAR_SIZE_EXPLAIN'			=> 'Width x Height in pixels. Set to 256x256 for EVE Portraits.',
	'MIN_AVATAR_SIZE'					=> 'Minimum avatar dimensions',
	'MIN_AVATAR_SIZE_EXPLAIN'			=> 'Width x Height in pixels. Set to 64x64 for EVE Portraits.',
	'EVEAPI_PORTRAIT_RESIZE'			=> 'Force avatar resize',
	'EVEAPI_PORTRAIT_RESIZE_EXPLAIN'	=> 'Resizes all avatars to Portrait size setting set above.',

	'EVEAPI_CRONJOB_SETTINGS'			=> 'Cronjob settings',
	'EVEAPI_ADMIN_IMMUNE'				=> 'Admins immune?',
	'EVEAPI_ADMIN_IMMUNE_EXPLAIN'		=> 'Are users in the Administrator group immune to API-checks?',
	'EVEAPI_SPECIAL_IMMUNE'				=> '&#34;Special&#34;-group immune?',
	'EVEAPI_SPECIAL_IMMUNE_EXPLAIN'		=> 'Are users in this &#34;Special&#34; group immune to API-checks?',	
	'EVEAPI_SPECIAL_GROUP'				=> '&#34;Special&#34;-group',
	'EVEAPI_SPECIAL_GROUP_EXPLAIN'		=> 'This group is considered the &#34;Special&#34;-group.',	
));

$lang = array_merge($lang, array(
	'EVEAPI_CORPORATION_M'				=> 'Enable corporation management',
	'EVEAPI_CORPORATION_M_EXPLAIN'		=> 'Masterswitch to enable user\'s API checks against the list of corporations below.',
	'EVEAPI_CORPORATION'				=> 'Corporation',
	'EVEAPI_ENABLE_C'					=> 'Enable',
	'EVEAPI_ENABLE_C_EXPLAIN'			=> 'Enables the API check of this corporation.',
	'EVEAPI_NAME_C'						=> 'Name',
	'EVEAPI_NAME_C_EXPLAIN'				=> 'Name of the corporation in which membership is required.',
	'EVEAPI_JABBER_C'					=> 'ejabberd',
	'EVEAPI_JABBER_C_EXPLAIN'			=> 'Allow access to ejabberd if an user is part of this corporation.',
	'EVEAPI_OPENFIRE_C'					=> 'Assign OpenFire group',
	'EVEAPI_OPENFIRE_C_EXPLAIN'			=> 'If an user is part of this corporation, the following OpenFire group will be assigned.<br />Leave empty to disable.',
	'EVEAPI_ASSIGN_GROUP_C'				=> 'Assign forum group',
	'EVEAPI_ASSIGN_GROUP_C_EXPLAIN' 	=> 'If an user is part of this corporation, the following forum group will be assigned.',
	'EVEAPI_TSGROUP_C'					=> 'Assign TeamSpeak group',
	'EVEAPI_TSGROUP_C_EXPLAIN'			=> 'If an user is part of this corporation, the following TeamSpeak group will be assigned.<br />Set 0 to disable.',
));

$lang = array_merge($lang, array(
	'EVEAPI_ALLIANCE_M'					=> 'Enable alliance management',
	'EVEAPI_ALLIANCE_M_EXPLAIN'			=> 'Masterswitch to enable user\'s API checks against the list of alliances below.',
	'EVEAPI_ALLIANCE'					=> 'Alliance',
	'EVEAPI_ENABLE_A'					=> 'Enable',
	'EVEAPI_ENABLE_A_EXPLAIN'			=> 'Enables the API check of this alliance.',
	'EVEAPI_NAME_A'						=> 'Name',
	'EVEAPI_NAME_A_EXPLAIN'				=> 'Name of the alliance in which membership is required.',
	'EVEAPI_JABBER_A'					=> 'eabberd',
	'EVEAPI_JABBER_A_EXPLAIN'			=> 'Allow access to ejabberd if an user is part of this alliance.',
	'EVEAPI_OPENFIRE_A'					=> 'Assign OpenFire group',
	'EVEAPI_OPENFIRE_A_EXPLAIN'			=> 'If an user is part of this alliance, the following OpenFire group will be assigned.<br />Leave empty to disable.',
	'EVEAPI_ASSIGN_GROUP_A'				=> 'Assign forum group',
	'EVEAPI_ASSIGN_GROUP_A_EXPLAIN' 	=> 'If an user is part of this alliance, the following forum group will be assigned.',
	'EVEAPI_TSGROUP_A'					=> 'Assign TeamSpeak group',
	'EVEAPI_TSGROUP_A_EXPLAIN'			=> 'If an user is part of this alliance, the following TeamSpeak group will be assigned.<br />Set 0 to disable.',
	'EVEAPI_SEPERATE_A'					=> 'Enabled seperate group per corporation',
	'EVEAPI_SEPERATE_A_EXPLAIN'			=> 'This will enlist users in groups according to the corporations within the alliance. If such group doesn\'t exist, it will be made using the selected group above as a template.',
));

$lang = array_merge($lang, array(
	'EVEAPI_STANDINGS_M'				=> 'Enable standings management',
	'EVEAPI_STANDINGS_M_EXPLAIN'		=> 'Masterswitch to enable user\'s API checks against the standings selected below.',
	'EVEAPI_S_CHARACTER'				=> 'Enable character standings',
	'EVEAPI_S_CHARACTER_EXPLAIN'		=> 'Enables standing checks on character level.',
	'EVEAPI_S_CORPORATION'				=> 'Enable corporation standings',
	'EVEAPI_S_CORPORATION_EXPLAIN'		=> 'Enables standing checks on corporation level.',
	'EVEAPI_S_ALLIANCE'					=> 'Enable alliance standings',
	'EVEAPI_S_ALLIANCE_EXPLAIN'			=> 'Enables standing checks on alliance level.',
	'EVEAPI_S_CHARACTERNAME'			=> 'Character to grab standings from',
	'EVEAPI_S_CHARACTERNAME_EXPLAIN'	=> 'This character\'s API details will be used to grab the standings lists selected above.<br />Be sure to have the correct API permissions set for this character!',
	'EVEAPI_S_CORP_KEYID'				=> 'Character\'s keyID (Corporation level)',
	'EVEAPI_S_CORP_KEYID_EXPLAIN'		=> 'This character\'s keyID with Corporation-level access will be used to check Corporation and/or Alliance standings.<br />NOTE: Must belong to above selected character!',
	'EVEAPI_S_CORP_VCODE'				=> 'Character\'s vCode (Corporation level)',
	'EVEAPI_S_CORP_VCODE_EXPLAIN'		=> 'This character\'s vCode with Corporation-level access will be used to check Corporation and/or Alliance standings.<br />NOTE: Must belong to above selected character!',
    
	'EVEAPI_STANDINGS_RED'				=> 'Terrible standings',
	'EVEAPI_S_RED_ENABLE'				=> 'Enable terrible standings',
	'EVEAPI_S_RED_ENABLE_EXPLAIN'		=> 'Enables standing checks for -10.0 standings.',
	
	'EVEAPI_STANDINGS_ORANGE'			=> 'Bad standings',
	'EVEAPI_S_ORANGE_ENABLE'			=> 'Enable bad standings',
	'EVEAPI_S_ORANGE_ENABLE_EXPLAIN'	=> 'Enables standing checks for -5.0 standings.',
	
	'EVEAPI_STANDINGS_NEUTRAL'			=> 'Neutral standings',
	'EVEAPI_S_NEUTRAL_ENABLE'			=> 'Enable neutral standings',
	'EVEAPI_S_NEUTRAL_ENABLE_EXPLAIN'	=> 'Enables standing checks for 0.0 standings.',
	
	'EVEAPI_STANDINGS_LIGHTBLUE'		=> 'Good standings',
	'EVEAPI_S_LIGHTBLUE_ENABLE'			=> 'Enable good standings',
	'EVEAPI_S_LIGHTBLUE_ENABLE_EXPLAIN'	=> 'Enables standing checks for 5.0 standings.',
	
	'EVEAPI_STANDINGS_BLUE'				=> 'Excellent standings',
	'EVEAPI_S_BLUE_ENABLE'				=> 'Enable excellent standings',
	'EVEAPI_S_BLUE_ENABLE_EXPLAIN'		=> 'Enables standing checks for 10.0 standings.',
	
	'EVEAPI_S_ASSIGN_GROUP'				=> 'Assign forum group',
	'EVEAPI_S_ASSIGN_GROUP_EXPLAIN' 	=> 'If a character / corporation / alliance has this standing, the following forum group will be assigned.',
	'EVEAPI_S_JABBER'					=> 'ejabberd',
	'EVEAPI_S_JABBER_EXPLAIN'			=> 'Allow access to ejabberd if an user has this type of standing.',
	'EVEAPI_S_OPENFIRE'					=> 'Assign OpenFire group',
	'EVEAPI_S_OPENFIRE_EXPLAIN'			=> 'If a character / corporation / alliance has this standing, the following OpenFire group will be assigned.<br />Leave empty to disable.',
	'EVEAPI_S_TSGROUP'					=> 'Assign TeamSpeak group',
	'EVEAPI_S_TSGROUP_EXPLAIN'			=> 'If a character / corporation / alliance has this standing, the following TeamSpeak group will be assigned.<br />Set 0 to disable.',
));

$lang = array_merge($lang, array(
	'EVEAPI_FW_M'						=> 'Enable faction warfare management',
	'EVEAPI_FW_M_EXPLAIN'				=> 'Masterswitch to enable user\'s API checks against faction warfare selected below.',
	
	'EVEAPI_FW_AMARR'					=> 'Amarr Empire',
	'EVEAPI_FW_ENABLE_A'				=> 'Enable',
	'EVEAPI_FW_ENABLE_A_EXPLAIN'		=> 'Enables the API check of the Amarr Empire.',
	'EVEAPI_FW_GROUP_A'					=> 'Assign forum group',
	'EVEAPI_FW_GROUP_A_EXPLAIN'			=> 'If an user is part of the Amarr Empire, the following forum group will be assigned.',
	'EVEAPI_FW_JABBER_A'				=> 'Jabber',
	'EVEAPI_FW_JABBER_A_EXPLAIN'		=> 'Allow access to Jabber if an user is part of the Amarr Empire.',
	'EVEAPI_FW_OPENFIRE_A'				=> 'Assign OpenFire group',
	'EVEAPI_FW_OPENFIRE_A_EXPLAIN'		=> 'If an user is part of the Amarr Empire, the following OpenFire group will be assigned.<br />Leave empty to disable.',
	'EVEAPI_FW_TSGROUP_A'				=> 'Assign TeamSpeak group',
	'EVEAPI_FW_TSGROUP_A_EXPLAIN'		=> 'If an user is part of the Amarr Empire, the following TeamSpeak group will be assigned.<br />Set 0 to disable.',

	'EVEAPI_FW_CALDARI'					=> 'Caldari State',
	'EVEAPI_FW_ENABLE_C'				=> 'Enable',
	'EVEAPI_FW_ENABLE_C_EXPLAIN'		=> 'Enables the API check of the Caldari State.',
	'EVEAPI_FW_GROUP_C'					=> 'Assign forum group',
	'EVEAPI_FW_GROUP_C_EXPLAIN'			=> 'If an user is part of the Caldari State, the following forum group will be assigned.',
	'EVEAPI_FW_JABBER_C'				=> 'Jabber',
	'EVEAPI_FW_JABBER_C_EXPLAIN'		=> 'Allow access to Jabber if an user is part of the Caldari State.',
	'EVEAPI_FW_OPENFIRE_C'				=> 'Assign OpenFire group',
	'EVEAPI_FW_OPENFIRE_C_EXPLAIN'		=> 'If an user is part of the Caldari State, the following OpenFire group will be assigned.<br />Leave empty to disable.',
	'EVEAPI_FW_TSGROUP_C'				=> 'Assign TeamSpeak group',
	'EVEAPI_FW_TSGROUP_C_EXPLAIN'		=> 'If an user is part of the Caldari State, the following TeamSpeak group will be assigned.<br />Set 0 to disable.',

	'EVEAPI_FW_GALLENTE'				=> 'Gallente Federation',	
	'EVEAPI_FW_ENABLE_G'				=> 'Enable',
	'EVEAPI_FW_ENABLE_G_EXPLAIN'		=> 'Enables the API check of the Gallente Federation.',
	'EVEAPI_FW_GROUP_G'					=> 'Assign forum group',
	'EVEAPI_FW_GROUP_G_EXPLAIN'			=> 'If an user is part of the Gallente Federation, the following forum group will be assigned.',
	'EVEAPI_FW_JABBER_G'				=> 'Jabber',
	'EVEAPI_FW_JABBER_G_EXPLAIN'		=> 'Allow access to Jabber if an user is part of the Gallente Federation.',
	'EVEAPI_FW_OPENFIRE_G'				=> 'Assign OpenFire group',
	'EVEAPI_FW_OPENFIRE_G_EXPLAIN'		=> 'If an user is part of the Gallente Federation, the following OpenFire group will be assigned.<br />Leave empty to disable.',
	'EVEAPI_FW_TSGROUP_G'				=> 'Assign TeamSpeak group',
	'EVEAPI_FW_TSGROUP_G_EXPLAIN'		=> 'If an user is part of the Gallente Federation, the following TeamSpeak group will be assigned.<br />Set 0 to disable.',

	'EVEAPI_FW_MINMATAR'				=> 'Minmatar Republic',
	'EVEAPI_FW_ENABLE_M'				=> 'Enable',
	'EVEAPI_FW_ENABLE_M_EXPLAIN'		=> 'Enables the API check of the Minmatar Republic.',
	'EVEAPI_FW_GROUP_M'					=> 'Assign forum group',
	'EVEAPI_FW_GROUP_M_EXPLAIN'			=> 'If an user is part of the Minmatar Republic, the following forum group will be assigned.',
	'EVEAPI_FW_JABBER_M'				=> 'Jabber',
	'EVEAPI_FW_JABBER_M_EXPLAIN'		=> 'Allow access to Jabber if an user is part of the Minmatar Republic.',
	'EVEAPI_FW_OPENFIRE_M'				=> 'Assign OpenFire group',
	'EVEAPI_FW_OPENFIRE_M_EXPLAIN'		=> 'If an user is part of the Minmatar Republic, the following OpenFire group will be assigned.<br />Leave empty to disable.',
	'EVEAPI_FW_TSGROUP_M'				=> 'Assign TeamSpeak group',
	'EVEAPI_FW_TSGROUP_M_EXPLAIN'		=> 'If an user is part of the Minmatar Republic, the following TeamSpeak group will be assigned.<br />Set 0 to disable.',
));

$lang = array_merge($lang, array(
	'EVEAPI_TS_M'						=> 'Enable TeamSpeak 3 management',
	'EVEAPI_TS_M_EXPLAIN'				=> 'Masterswitch to enable TeamSpeak 3 management (cronjob and special TeamSpeak registration form).<br />Be sure to have this webserver added to the TeamSpeak server whitelist!',
	'EVEAPI_TS_IP'						=> 'Server IP',
	'EVEAPI_TS_IP_EXPLAIN'				=> 'IP address of the TeamSpeak server.',
	'EVEAPI_TS_USERNAME'				=> 'Serverquery username',
	'EVEAPI_TS_USERNAME_EXPLAIN'		=> 'Username to access the serverquery command prompt.',
	'EVEAPI_TS_PASSWORD'				=> 'Serverquery password',
	'EVEAPI_TS_PASSWORD_EXPLAIN'		=> 'Password to access the serverquery command prompt.',
	'EVEAPI_TS_PORT_SERVER'				=> 'Virtual server port',
	'EVEAPI_TS_PORT_SERVER_EXPLAIN'		=> 'Port used to connect to the virtual server.<br />Default: 9987',
	'EVEAPI_TS_PORT_QUERY'				=> 'Serverquery port',
	'EVEAPI_TS_PORT_QUERY_EXPLAIN'		=> 'Port used to allow remote server commands.<br />Default: 10011',
	'EVEAPI_TS_NICKNAME'				=> 'Serverquery nickname',
	'EVEAPI_TS_NICKNAME_EXPLAIN'		=> 'This nickname will be used mask the webserver.',
    
	'EVEAPI_TS_SPECIAL_GROUPS'			=> 'Special groups',
	'EVEAPI_TS_ADMIN'					=> '&#34;Admin&#34;-group immune?',
	'EVEAPI_TS_ADMIN_EXPLAIN'			=> 'This options enables immunity for the &#34;Admin&#34;-group.',
	'EVEAPI_TS_ADMIN_TSGROUP'			=> '&#34;Admin&#34;-group',
	'EVEAPI_TS_ADMIN_TSGROUP_EXPLAIN'	=> 'The TeamSpeak &#34;Admin&#34;-group identified by an integer.',
	'EVEAPI_TS_SPECIAL'					=> '&#34;Special&#34;-group immune?',
	'EVEAPI_TS_SPECIAL_EXPLAIN'			=> 'This options enables immunity for this &#34;Special&#34;-group.',
	'EVEAPI_TS_SPECIAL_TSGROUP'			=> '&#34;Special&#34;-group',
	'EVEAPI_TS_SPECIAL_TSGROUP_EXPLAIN'	=> 'A TeamSpeak &#34;Special&#34;-group identified by an integer.',
));

$lang = array_merge($lang, array(
	'EVEAPI_SPECIAL_C'					=> 'Amount of Corporations',
	'EVEAPI_SPECIAL_C_EXPLAIN'			=> 'Amount of Corporations shown below.',
	'EVEAPI_SPECIAL_A'					=> 'Shown Alliances',
	'EVEAPI_SPECIAL_A_EXPLAIN'			=> 'Amount of Alliances shown below.',
	'EVEAPI_SPECIAL_F'					=> 'Special forumgroups',
	'EVEAPI_SPECIAL_F_EXPLAIN'			=> 'Amount of \'Special\' forumgroups shown below.',
	'EVEAPI_SPECIAL_TS'					=> 'Special TeamSpeak-groups',
	'EVEAPI_SPECIAL_TS_EXPLAIN'			=> 'Amount of \'Special\' TeamSpeak-groups shown below.',
));

$lang = array_merge($lang, array(
	'EVEAPI_JABBER_EJABBER'					=> 'ejabberd',
	'EVEAPI_JABBER_M'						=> 'Enable Jabber management',
	'EVEAPI_JABBER_M_EXPLAIN'				=> 'Masterswitch to enable Jabber management.',
	'EVEAPI_EJABBER_SWITCH'					=> 'Enable ejabberd management',
	'EVEAPI_EJABBER_SWITCH_EXPLAIN'			=> 'Switch to enable ejabberd external authentication using the forums userbase.',
	'EVEAPI_EJABBER_CODE'					=> 'Authentication code',
	'EVEAPI_EJABBER_CODE_EXPLAIN'			=> 'This code is used to verify incoming requests to avoid spamming and hacking.',
	
	'EVEAPI_JABBER_OPENFIRE'				=> 'OpenFire UserService',
	'EVEAPI_OPENFIRE_SWITCH'				=> 'Enable OpenFire management',
	'EVEAPI_OPENFIRE_SWITCH_EXPLAIN'		=> 'Switch to enable OpenFire management.',
	'EVEAPI_OPENFIRE_HOST'					=> 'Host',
	'EVEAPI_OPENFIRE_HOST_EXPLAIN'			=> 'Host of which the OpenFire server is running on.<br />Example: jabber.example.com',
	'EVEAPI_OPENFIRE_PORT'					=> 'Port',
	'EVEAPI_OPENFIRE_PORT_EXPLAIN'			=> 'Port to connect to the OpenFire admin console.<br />Default: 9090',
	'EVEAPI_OPENFIRE_CODE'					=> 'Shared secret key',
	'EVEAPI_OPENFIRE_CODE_EXPLAIN'			=> 'This secret key is required to allow for external user management.',
));

$lang = array_merge($lang, array(
	'EVEAPI_ACCESSMASK_NOTENOUGH'				=> 'The current accessMask does not have the minimum accessMask required to use this mod. <br />Please be sure to have the following selected: <br /><br />* <b>Public -> CharacterInfo</b> or <b>Private -> CharacterInfo</b><br />* And <b>Public -> FacWarStats</b> if you are using the Faction Warfare integration.',
	
	'EVEAPI_ACCESSMASK_ACCOUNTMARKET'			=> 'Account and Market',
	'EVEAPI_ACCESSMASK_WALLETTRANSACTIONS'		=> 'WalletTransactions',
	'EVEAPI_ACCESSMASK_WALLETJOURNAL'			=> 'WalletJournal',
	'EVEAPI_ACCESSMASK_MARKETORDERS'			=> 'MarketOrders',
	'EVEAPI_ACCESSMASK_ACCOUNTBALANCE'			=> 'AccountBalance',
	
	'EVEAPI_ACCESSMASK_COMMUNICATIONS'			=> 'Communications',
	'EVEAPI_ACCESSMASK_NOTIFICATIONTEXTS'		=> 'NotificationTexts',
	'EVEAPI_ACCESSMASK_NOTIFICATIONS'			=> 'Notifications',
	'EVEAPI_ACCESSMASK_MAILMESSAGES'			=> 'MailMessages',
	'EVEAPI_ACCESSMASK_MAILINGLISTS'			=> 'MailingLists',
	'EVEAPI_ACCESSMASK_MAILBODIES'				=> 'MailBodies',
	'EVEAPI_ACCESSMASK_CONTACTNOTIFICATIONS'	=> 'ContactNotifications',
	'EVEAPI_ACCESSMASK_CONTACTLIST'				=> 'ContactList',
	
	'EVEAPI_ACCESSMASK_PRIVATEINFORMATION'		=> 'Private Information',
	'EVEAPI_ACCESSMASK_LOCATIONS'				=> 'Locations',
	'EVEAPI_ACCESSMASK_CONTRACTS'				=> 'Contracts',
	'EVEAPI_ACCESSMASK_ACCOUNTSTATUS'			=> 'AccountStatus',
	'EVEAPI_ACCESSMASK_CHARACTERINFO'			=> 'CharacterInfo',
	'EVEAPI_ACCESSMASK_UPCOMINGCALENDAREVENTS'	=> 'UpcomingCalendarEvents',
	'EVEAPI_ACCESSMASK_SKILLQUEUE'				=> 'SkillQueue',
	'EVEAPI_ACCESSMASK_SKILLINTRAINING'			=> 'SkillInTraining',
	'EVEAPI_ACCESSMASK_CHARACTERSHEET'			=> 'CharacterSheet',
	'EVEAPI_ACCESSMASK_CALENDAREVENTATTENDEES'	=> 'CalendarEventAttendees',
	'EVEAPI_ACCESSMASK_ASSETLIST'				=> 'AssetList',

	'EVEAPI_ACCESSMASK_PUBLICINFORMATION'		=> 'Public Information',
	'EVEAPI_ACCESSMASK_CHARACTERINFO'			=> 'CharacterInfo',
	'EVEAPI_ACCESSMASK_STANDINGS'				=> 'Standings',
	'EVEAPI_ACCESSMASK_MEDALS'					=> 'Medals',
	'EVEAPI_ACCESSMASK_KILLLOG'					=> 'KillLog',
	'EVEAPI_ACCESSMASK_FACWARSTATS'				=> 'FacWarStats',
	
	'EVEAPI_ACCESSMASK_SCIENCEINDUSTRY'			=> 'Science and Industry',
	'EVEAPI_ACCESSMASK_RESEARCH'				=> 'Research',
	'EVEAPI_ACCESSMASK_INDUSTRYJOBS'			=> 'IndustryJobs',
));

?>