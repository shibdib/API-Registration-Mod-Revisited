<?php
/**
*
* @package acp
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* @todo add cron intervals to server settings? (database_gc, queue_interval, session_gc, search_gc, cache_gc, warnings_gc)
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_eveapi
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		global $cache;
		
		$action	= request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;

		$form_key = 'acp_eveapi';
		add_form_key($form_key);

		/**
		*	Validation types are:
		*		string, int, bool,
		*		script_path (absolute path in url - beginning with / and no trailing slash),
		*		rpath (relative), rwpath (realtive, writable), path (relative path, but able to escape the root), wpath (writable)
		*/
		switch ($mode)
		{
			case 'general':
                            
                            $vars = array(
                                'legend0'			=> 'GENERAL_SETTINGS',
                                'eveapi_validation'		=> array('lang' => 'EVEAPI_VALIDATION',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
                                'eveapi_nonmember'		=> array('lang' => 'EVEAPI_NONMEMBER',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),

                                'legend1'			=> 'EVEAPI_PORTRAIT_SETTINGS',
                                'avatar_min_width'		=> array('lang' => 'MIN_AVATAR_SIZE',           'validate' => 'int:0',  'type' => false, 'method' => false, 'explain' => false,),
                                'avatar_min_height'		=> array('lang' => 'MIN_AVATAR_SIZE',           'validate' => 'int:0',  'type' => false, 'method' => false, 'explain' => false,),
                                'avatar_max_width'		=> array('lang' => 'MAX_AVATAR_SIZE',           'validate' => 'int:0',  'type' => false, 'method' => false, 'explain' => false,),
                                'avatar_max_height'		=> array('lang' => 'MAX_AVATAR_SIZE',           'validate' => 'int:0',  'type' => false, 'method' => false, 'explain' => false,),

                                'allow_avatar'			=> array('lang' => 'ALLOW_AVATARS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
                                'eveapi_portrait'		=> array('lang' => 'EVEAPI_PORTRAIT',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
                                'eveapi_portrait_size'          => array('lang' => 'EVEAPI_PORTRAIT_SIZE',	'validate' => 'int',	'type' => 'custom', 'method' => 'select_eveapi_portrait_size', 'explain' => true),
                                'avatar_min'			=> array('lang' => 'MIN_AVATAR_SIZE',		'validate' => 'int:0',	'type' => 'dimension:3:4', 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),
                                'avatar_max'			=> array('lang' => 'MAX_AVATAR_SIZE',		'validate' => 'int:0',	'type' => 'dimension:3:4', 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),
                                'eveapi_portrait_resize'	=> array('lang' => 'EVEAPI_PORTRAIT_RESIZE',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),	
								
								'legend2'                  => 'ACP_SUBMIT_CHANGES',
                            );
                            
                            $display_vars = array(
                                'title'	=> 'GENERAL_SETTINGS',
                                'vars'	=> $vars
                            );
                            
                            $vars = array();
			break;

			case 'corporation':
                            $vars = array(
                                'legend0'                   => 'GENERAL_SETTINGS',
                                'eveapi_c_masterswitch'     => array('lang' => 'EVEAPI_CORPORATION_M',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
                                'eveapi_special_corporations'   => array('lang' => 'EVEAPI_SPECIAL_C',	'validate' => 'int:10',	'type' => 'text:4:5', 'explain' => true),
                            );

                            for($i = 1; $i <=  $config['eveapi_special_corporations']; $i++)
                            {
                                $vars = array_merge($vars, array(
                                    'legend'.$i			=> 'EVEAPI_CORPORATION',
                                    'eveapi_c_'.$i.'_switch'	=> array('lang' => 'EVEAPI_ENABLE_C',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
                                    'eveapi_c_'.$i.'_name'	=> array('lang' => 'EVEAPI_NAME_C',				'validate' => 'string',	'type' => 'text:25:50', 'explain' => true),
                                    'eveapi_c_'.$i.'_group'	=> array('lang' => 'EVEAPI_ASSIGN_GROUP_C',		'validate' => 'int',	'type' => 'select', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', array(1, 2, 3, 4, 5, 6, 7), false), 'explain' => true),
                                    'eveapi_c_'.$i.'_default'	=> array('lang' => 'EVEAPI_DEFAULT',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
                                    'eveapi_c_'.$i.'_tsgroup'   => array('lang' => 'EVEAPI_TSGROUP_C',		'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
                                    'eveapi_c_'.$i.'_jabber'	=> array('lang' => 'EVEAPI_JABBER_C',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
									'eveapi_c_'.$i.'_openfire'	=> array('lang' => 'EVEAPI_OPENFIRE_C',		'validate' => 'string',	'type' => 'text:30:35', 'explain' => true),
                                ));
                            }
                            
                            $i =  $config['eveapi_special_corporations'] + 1;
                            $vars = array_merge($vars, array(
                                'legend'.$i => 'ACP_SUBMIT_CHANGES',
                            ));
                            
                            $display_vars = array(
                                'title'	=> 'EVEAPI_CORPORATIONS',
                                'vars'	=> $vars
                            );
                            
                            $vars = array();
			break;

			case 'alliance':
                            $vars = array(
                                'legend0'			=> 'GENERAL_SETTINGS',
                                'eveapi_a_masterswitch'     	=> array('lang' => 'EVEAPI_ALLIANCE_M',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
                                'eveapi_special_alliances'      => array('lang' => 'EVEAPI_SPECIAL_A',	'validate' => 'int:10',	'type' => 'text:4:5', 'explain' => true),
                            );

                            for($i = 1; $i <=  $config['eveapi_special_alliances']; $i++)
                            {
                                $vars = array_merge($vars, array(
                                    'legend'.$i                 => 'EVEAPI_ALLIANCE',
                                    'eveapi_a_'.$i.'_switch'    => array('lang' => 'EVEAPI_ENABLE_A',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
                                    'eveapi_a_'.$i.'_name'      => array('lang' => 'EVEAPI_NAME_A',			'validate' => 'string',	'type' => 'text:25:50', 'explain' => true),
                                    'eveapi_a_'.$i.'_group'     => array('lang' => 'EVEAPI_ASSIGN_GROUP_A',	'validate' => 'int',	'type' => 'select', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', array(1, 2, 3, 4, 5, 6, 7), false), 'explain' => true),
                                    'eveapi_a_'.$i.'_default'   => array('lang' => 'EVEAPI_DEFAULT',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),        
                                    'eveapi_a_'.$i.'_tsgroup'   => array('lang' => 'EVEAPI_TSGROUP_A',		'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
                                    'eveapi_a_'.$i.'_jabber'	=> array('lang' => 'EVEAPI_JABBER_A',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
									'eveapi_a_'.$i.'_openfire'   => array('lang' => 'EVEAPI_OPENFIRE_A',	'validate' => 'string',	'type' => 'text:30:35', 'explain' => true),
                                ));
                            }
                            
                            $i =  $config['eveapi_special_alliances'] + 1;
                            $vars = array_merge($vars, array(
                                'legend'.$i                  => 'ACP_SUBMIT_CHANGES',
                            ));
                            
                            $display_vars = array(
                                'title'	=> 'EVEAPI_ALLIANCES',
                                'vars'	=> $vars
                            );
                            
                            $vars = array();
			break;
			
			case 'standings':
				$display_vars = array(
					'title'	=> 'EVEAPI_STANDINGS',
					'vars'	=> array(
						'legend0'			=> 'GENERAL_SETTINGS',
						'eveapi_s_masterswitch'		=> array('lang' => 'EVEAPI_STANDINGS_M',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_character'		=> array('lang' => 'EVEAPI_S_CHARACTER',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_corporation'		=> array('lang' => 'EVEAPI_S_CORPORATION',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_alliance'			=> array('lang' => 'EVEAPI_S_ALLIANCE',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_charactername'	=> array('lang' => 'EVEAPI_S_CHARACTERNAME',		'validate' => 'string',	'type' => 'text:20:50', 'explain' => true),
						'eveapi_s_corp_keyid'		=> array('lang' => 'EVEAPI_S_CORP_KEYID',			'validate' => 'int:0',	'type' => 'text:15:15', 'explain' => true),
						'eveapi_s_corp_vcode'		=> array('lang' => 'EVEAPI_S_CORP_VCODE',			'validate' => 'string',	'type' => 'text:40:64', 'explain' => true),
						
						'legend1'			=> 'EVEAPI_STANDINGS_RED',
						'eveapi_s_red_switch'		=> array('lang' => 'EVEAPI_S_RED_ENABLE',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_red_group'		=> array('lang' => 'EVEAPI_S_ASSIGN_GROUP',			'validate' => 'int',	'type' => 'select', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', array(1, 2, 3, 4, 5, 6, 7), false), 'explain' => true),
 						'eveapi_s_red_default'		=> array('lang' => 'EVEAPI_DEFAULT',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),        
						'eveapi_s_red_tsgroup'		=> array('lang' => 'EVEAPI_S_TSGROUP',				'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
						'eveapi_s_red_jabber'		=> array('lang' => 'EVEAPI_S_JABBER',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_red_openfire'		=> array('lang' => 'EVEAPI_S_OPENFIRE',				'validate' => 'string',	'type' => 'text:30:35', 'explain' => true),
                                            
						'legend2'			=> 'EVEAPI_STANDINGS_ORANGE',
						'eveapi_s_orange_switch'	=> array('lang' => 'EVEAPI_S_ORANGE_ENABLE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_orange_group'		=> array('lang' => 'EVEAPI_S_ASSIGN_GROUP',			'validate' => 'int',	'type' => 'select', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', array(1, 2, 3, 4, 5, 6, 7), false), 'explain' => true),
						'eveapi_s_orange_default'	=> array('lang' => 'EVEAPI_DEFAULT',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),        
						'eveapi_s_orange_tsgroup'	=> array('lang' => 'EVEAPI_S_TSGROUP',				'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
						'eveapi_s_orange_jabber'	=> array('lang' => 'EVEAPI_S_JABBER',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_orange_openfire'	=> array('lang' => 'EVEAPI_S_OPENFIRE',				'validate' => 'string',	'type' => 'text:30:35', 'explain' => true),

						'legend3'			=> 'EVEAPI_STANDINGS_NEUTRAL',
						'eveapi_s_neutral_switch'	=> array('lang' => 'EVEAPI_S_NEUTRAL_ENABLE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_neutral_group'	=> array('lang' => 'EVEAPI_S_ASSIGN_GROUP',			'validate' => 'int',	'type' => 'select', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', array(1, 2, 3, 4, 5, 6, 7), false), 'explain' => true),
						'eveapi_s_neutral_default'	=> array('lang' => 'EVEAPI_DEFAULT',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),        
						'eveapi_s_neutral_tsgroup'	=> array('lang' => 'EVEAPI_S_TSGROUP',				'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
						'eveapi_s_neutral_jabber'	=> array('lang' => 'EVEAPI_S_JABBER',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_neutral_openfire'	=> array('lang' => 'EVEAPI_S_OPENFIRE',				'validate' => 'string',	'type' => 'text:30:35', 'explain' => true),

						'legend4'			=> 'EVEAPI_STANDINGS_LIGHTBLUE',
						'eveapi_s_lightblue_switch'	=> array('lang' => 'EVEAPI_S_LIGHTBLUE_ENABLE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_lightblue_group'	=> array('lang' => 'EVEAPI_S_ASSIGN_GROUP',			'validate' => 'int',	'type' => 'select', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', array(1, 2, 3, 4, 5, 6, 7), false), 'explain' => true),
						'eveapi_s_lightblue_default'	=> array('lang' => 'EVEAPI_DEFAULT',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),        
						'eveapi_s_lightblue_tsgroup'	=> array('lang' => 'EVEAPI_S_TSGROUP',			'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
						'eveapi_s_lightblue_jabber'		=> array('lang' => 'EVEAPI_S_JABBER',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_lightblue_openfire'	=> array('lang' => 'EVEAPI_S_OPENFIRE',			'validate' => 'string',	'type' => 'text:30:35', 'explain' => true),

						'legend5'			=> 'EVEAPI_STANDINGS_BLUE',
						'eveapi_s_blue_switch'		=> array('lang' => 'EVEAPI_S_BLUE_ENABLE',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_blue_group'		=> array('lang' => 'EVEAPI_S_ASSIGN_GROUP',			'validate' => 'int',	'type' => 'select', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', array(1, 2, 3, 4, 5, 6, 7), false), 'explain' => true),
						'eveapi_s_blue_default'		=> array('lang' => 'EVEAPI_DEFAULT',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),        
						'eveapi_s_blue_tsgroup'		=> array('lang' => 'EVEAPI_S_TSGROUP',				'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
						'eveapi_s_blue_jabber'		=> array('lang' => 'EVEAPI_S_JABBER',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_s_blue_openfire'	=> array('lang' => 'EVEAPI_S_OPENFIRE',				'validate' => 'string',	'type' => 'text:30:35', 'explain' => true),
						
						'legend6'			=> 'ACP_SUBMIT_CHANGES',
					)
				);
			break;
			
			case 'factionwarfare':
				$display_vars = array(
					'title'	=> 'EVEAPI_FW',
					'vars'	=> array(
						'legend0'			=> 'GENERAL_SETTINGS',
						'eveapi_fw_masterswitch'	=> array('lang' => 'EVEAPI_FW_M',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						
						'legend1'			=> 'EVEAPI_FW_AMARR',
						'eveapi_fw_a_switch'		=> array('lang' => 'EVEAPI_FW_ENABLE_A',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_fw_a_group'			=> array('lang' => 'EVEAPI_FW_GROUP_A',		'validate' => 'int',	'type' => 'select', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', array(1, 2, 3, 4, 5, 6, 7), false), 'explain' => true),
						'eveapi_fw_a_default'		=> array('lang' => 'EVEAPI_DEFAULT',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_fw_a_tsgroup'		=> array('lang' => 'EVEAPI_FW_TSGROUP_A',	'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
						'eveapi_fw_a_jabber'		=> array('lang' => 'EVEAPI_FW_JABBER_A',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),    
						'eveapi_fw_a_openfire'		=> array('lang' => 'EVEAPI_FW_OPENFIRE_A',	'validate' => 'string',	'type' => 'text:30:35', 'explain' => true),

						'legend2'			=> 'EVEAPI_FW_CALDARI',
						'eveapi_fw_c_switch'		=> array('lang' => 'EVEAPI_FW_ENABLE_C',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_fw_c_group'			=> array('lang' => 'EVEAPI_FW_GROUP_C',		'validate' => 'int',	'type' => 'select', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', array(1, 2, 3, 4, 5, 6, 7), false), 'explain' => true),
						'eveapi_fw_c_default'		=> array('lang' => 'EVEAPI_DEFAULT',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_fw_c_tsgroup'		=> array('lang' => 'EVEAPI_FW_TSGROUP_C',	'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
						'eveapi_fw_c_jabber'		=> array('lang' => 'EVEAPI_FW_JABBER_C',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),    
						'eveapi_fw_c_openfire'		=> array('lang' => 'EVEAPI_FW_OPENFIRE_C',	'validate' => 'string',	'type' => 'text:30:35', 'explain' => true),

						'legend3'			=> 'EVEAPI_FW_GALLENTE',
						'eveapi_fw_g_switch'		=> array('lang' => 'EVEAPI_FW_ENABLE_G',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_fw_g_group'			=> array('lang' => 'EVEAPI_FW_GROUP_G',		'validate' => 'int',	'type' => 'select', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', array(1, 2, 3, 4, 5, 6, 7), false), 'explain' => true),
 						'eveapi_fw_g_default'		=> array('lang' => 'EVEAPI_DEFAULT',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_fw_g_tsgroup'		=> array('lang' => 'EVEAPI_FW_TSGROUP_G',	'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
						'eveapi_fw_g_jabber'		=> array('lang' => 'EVEAPI_FW_JABBER_G',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_fw_g_openfire'		=> array('lang' => 'EVEAPI_FW_OPENFIRE_G',	'validate' => 'string',	'type' => 'text:30:35', 'explain' => true),

						'legend4'			=> 'EVEAPI_FW_MINMATAR',
						'eveapi_fw_m_switch'		=> array('lang' => 'EVEAPI_FW_ENABLE_M',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_fw_m_group'			=> array('lang' => 'EVEAPI_FW_GROUP_M',		'validate' => 'int',	'type' => 'select', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', array(1, 2, 3, 4, 5, 6, 7), false), 'explain' => true),
						'eveapi_fw_m_default'		=> array('lang' => 'EVEAPI_DEFAULT',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_fw_m_tsgroup'		=> array('lang' => 'EVEAPI_FW_TSGROUP_M',	'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
						'eveapi_fw_m_jabber'		=> array('lang' => 'EVEAPI_FW_JABBER_M',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_fw_m_openfire'		=> array('lang' => 'EVEAPI_FW_OPENFIRE_M',	'validate' => 'string',	'type' => 'text:30:35', 'explain' => true),
						
						'legend5'			=> 'ACP_SUBMIT_CHANGES',
					)
				);
			break;
			
			case 'teamspeak3':
				$vars = array(
					'legend0'				=> 'GENERAL_SETTINGS',
					'eveapi_ts_masterswitch'		=> array('lang' => 'EVEAPI_TS_M',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
					'eveapi_ts_ip'					=> array('lang' => 'EVEAPI_TS_IP',			'validate' => 'string',	'type' => 'text:25:30', 'explain' => true),
					'eveapi_ts_username'			=> array('lang' => 'EVEAPI_TS_USERNAME',	'validate' => 'string',	'type' => 'text:15:20', 'explain' => true),
					'eveapi_ts_password'			=> array('lang' => 'EVEAPI_TS_PASSWORD',	'validate' => 'string',	'type' => 'password:15:20', 'explain' => true),
					'eveapi_ts_port_server'			=> array('lang' => 'EVEAPI_TS_PORT_SERVER',	'validate' => 'int:0',	'type' => 'text:4:5', 'explain' => true),
					'eveapi_ts_port_query'			=> array('lang' => 'EVEAPI_TS_PORT_QUERY',	'validate' => 'int:0',	'type' => 'text:4:5', 'explain' => true),
					'eveapi_ts_nickname'			=> array('lang' => 'EVEAPI_TS_NICKNAME',	'validate' => 'string',	'type' => 'text:15:20', 'explain' => true),

					'legend1'				=> 'EVEAPI_TS_SPECIAL_GROUPS',
					'eveapi_special_ts'				=> array('lang' => 'EVEAPI_SPECIAL_TS',			'validate' => 'int:5',	'type' => 'text:4:5', 'explain' => true),
					'eveapi_ts_admin_switch'		=> array('lang' => 'EVEAPI_TS_ADMIN',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
					'eveapi_ts_admin_tsgroup'		=> array('lang' => 'EVEAPI_TS_ADMIN_TSGROUP',	'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
				);

				for($i = 1; $i <= $config['eveapi_special_ts']; $i++)
				{
					$vars = array_merge($vars, array(
						'eveapi_ts_special_'.$i.'_switch'	=> array('lang' => 'EVEAPI_TS_SPECIAL',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'eveapi_ts_special_'.$i.'_tsgroup'	=> array('lang' => 'EVEAPI_TS_SPECIAL_TSGROUP',	'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
					));
				}

				$vars = array_merge($vars, array(
					'legend2'				=> 'ACP_SUBMIT_CHANGES',
				));

				$display_vars = array(
					'title'	=> 'EVEAPI_TS',
					'vars'	=> $vars
				);

				$vars = array();
			break;
                        
			case 'jabber':
				$vars = array(
					'legend0'				=> 'GENERAL_SETTINGS',
					'eveapi_jabber_masterswitch'	=> array('lang' => 'EVEAPI_JABBER_M',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),

					'legend1'				=> 'EVEAPI_JABBER_EJABBER',
					'eveapi_ejabber_switch'			=> array('lang' => 'EVEAPI_EJABBER_SWITCH',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
					'eveapi_ejabber_code'			=> array('lang' => 'EVEAPI_EJABBER_CODE',		'validate' => 'string',	'type' => 'text:13:15', 'explain' => true),

					'legend2'				=> 'EVEAPI_JABBER_OPENFIRE',
					'eveapi_openfire_switch'		=> array('lang' => 'EVEAPI_OPENFIRE_SWITCH',	'validate' => 'bool',		'type' => 'radio:yes_no', 'explain' => true),
					'eveapi_openfire_host'			=> array('lang' => 'EVEAPI_OPENFIRE_HOST',		'validate' => 'string',		'type' => 'text:30:35', 'explain' => true),
					'eveapi_openfire_port'			=> array('lang' => 'EVEAPI_OPENFIRE_PORT',		'validate' => 'int:0',		'type' => 'text:10:10', 'explain' => true),
					'eveapi_openfire_code'			=> array('lang' => 'EVEAPI_OPENFIRE_CODE',		'validate' => 'string',		'type' => 'text:30:35', 'explain' => true),
					
					'legend3'				=> 'ACP_SUBMIT_CHANGES',
				);

				$display_vars = array(
					'title'	=> 'EVEAPI_JABBER',
					'vars'	=> $vars
				);

				$vars = array();
			break;
		
			case 'accessmask':
				$vars = array(
					'legend0'						=> 'EVEAPI_ACCESSMASK_ACCOUNTMARKET',
					'eveapi_accessmask_4194304'		=> array('lang' => 'EVEAPI_ACCESSMASK_WALLETTRANSACTIONS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_2097152'		=> array('lang' => 'EVEAPI_ACCESSMASK_WALLETJOURNAL',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_4096'		=> array('lang' => 'EVEAPI_ACCESSMASK_MARKETORDERS',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_1'			=> array('lang' => 'EVEAPI_ACCESSMASK_ACCOUNTBALANCE',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),

					'legend1'						=> 'EVEAPI_ACCESSMASK_COMMUNICATIONS',
					'eveapi_accessmask_32768'		=> array('lang' => 'EVEAPI_ACCESSMASK_NOTIFICATIONTEXTS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_16384'		=> array('lang' => 'EVEAPI_ACCESSMASK_NOTIFICATIONS',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_2048'		=> array('lang' => 'EVEAPI_ACCESSMASK_MAILMESSAGES',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_1024'		=> array('lang' => 'EVEAPI_ACCESSMASK_MAILINGLISTS',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_512'			=> array('lang' => 'EVEAPI_ACCESSMASK_MAILBODIES',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_32'			=> array('lang' => 'EVEAPI_ACCESSMASK_CONTACTNOTIFICATIONS',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_16'			=> array('lang' => 'EVEAPI_ACCESSMASK_CONTACTLIST',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),

					'legend2'						=> 'EVEAPI_ACCESSMASK_PRIVATEINFORMATION',
					'eveapi_accessmask_134217728'	=> array('lang' => 'EVEAPI_ACCESSMASK_LOCATIONS',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_67108864'	=> array('lang' => 'EVEAPI_ACCESSMASK_CONTRACTS',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_33554432'	=> array('lang' => 'EVEAPI_ACCESSMASK_ACCOUNTSTATUS',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_16777216'	=> array('lang' => 'EVEAPI_ACCESSMASK_CHARACTERINFO',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_1048576'		=> array('lang' => 'EVEAPI_ACCESSMASK_UPCOMINGCALENDAREVENTS',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_262144'		=> array('lang' => 'EVEAPI_ACCESSMASK_SKILLQUEUE',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_131072'		=> array('lang' => 'EVEAPI_ACCESSMASK_SKILLINTRAINING',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_8'			=> array('lang' => 'EVEAPI_ACCESSMASK_CHARACTERSHEET',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_4'			=> array('lang' => 'EVEAPI_ACCESSMASK_CALENDAREVENTATTENDEES',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_2'			=> array('lang' => 'EVEAPI_ACCESSMASK_ASSETLIST',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),

					'legend3'						=> 'EVEAPI_ACCESSMASK_PUBLICINFORMATION',
					'eveapi_accessmask_8388608'		=> array('lang' => 'EVEAPI_ACCESSMASK_CHARACTERINFO',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_524288'		=> array('lang' => 'EVEAPI_ACCESSMASK_STANDINGS',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_8192'		=> array('lang' => 'EVEAPI_ACCESSMASK_MEDALS',					'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_256'			=> array('lang' => 'EVEAPI_ACCESSMASK_KILLLOG',					'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_64'			=> array('lang' => 'EVEAPI_ACCESSMASK_FACWARSTATS',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),

					'legend4'						=> 'EVEAPI_ACCESSMASK_SCIENCEINDUSTRY',
					'eveapi_accessmask_65536'		=> array('lang' => 'EVEAPI_ACCESSMASK_RESEARCH',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					'eveapi_accessmask_128'			=> array('lang' => 'EVEAPI_ACCESSMASK_INDUSTRYJOBS',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
					
					'legend5'                       => 'ACP_SUBMIT_CHANGES',
				);

				$display_vars = array(
					'title'	=> 'EVEAPI_ACCESSMASK',
					'vars'	=> $vars
				);

				$vars = array();
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}

		if (isset($display_vars['lang']))
		{
			$user->add_lang($display_vars['lang']);
		}

		$this->new_config = $config;
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		$error = array();

		// We validate the complete config if whished
		validate_config_vars($display_vars['vars'], $cfg_array, $error);

		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}
		
		if($submit && $mode == 'accessmask')
		{
			// Check if accessMask options have the bare minimum selected.
			$accessMask = 0;
			for($i = 1; $i <= 134217728; $i *= 2)
			{
				if(!isset($cfg_array['eveapi_accessmask_' . $i]))
				{
					continue;
				}

				if($cfg_array['eveapi_accessmask_' . $i])
				{
					$accessMask += $i;
				}
			}

			// Show error on ACP to inform admin of insufficient accessMask
			if(!eveapi_validateAccessmask($accessMask, false))
			{
				$error[] = $user->lang['EVEAPI_ACCESSMASK_NOTENOUGH'];
			}
		}
		
		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $null)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

			if ($submit)
			{
				set_config($config_name, $config_value);
			}
		}

		if ($submit)
		{
			add_log('admin', 'LOG_CONFIG_' . strtoupper($mode));

			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->tpl_name = 'acp_board';
		$this->page_title = $display_vars['title'];

		$template->assign_vars(array(
			'L_TITLE'			=> "EVE API Settings",
			'L_TITLE_EXPLAIN'	=> "Settings for enabling EVE API into phpBB",

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);

		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);

			if (empty($content))
			{
				continue;
			}

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> $content,
				)
			);

			unset($display_vars['vars'][$config_key]);
		}
	}
	
	function select_eveapi_portrait_size($value, $key = '')
	{
		global $user, $config;
		
		$radio_ary = array(64 => 'EVEAPI_PORTRAIT_SMALL', 128 => 'EVEAPI_PORTRAIT_MEDIUM', 256 => 'EVEAPI_PORTRAIT_LARGE');
		
		return h_radio('config[eveapi_portrait_size]', $radio_ary, $value, $key);
	}
}

?>