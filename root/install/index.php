<?php
/**
*
* @author Mr Twinkie
* @package eveapi
* @copyright (c) 2015 Mr Twinkie
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* Created originally by cyerus
*/

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
define('IN_INSTALL', true);  // Make it able to load from /root/install/ directory.
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/umil_eveapi');

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

require_once($phpbb_root_path . 'install/function_eve_systems.' . $phpEx);
require_once($phpbb_root_path . 'install/function_eve_ships.' . $phpEx);
require_once($phpbb_root_path . 'install/function_eve_items.' . $phpEx);

// The name of the mod to be displayed during installation.
$mod_name = 'UMIL_EVEAPI';

/*
* The name of the config variable which will hold the currently installed version
* UMIL will handle checking, setting, and updating the version itself.
*/
$version_config_name = 'eveapi_version';

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/
$versions = array(
	'7.0.7'	=> array(
		'custom'	=> 'umil_eveapi_7_0_7',
	),
	
	'7.0.6'	=> array(
		'custom'	=> 'umil_eveapi_7_0_6',
	),
	
	'7.0.5'	=> array(
		'custom'	=> 'umil_eveapi_7_0_5',
	),
	
	'7.0.4'	=> array(
		'custom'	=> 'umil_eveapi_7_0_4',
	),
	
	'7.0.0'	=> array(
		'custom'	=> 'umil_eveapi_7_0_0',
	),

	'6.3.1'	=> array(
		'custom'	=> 'umil_eveapi_6_3_1',
	),
	
	'6.3.0'	=> array(
		'custom'	=> 'umil_eveapi_6_3_0',
	),
	
	'6.2.4'	=> array(
		'custom'	=> 'umil_eveapi_6_2_4',
	),
	
	'6.2.3'	=> array(
		'custom'	=> 'umil_eveapi_6_2_3',
	),
	
	'6.2.2'	=> array(
		'custom'	=> 'umil_eveapi_6_2_2',
	),
	
	'6.2.1'	=> array(
		'custom'	=> 'umil_eveapi_6_2_1',
	),
    
	'6.2.0'	=> array(
		'custom'	=> 'umil_eveapi_6_2_0',
	),

	'6.1.2'	=> array(
		'custom'	=> 'umil_eveapi_6_1_2',
	),

	'6.1.1'	=> array(
		'custom'	=> 'umil_eveapi_6_1_1',
	),
	
	'6.1.0'	=> array(
		'custom'	=> 'umil_eveapi_6_1_0',
	),

	'6.0.3'	=> array(
		'custom'	=> 'umil_eveapi_6_0_3',
	),
	
	'6.0.2'	=> array(
		'custom'	=> 'umil_eveapi_6_0_2',
	),

	'6.0.1'	=> array(
		'custom'	=> 'umil_eveapi_6_0_1',
	),

	'6.0.0'	=> array(
		'custom'	=> 'umil_eveapi_6_0_0',
	),
);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

// <editor-fold defaultstate="collapsed" desc="functions based on version">
/*
 * Functions based on version
 */
 function umil_eveapi_7_0_7($action, $version)
{
    global $db, $table_prefix, $umil;
    
    if($action == 'uninstall')
    {
        // Modifications to the Group-settings
        // ****************************************************************************************************
        // 
        // Backing up Group settings
        if ($umil->table_exists('eveapi_grouptmp'))
        {
            $umil->table_remove('eveapi_grouptmp');
        }

        $umil->table_add('eveapi_grouptmp', array(
            'COLUMNS'        => array(
                'group_id'					=> array('UINT:8', 0),
                'group_eveapi_special'		=> array('TINT:1', 0),
                'group_eveapi_ts3'			=> array('UINT:8', 0),
                'group_eveapi_jabber'		=> array('TINT:1', 0),
				'group_eveapi_openfire'		=> array('VCHAR:20', ''),
            ),
            'PRIMARY_KEY'    => 'group_id',
        ));

        $sql = "INSERT INTO eveapi_grouptmp (group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire) 
                SELECT group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire
                FROM " . GROUPS_TABLE;
        $db->sql_query($sql);
        
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_special');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_ts3');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_jabber');
		$umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_openfire');
        // ****************************************************************************************************
    }
    
    if($action == 'install')
    {
        // Modifications to the Group-settings
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_special'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_special', array('TINT:1', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_ts3'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_ts3', array('UINT:8', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_jabber'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_jabber', array('TINT:1', 0));
        }
		
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_openfire'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_openfire', array('VCHAR:20', ""));
        }

        // Filling API data from 'prepare'-script (if applicable)
        if($umil->table_exists("eveapi_grouptmp"))
        {
            $sql = "SELECT *
                    FROM eveapi_grouptmp
                    ORDER BY group_id";
            $result = $db->sql_query($sql);

            while ($g_row = $db->sql_fetchrow($result))
            {
                    $openfire = $umil->table_column_exists('eveapi_grouptmp', 'group_eveapi_openfire') ? $g_row['group_eveapi_openfire'] : "";
				
					$sql_in = "UPDATE " . GROUPS_TABLE . "
                    SET group_eveapi_special = " . $g_row['group_eveapi_special'] . ", group_eveapi_ts3 = " . $g_row['group_eveapi_ts3'] . ", group_eveapi_jabber = " . $g_row['group_eveapi_jabber'] . ", group_eveapi_openfire = '" . $openfire . "'
                    WHERE group_id = " . $g_row['group_id'];
                    $db->sql_query($sql_in);
            }
            $db->sql_freeresult($result);

            $umil->table_remove('eveapi_grouptmp');
        }
    }
    
    if($action == 'update')
    {
        // Rubicon 1.1 stuff, yay!
        umil_eveapi_update_eve_database();
        
        // ACP Module Managament
        // Check if module category exists before attempting delete
        // NOTICE the Jabber-module missing from the list! (as version < 6.2.0 doesn't have the Jabber module)
        if($umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_remove('acp', 'ACP_CAT_EVEAPI', array(
                'module_basename'   => 'eveapi',
                'modes'             => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
            ));
            
            $umil->module_remove('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI');
        }
        
        // And re-adding, after check if it doesn't exist anymore.
        if(!$umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_add(array(
                // Add a new category named ACP_CAT_EVEAPI to ACP_CAT_GENERAL
                array('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'),

                // Add the settings and features modes from the acp_eveapi module to the ACP_CAT_EVEAPI category using the "automatic" method.
                array('acp', 'ACP_CAT_EVEAPI', array(
                        'module_basename'       => 'eveapi',
                        'modes'                 => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
                    ),
                ),
            ));
        }
        
        // Added database support for Group-based settings
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_special'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_special', array('TINT:1', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_ts3'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_ts3', array('UINT:8', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_jabber'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_jabber', array('TINT:1', 0));
        }
		
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_openfire'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_openfire', array('VCHAR:20', 0));
        }
    }
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_7_0_6';
}
 function umil_eveapi_7_0_6($action, $version)
{
    global $db, $table_prefix, $umil;
    
    if($action == 'uninstall')
    {
        // Modifications to the Group-settings
        // ****************************************************************************************************
        // 
        // Backing up Group settings
        if ($umil->table_exists('eveapi_grouptmp'))
        {
            $umil->table_remove('eveapi_grouptmp');
        }

        $umil->table_add('eveapi_grouptmp', array(
            'COLUMNS'        => array(
                'group_id'					=> array('UINT:8', 0),
                'group_eveapi_special'		=> array('TINT:1', 0),
                'group_eveapi_ts3'			=> array('UINT:8', 0),
                'group_eveapi_jabber'		=> array('TINT:1', 0),
				'group_eveapi_openfire'		=> array('VCHAR:20', ''),
            ),
            'PRIMARY_KEY'    => 'group_id',
        ));

        $sql = "INSERT INTO eveapi_grouptmp (group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire) 
                SELECT group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire
                FROM " . GROUPS_TABLE;
        $db->sql_query($sql);
        
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_special');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_ts3');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_jabber');
		$umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_openfire');
        // ****************************************************************************************************
    }
    
    if($action == 'install')
    {
        // Modifications to the Group-settings
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_special'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_special', array('TINT:1', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_ts3'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_ts3', array('UINT:8', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_jabber'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_jabber', array('TINT:1', 0));
        }
		
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_openfire'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_openfire', array('VCHAR:20', ""));
        }

        // Filling API data from 'prepare'-script (if applicable)
        if($umil->table_exists("eveapi_grouptmp"))
        {
            $sql = "SELECT *
                    FROM eveapi_grouptmp
                    ORDER BY group_id";
            $result = $db->sql_query($sql);

            while ($g_row = $db->sql_fetchrow($result))
            {
                    $openfire = $umil->table_column_exists('eveapi_grouptmp', 'group_eveapi_openfire') ? $g_row['group_eveapi_openfire'] : "";
				
					$sql_in = "UPDATE " . GROUPS_TABLE . "
                    SET group_eveapi_special = " . $g_row['group_eveapi_special'] . ", group_eveapi_ts3 = " . $g_row['group_eveapi_ts3'] . ", group_eveapi_jabber = " . $g_row['group_eveapi_jabber'] . ", group_eveapi_openfire = '" . $openfire . "'
                    WHERE group_id = " . $g_row['group_id'];
                    $db->sql_query($sql_in);
            }
            $db->sql_freeresult($result);

            $umil->table_remove('eveapi_grouptmp');
        }
    }
    
    if($action == 'update')
    {
        // Rubicon 1.1 stuff, yay!
        umil_eveapi_update_eve_database();
        
        // ACP Module Managament
        // Check if module category exists before attempting delete
        // NOTICE the Jabber-module missing from the list! (as version < 6.2.0 doesn't have the Jabber module)
        if($umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_remove('acp', 'ACP_CAT_EVEAPI', array(
                'module_basename'   => 'eveapi',
                'modes'             => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
            ));
            
            $umil->module_remove('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI');
        }
        
        // And re-adding, after check if it doesn't exist anymore.
        if(!$umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_add(array(
                // Add a new category named ACP_CAT_EVEAPI to ACP_CAT_GENERAL
                array('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'),

                // Add the settings and features modes from the acp_eveapi module to the ACP_CAT_EVEAPI category using the "automatic" method.
                array('acp', 'ACP_CAT_EVEAPI', array(
                        'module_basename'       => 'eveapi',
                        'modes'                 => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
                    ),
                ),
            ));
        }
        
        // Added database support for Group-based settings
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_special'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_special', array('TINT:1', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_ts3'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_ts3', array('UINT:8', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_jabber'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_jabber', array('TINT:1', 0));
        }
		
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_openfire'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_openfire', array('VCHAR:20', 0));
        }
    }
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_7_0_6';
}
 function umil_eveapi_7_0_5($action, $version)
{
    global $db, $table_prefix, $umil;
	
	    if($action == 'uninstall')
    {
        // Modifications to the Group-settings
        // ****************************************************************************************************
        // 
        // Backing up Group settings
        if ($umil->table_exists('eveapi_grouptmp'))
        {
            $umil->table_remove('eveapi_grouptmp');
        }

        $umil->table_add('eveapi_grouptmp', array(
            'COLUMNS'        => array(
                'group_id'					=> array('UINT:8', 0),
                'group_eveapi_special'		=> array('TINT:1', 0),
                'group_eveapi_ts3'			=> array('UINT:8', 0),
                'group_eveapi_jabber'		=> array('TINT:1', 0),
				'group_eveapi_openfire'		=> array('VCHAR:20', ''),
            ),
            'PRIMARY_KEY'    => 'group_id',
        ));

        $sql = "INSERT INTO eveapi_grouptmp (group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire) 
                SELECT group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire
                FROM " . GROUPS_TABLE;
        $db->sql_query($sql);
        
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_special');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_ts3');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_jabber');
		$umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_openfire');
        // ****************************************************************************************************
    }
    
	if($action == 'update')
    {
        umil_eveapi_update_eve_database();
    }
	
    $umil->cache_purge();

    return 'UMIL_EVEAPI_7_0_5';
}
 function umil_eveapi_7_0_4($action, $version)
{
    global $db, $table_prefix, $umil;
    
    if($action == 'uninstall')
    {
        // Modifications to the Group-settings
        // ****************************************************************************************************
        // 
        // Backing up Group settings
        if ($umil->table_exists('eveapi_grouptmp'))
        {
            $umil->table_remove('eveapi_grouptmp');
        }

        $umil->table_add('eveapi_grouptmp', array(
            'COLUMNS'        => array(
                'group_id'					=> array('UINT:8', 0),
                'group_eveapi_special'		=> array('TINT:1', 0),
                'group_eveapi_ts3'			=> array('UINT:8', 0),
                'group_eveapi_jabber'		=> array('TINT:1', 0),
				'group_eveapi_openfire'		=> array('VCHAR:20', ''),
            ),
            'PRIMARY_KEY'    => 'group_id',
        ));

        $sql = "INSERT INTO eveapi_grouptmp (group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire) 
                SELECT group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire
                FROM " . GROUPS_TABLE;
        $db->sql_query($sql);
        
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_special');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_ts3');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_jabber');
		$umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_openfire');
        // ****************************************************************************************************
    }
    
    if($action == 'install')
    {
        // Modifications to the Group-settings
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_special'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_special', array('TINT:1', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_ts3'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_ts3', array('UINT:8', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_jabber'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_jabber', array('TINT:1', 0));
        }
		
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_openfire'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_openfire', array('VCHAR:20', ""));
        }

        // Filling API data from 'prepare'-script (if applicable)
        if($umil->table_exists("eveapi_grouptmp"))
        {
            $sql = "SELECT *
                    FROM eveapi_grouptmp
                    ORDER BY group_id";
            $result = $db->sql_query($sql);

            while ($g_row = $db->sql_fetchrow($result))
            {
                    $openfire = $umil->table_column_exists('eveapi_grouptmp', 'group_eveapi_openfire') ? $g_row['group_eveapi_openfire'] : "";
				
					$sql_in = "UPDATE " . GROUPS_TABLE . "
                    SET group_eveapi_special = " . $g_row['group_eveapi_special'] . ", group_eveapi_ts3 = " . $g_row['group_eveapi_ts3'] . ", group_eveapi_jabber = " . $g_row['group_eveapi_jabber'] . ", group_eveapi_openfire = '" . $openfire . "'
                    WHERE group_id = " . $g_row['group_id'];
                    $db->sql_query($sql_in);
            }
            $db->sql_freeresult($result);

            $umil->table_remove('eveapi_grouptmp');
        }
    }
    
    if($action == 'update')
    {
        // Rubicon 1.1 stuff, yay!
        umil_eveapi_update_eve_database();
        
        // ACP Module Managament
        // Check if module category exists before attempting delete
        // NOTICE the Jabber-module missing from the list! (as version < 6.2.0 doesn't have the Jabber module)
        if($umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_remove('acp', 'ACP_CAT_EVEAPI', array(
                'module_basename'   => 'eveapi',
                'modes'             => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
            ));
            
            $umil->module_remove('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI');
        }
        
        // And re-adding, after check if it doesn't exist anymore.
        if(!$umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_add(array(
                // Add a new category named ACP_CAT_EVEAPI to ACP_CAT_GENERAL
                array('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'),

                // Add the settings and features modes from the acp_eveapi module to the ACP_CAT_EVEAPI category using the "automatic" method.
                array('acp', 'ACP_CAT_EVEAPI', array(
                        'module_basename'       => 'eveapi',
                        'modes'                 => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
                    ),
                ),
            ));
        }
        
        // Added database support for Group-based settings
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_special'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_special', array('TINT:1', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_ts3'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_ts3', array('UINT:8', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_jabber'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_jabber', array('TINT:1', 0));
        }
		
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_openfire'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_openfire', array('VCHAR:20', 0));
        }
    }
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_7_0_4';
}
 function umil_eveapi_7_0_0($action, $version)
{
    global $db, $table_prefix, $umil;
    
    if($action == 'uninstall')
    {
        // Modifications to the Group-settings
        // ****************************************************************************************************
        // 
        // Backing up Group settings
        if ($umil->table_exists('eveapi_grouptmp'))
        {
            $umil->table_remove('eveapi_grouptmp');
        }

        $umil->table_add('eveapi_grouptmp', array(
            'COLUMNS'        => array(
                'group_id'					=> array('UINT:8', 0),
                'group_eveapi_special'		=> array('TINT:1', 0),
                'group_eveapi_ts3'			=> array('UINT:8', 0),
                'group_eveapi_jabber'		=> array('TINT:1', 0),
				'group_eveapi_openfire'		=> array('VCHAR:20', ''),
            ),
            'PRIMARY_KEY'    => 'group_id',
        ));

        $sql = "INSERT INTO eveapi_grouptmp (group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire) 
                SELECT group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire
                FROM " . GROUPS_TABLE;
        $db->sql_query($sql);
        
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_special');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_ts3');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_jabber');
		$umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_openfire');
        // ****************************************************************************************************
    }
    
    if($action == 'install')
    {
        // Modifications to the Group-settings
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_special'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_special', array('TINT:1', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_ts3'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_ts3', array('UINT:8', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_jabber'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_jabber', array('TINT:1', 0));
        }
		
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_openfire'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_openfire', array('VCHAR:20', ""));
        }

        // Filling API data from 'prepare'-script (if applicable)
        if($umil->table_exists("eveapi_grouptmp"))
        {
            $sql = "SELECT *
                    FROM eveapi_grouptmp
                    ORDER BY group_id";
            $result = $db->sql_query($sql);

            while ($g_row = $db->sql_fetchrow($result))
            {
                    $openfire = $umil->table_column_exists('eveapi_grouptmp', 'group_eveapi_openfire') ? $g_row['group_eveapi_openfire'] : "";
				
					$sql_in = "UPDATE " . GROUPS_TABLE . "
                    SET group_eveapi_special = " . $g_row['group_eveapi_special'] . ", group_eveapi_ts3 = " . $g_row['group_eveapi_ts3'] . ", group_eveapi_jabber = " . $g_row['group_eveapi_jabber'] . ", group_eveapi_openfire = '" . $openfire . "'
                    WHERE group_id = " . $g_row['group_id'];
                    $db->sql_query($sql_in);
            }
            $db->sql_freeresult($result);

            $umil->table_remove('eveapi_grouptmp');
        }
    }
    
    if($action == 'update')
    {
        // Rubicon 1.1 stuff, yay!
        umil_eveapi_update_eve_database();
        
        // ACP Module Managament
        // Check if module category exists before attempting delete
        // NOTICE the Jabber-module missing from the list! (as version < 6.2.0 doesn't have the Jabber module)
        if($umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_remove('acp', 'ACP_CAT_EVEAPI', array(
                'module_basename'   => 'eveapi',
                'modes'             => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
            ));
            
            $umil->module_remove('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI');
        }
        
        // And re-adding, after check if it doesn't exist anymore.
        if(!$umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_add(array(
                // Add a new category named ACP_CAT_EVEAPI to ACP_CAT_GENERAL
                array('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'),

                // Add the settings and features modes from the acp_eveapi module to the ACP_CAT_EVEAPI category using the "automatic" method.
                array('acp', 'ACP_CAT_EVEAPI', array(
                        'module_basename'       => 'eveapi',
                        'modes'                 => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
                    ),
                ),
            ));
        }
        
        // Added database support for Group-based settings
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_special'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_special', array('TINT:1', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_ts3'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_ts3', array('UINT:8', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_jabber'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_jabber', array('TINT:1', 0));
        }
		
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_openfire'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_openfire', array('VCHAR:20', 0));
        }
    }
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_7_0_0';
}

function umil_eveapi_6_3_1($action, $version)
{
    global $db, $table_prefix, $umil;
    
    if($action == 'uninstall')
    {
        // Modifications to the Group-settings
        // ****************************************************************************************************
        // 
        // Backing up Group settings
        if ($umil->table_exists('eveapi_grouptmp'))
        {
            $umil->table_remove('eveapi_grouptmp');
        }

        $umil->table_add('eveapi_grouptmp', array(
            'COLUMNS'        => array(
                'group_id'					=> array('UINT:8', 0),
                'group_eveapi_special'		=> array('TINT:1', 0),
                'group_eveapi_ts3'			=> array('UINT:8', 0),
                'group_eveapi_jabber'		=> array('TINT:1', 0),
				'group_eveapi_openfire'		=> array('VCHAR:20', ''),
            ),
            'PRIMARY_KEY'    => 'group_id',
        ));

        $sql = "INSERT INTO eveapi_grouptmp (group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire) 
                SELECT group_id, group_eveapi_special, group_eveapi_ts3, group_eveapi_jabber, group_eveapi_openfire
                FROM " . GROUPS_TABLE;
        $db->sql_query($sql);
        
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_special');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_ts3');
        $umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_jabber');
		$umil->table_column_remove(GROUPS_TABLE, 'group_eveapi_openfire');
        // ****************************************************************************************************
    }
    
    if($action == 'install')
    {
        // Modifications to the Group-settings
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_special'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_special', array('TINT:1', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_ts3'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_ts3', array('UINT:8', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_jabber'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_jabber', array('TINT:1', 0));
        }
		
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_openfire'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_openfire', array('VCHAR:20', ""));
        }

        // Filling API data from 'prepare'-script (if applicable)
        if($umil->table_exists("eveapi_grouptmp"))
        {
            $sql = "SELECT *
                    FROM eveapi_grouptmp
                    ORDER BY group_id";
            $result = $db->sql_query($sql);

            while ($g_row = $db->sql_fetchrow($result))
            {
                    $openfire = $umil->table_column_exists('eveapi_grouptmp', 'group_eveapi_openfire') ? $g_row['group_eveapi_openfire'] : "";
				
					$sql_in = "UPDATE " . GROUPS_TABLE . "
                    SET group_eveapi_special = " . $g_row['group_eveapi_special'] . ", group_eveapi_ts3 = " . $g_row['group_eveapi_ts3'] . ", group_eveapi_jabber = " . $g_row['group_eveapi_jabber'] . ", group_eveapi_openfire = '" . $openfire . "'
                    WHERE group_id = " . $g_row['group_id'];
                    $db->sql_query($sql_in);
            }
            $db->sql_freeresult($result);

            $umil->table_remove('eveapi_grouptmp');
        }
    }
    
    if($action == 'update')
    {
        // Rubicon 1.1 stuff, yay!
        umil_eveapi_update_eve_database();
        
        // ACP Module Managament
        // Check if module category exists before attempting delete
        // NOTICE the Jabber-module missing from the list! (as version < 6.2.0 doesn't have the Jabber module)
        if($umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_remove('acp', 'ACP_CAT_EVEAPI', array(
                'module_basename'   => 'eveapi',
                'modes'             => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
            ));
            
            $umil->module_remove('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI');
        }
        
        // And re-adding, after check if it doesn't exist anymore.
        if(!$umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_add(array(
                // Add a new category named ACP_CAT_EVEAPI to ACP_CAT_GENERAL
                array('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'),

                // Add the settings and features modes from the acp_eveapi module to the ACP_CAT_EVEAPI category using the "automatic" method.
                array('acp', 'ACP_CAT_EVEAPI', array(
                        'module_basename'       => 'eveapi',
                        'modes'                 => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
                    ),
                ),
            ));
        }
        
        // Added database support for Group-based settings
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_special'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_special', array('TINT:1', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_ts3'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_ts3', array('UINT:8', 0));
        }

        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_jabber'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_jabber', array('TINT:1', 0));
        }
		
        if (!$umil->table_column_exists(GROUPS_TABLE, 'group_eveapi_openfire'))
        {
            $umil->table_column_add(GROUPS_TABLE, 'group_eveapi_openfire', array('VCHAR:20', 0));
        }
    }
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_3_1';
}

function umil_eveapi_6_3_0($action, $version)
{
    global $db, $table_prefix, $umil;
    
	if($action == 'update')
    {
        // Rubicon 1.0.4 stuff, yay!
        umil_eveapi_update_eve_database();
		
		// Table determining access to Jabber
		if ($umil->table_exists('eveapi_jabber'))
		{
			$umil->table_remove('eveapi_jabber');
		}
		$umil->table_add('eveapi_jabber', array(
			'COLUMNS'        => array(
				'userID'              => array('INT:11', 0),
			),
			'PRIMARY_KEY'    => 'userID',
		));
		
        // ACP Module Managament
        // Check if module category exists before attempting delete
        // NOTICE the AccessMask-module missing from the list! (as version < 6.3.0 doesn't have the Jabber module)
        if($umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_remove('acp', 'ACP_CAT_EVEAPI', array(
                'module_basename'   => 'eveapi',
                'modes'             => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber'),
            ));
            
            $umil->module_remove('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI');
        }
        
        // And re-adding, after check if it doesn't exist anymore.
        if(!$umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_add(array(
                // Add a new category named ACP_CAT_EVEAPI to ACP_CAT_GENERAL
                array('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'),

                // Add the settings and features modes from the acp_eveapi module to the ACP_CAT_EVEAPI category using the "automatic" method.
                array('acp', 'ACP_CAT_EVEAPI', array(
                        'module_basename'       => 'eveapi',
                        'modes'                 => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
                    ),
                ),
            ));
        }
    }
	
	if($action == 'install')
    {
		// Table determining access to Jabber
		if ($umil->table_exists('eveapi_jabber'))
		{
			$umil->table_remove('eveapi_jabber');
		}
		$umil->table_add('eveapi_jabber', array(
			'COLUMNS'        => array(
				'userID'              => array('INT:11', 0),
			),
			'PRIMARY_KEY'    => 'userID',
		));
    }
	
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_3_0';
}

function umil_eveapi_6_2_4($action, $version)
{
    global $db, $table_prefix, $umil;
    
	if($action == 'update')
    {
        // Odyssey 1.0.9 stuff, yay!
        umil_eveapi_update_eve_database();
    }
	
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_2_4';
}

function umil_eveapi_6_2_3($action, $version)
{
    global $db, $table_prefix, $umil;
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_2_3';
}

function umil_eveapi_6_2_2($action, $version)
{
    global $db, $table_prefix, $umil;
    
    if($action == 'update')
    {
        // Retribution 1.1 stuff, yay!
        umil_eveapi_update_eve_database();
    }
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_2_2';
}

function umil_eveapi_6_2_1($action, $version)
{
    global $db, $table_prefix, $umil;
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_2_1';
}

function umil_eveapi_6_2_0($action, $version)
{
    global $db, $table_prefix, $umil;
    
    if($action == 'update')
    {
        // Retribution 1.0.7 stuff, yay!
        umil_eveapi_update_eve_database();
        
        // ACP Module Managament
        // Check if module category exists before attempting delete
        // NOTICE the Jabber-module missing from the list! (as version < 6.2.0 doesn't have the Jabber module)
        if($umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_remove('acp', 'ACP_CAT_EVEAPI', array(
                'module_basename'   => 'eveapi',
                'modes'             => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3'),
            ));
            
            $umil->module_remove('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI');
        }
        
        // And re-adding, after check if it doesn't exist anymore.
        if(!$umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_add(array(
                // Add a new category named ACP_CAT_EVEAPI to ACP_CAT_GENERAL
                array('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'),

                // Add the settings and features modes from the acp_eveapi module to the ACP_CAT_EVEAPI category using the "automatic" method.
                array('acp', 'ACP_CAT_EVEAPI', array(
                        'module_basename'       => 'eveapi',
                        'modes'                 => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber'),
                    ),
                ),
            ));
        }
    }
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_2_0';
}

function umil_eveapi_6_1_2($action, $version)
{
    global $db, $table_prefix, $umil;
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_1_2';
}

function umil_eveapi_6_1_1($action, $version)
{
    global $db, $table_prefix, $umil;
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_1_1';
}

function umil_eveapi_6_1_0($action, $version)
{
    global $db, $table_prefix, $umil;
    
    if($action == 'update')
    {
        // Inferno stuff, yay!
        umil_eveapi_update_eve_database();
    }
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_1_0';
}

function umil_eveapi_6_0_3($action, $version)
{
    global $db, $table_prefix, $umil;
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_0_3';
}

function umil_eveapi_6_0_2($action, $version)
{
    global $db, $table_prefix, $umil;
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_0_2';
}

function umil_eveapi_6_0_1($action, $version)
{
    global $db, $table_prefix, $umil;
    
    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_0_1';
}

function umil_eveapi_6_0_0($action, $version)
{
    global $db, $table_prefix, $umil;

    if ($action == 'uninstall')
    {
        // Disable API validation (as protection)
        if($umil->config_exists('eveapi_validation'))
        {
            $umil->config_update('eveapi_validation', 0);
        }

        // Backing up API data
        if ($umil->table_exists('eveapi_tmp'))
        {
            $umil->table_remove('eveapi_tmp');
        }

        $umil->table_add('eveapi_tmp', array(
            'COLUMNS'        => array(
                'user_id'              	=> array('UINT:10', 0),
                'eveapi_keyid'            	=> array('UINT:15', 0),
                'eveapi_vcode'          	=> array('VCHAR:64', ''),
                'eveapi_ts'            	=> array('VCHAR:28', ''),
            ),
            'PRIMARY_KEY'    => 'user_id',
        ));

        $sql = "INSERT INTO eveapi_tmp (user_id, eveapi_keyid, eveapi_vcode, eveapi_ts) 
        SELECT user_id, eveapi_keyid, eveapi_vcode, eveapi_ts
        FROM " . USERS_TABLE;
        $db->sql_query($sql);

        $umil->table_column_remove(USERS_TABLE, 'eveapi_keyid');
        $umil->table_column_remove(USERS_TABLE, 'eveapi_vcode');
        $umil->table_column_remove(USERS_TABLE, 'eveapi_ts');

        // Removing Standings
        if ($umil->table_exists('eveapi_standings'))
        {
            $umil->table_remove('eveapi_standings');
        }

        // Removing Items
        if ($umil->table_exists('eveapi_items'))
        {
            $umil->table_remove('eveapi_items');
        }

        // Removing Ships
        if ($umil->table_exists('eveapi_ships'))
        {
            $umil->table_remove('eveapi_ships');
        }

        // Removing Systems
        if ($umil->table_exists('eveapi_systems'))
        {
            $umil->table_remove('eveapi_systems');
        }
        
        // ACP Module Managament
        // Check if module category exists before attempting delete
        if($umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_remove('acp', 'ACP_CAT_EVEAPI', array(
                'module_basename'   => 'eveapi',
                'modes'             => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
            ));
            
            $umil->module_remove('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI');
        }
    }

    if ($action == 'install')
    {
        if (!$umil->table_column_exists(USERS_TABLE, 'eveapi_keyid'))
        {
            $umil->table_column_add(USERS_TABLE, 'eveapi_keyid', array('UINT', 0));
        }

        if (!$umil->table_column_exists(USERS_TABLE, 'eveapi_vcode'))
        {
            $umil->table_column_add(USERS_TABLE, 'eveapi_vcode', array('VCHAR:64', ''));
        }

        if (!$umil->table_column_exists(USERS_TABLE, 'eveapi_ts'))
        {
            $umil->table_column_add(USERS_TABLE, 'eveapi_ts', array('VCHAR:64', ''));
        }
		if (!$umil->table_column_exists(USERS_TABLE, 'eveapi_jabberpass'))
        {
            $umil->table_column_add(USERS_TABLE, 'eveapi_jabberpass', array('VCHAR:64', ''));
        }

        // Filling API data from 'prepare'-script (if applicable)
        if($umil->table_exists("eveapi_tmp"))
        {
            $sql = "SELECT *
                    FROM eveapi_tmp
                    ORDER BY user_id";
            $result = $db->sql_query($sql);

            while ($t_row = $db->sql_fetchrow($result))
            {
                    $sql_in = "UPDATE " . USERS_TABLE . "
                    SET eveapi_keyid = " . $t_row['eveapi_keyid'] . ", eveapi_vcode = \"" . $t_row['eveapi_vcode'] . "\", eveapi_ts = \"" . $t_row['eveapi_ts'] . "\"
                    WHERE user_id = " . $t_row['user_id'];
                    $db->sql_query($sql_in);
            }
            $db->sql_freeresult($result);

            $umil->table_remove('eveapi_tmp');
        }

        // Standings
        if ($umil->table_exists('eveapi_standings'))
        {
            $umil->table_remove('eveapi_standings');
        }
        $umil->table_add('eveapi_standings', array(
            'COLUMNS'        => array(
                    'standingsID'              => array('UINT', NULL, 'auto_increment'),
                    'standingsName'            => array('VCHAR:100', ''),
                    'standingsAmount'          => array('TINT:2', 0),
                    'standingsType'            => array('TINT:1', 0),
            ),
            'PRIMARY_KEY'    => 'standingsID',
        ));

        umil_eveapi_update_eve_database();
        
        // ACP Module Management
        if(!$umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'))
        {
            $umil->module_add(array(
                // Add a new category named ACP_CAT_EVEAPI to ACP_CAT_GENERAL
                array('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_EVEAPI'),

                // Add the settings and features modes from the acp_eveapi module to the ACP_CAT_EVEAPI category using the "automatic" method.
                array('acp', 'ACP_CAT_EVEAPI', array(
                        'module_basename'       => 'eveapi',
                        'modes'                 => array('general', 'corporation', 'alliance', 'standings', 'factionwarfare', 'teamspeak3', 'jabber', 'accessmask'),
                    ),
                ),
            ));
        }
        
        // Remove and readd the UCP -> Profile -> Avatar module to fix the bug of the submit-button not showing
        if($umil->module_exists('ucp', 'UCP_PROFILE', 'UCP_PROFILE_AVATAR'))
        {
            $umil->module_remove('ucp', 'UCP_PROFILE', array(
                'module_basename'   => 'profile',
                'modes'             => array('avatar'),
            ));
        }
        
        $umil->module_add('ucp', 'UCP_PROFILE', array(
            'module_basename'   => 'profile',
            'modes'             => array('avatar'),
        ));
    }

    $umil->cache_purge();

    return 'UMIL_EVEAPI_6_0_0';
}
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="functions for automated use">
/*
* Function to update the EVE DB tables, meaning the Items, Ships and Systems from a fresh EVE DB dump.
*/
function umil_eveapi_update_eve_database()
{
    global $db, $table_prefix, $umil;

    // Items
    if ($umil->table_exists('eveapi_items'))
    {
        $umil->table_remove('eveapi_items');
    }
    $umil->table_add('eveapi_items', array(
        'COLUMNS'        => array(
            'itemID'                => array('UINT:6', 0),
            'itemName'              => array('VCHAR:100', ''),
            'categoryID'            => array('UINT:6', 0),
        ),
        'PRIMARY_KEY'    => 'itemID',
    ));

    // Ships
    if ($umil->table_exists('eveapi_ships'))
    {
        $umil->table_remove('eveapi_ships');
    }
    $umil->table_add('eveapi_ships', array(
        'COLUMNS'        => array(
            'typeID'                => array('INT:10', 0),
            'typeName'              => array('VCHAR:100', ''),
            'Low'            		=> array('INT:10', 0),
            'Medium'            	=> array('INT:10', 0),
            'High'            		=> array('INT:10', 0),
            'Drone'            		=> array('INT:10', 0),
            'Rig'            		=> array('INT:10', 0),
            'Subsystem'            	=> array('INT:10', 0),
            'raceID'            	=> array('INT:10', 0),
            'raceName'              => array('VCHAR:20', ''),
            'Tech'             		=> array('VCHAR:10', ''),
            'groupName'             => array('VCHAR:50', ''),
            'marketGroupName'       => array('VCHAR:50', ''),
            'Icon'       			=> array('VCHAR:30', ''),
        ),
        'PRIMARY_KEY'    => 'typeID',
    ));

    // System
    if ($umil->table_exists('eveapi_systems'))
    {
        $umil->table_remove('eveapi_systems');
    }
    $umil->table_add('eveapi_systems', array(
        'COLUMNS'        => array(
            'systemID'              => array('INT:11', 0),
            'systemName'            => array('VCHAR:100', ''),
        ),
        'PRIMARY_KEY'    => 'systemID',
    ));

    // Fill Items table with data.
    umil_eveapi_insert_items();

    // Fill Ships table with data.
    umil_eveapi_insert_ships();

    // Fill Systems table with data.
    umil_eveapi_insert_systems();
}
// </editor-fold>

?>
