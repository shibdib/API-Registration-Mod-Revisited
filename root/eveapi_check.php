<?php

// Prepare the script to work within phpBB's environment.
define('IN_PHPBB', true);
define('IN_INSTALL', true);  // Make it able to load from /root/install/ directory.
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . '/includes/functions_user.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if($user->data['is_registered'] && $user->data['session_admin'])
{
    $phpversion = (version_compare(phpversion(), '5.2.0', '>=')) ? true : false;
    $phpversion_color = ($phpversion) ? "green" : "red";
    $phpversion_text = ($phpversion) ? "Yes" : "No";

    $extension_curl = function_exists('curl_init');
    $extension_curl_color = ($extension_curl) ? "green" : "red";
    $extension_curl_text = ($extension_curl) ? "Yes" : "No";

    $extension_openssl = function_exists('openssl_open');
    $extension_openssl_color = ($extension_openssl) ? "green" : "red";
    $extension_openssl_text = ($extension_openssl) ? "Yes" : "No";
            
    $extension_simplexml = function_exists('simplexml_load_string');
    $extension_simplexml_color = ($extension_simplexml) ? "green" : "red";
    $extension_simplexml_text = ($extension_simplexml) ? "Yes" : "No";

    $mod_automod = (version_compare($config['automod_version'], '1.0.2', '>=')) ? true : false;
    $mod_automod_color = ($mod_automod) ? "green" : "red";
    $mod_automod_text = ($mod_automod) ? "Yes" : "No";
	
	
	// TeamSpeak stuff
    try
    {
        $nickname = "TeamSpeakDebugGuy";
		$testMessage = "Awesome, TeamSpeak integration is working!";
		$errorCode = 0;
		$errorMsg = "No errors found!";
		
		$teamspeak_UID_color = (!empty($user->data['eveapi_ts'])) ? "green" : "red";
		$teamspeak_UID_text = (!empty($user->data['eveapi_ts'])) ? $user->data['eveapi_ts'] : "Unknown";

        $ts3_VirtualServer = TeamSpeak3::factory("serverquery://" . $config["eveapi_ts_username"] . ":" . $config["eveapi_ts_password"] . "@" . $config["eveapi_ts_ip"] . ":" . $config["eveapi_ts_port_query"] . "/?server_port=" . $config["eveapi_ts_port_server"] . "&nickname=" . $nickname);
        $ts3_VirtualServer->clientGetByUid($user->data['eveapi_ts'])->poke($testMessage);
    }
    catch(Exception $e)
    {
        $errorCode = $e->getCode();
		$errorMsg = $e->getMessage();
    }
	
	$error_color = ($errorCode == 0) ? "green" : "red";
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en-gb" lang="en-gb">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Content-Language" content="en-gb">
    
    <title>Requirements</title>

    <style type="text/css">
    <!--
* {
	/* Reset browsers default margin, padding and font sizes */
	margin: 0;
	padding: 0;
	font-size: 100%;
}

body, div, p, th, td, li, dd {
	font-size: x-small;
	voice-family: inherit;
	font-size: small
}

html>body, html>div, html>p, html>th, html>td, html>li, html>dd {
	font-size: small
}

html {
	color: #536482;
	background: #DBD7D1;
	/* Always show a scrollbar for short pages - stops the jump when the scrollbar appears. non-ie browsers */
	height: 100%;
	margin-bottom: 1px;
}

body {
	/* Text-Sizing with ems: http://www.clagnut.com/blog/348/ */
	font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif;
	color: #536482;
	background: #DBD7D1;
	font-size: 62.5%;	/* This sets the default font size to be equivalent to 10px */
	margin: 10px 15px;
}

h1 {
	font-family: "Trebuchet MS", Helvetica, sans-serif;
	font-size: 1.70em;
	font-weight: normal;
	color: #333333;
}

p {
	margin-bottom: 0.7em;
	line-height: 1.40em;
	font-size: 0.90em;
}

/* General links  */
a:link, a:visited {
	color: #105289;
	text-decoration: none;
}

a:hover {
	color: #BC2A4D;
	text-decoration: underline;
}

a:active {
	color: #368AD2;
	text-decoration: none;
}

.install-body p a {
	font-weight: bold;
}

#wrap {
	padding: 0 0 15px 0;
	min-width: 615px;
}

#page-body {
	clear: both;
	min-width: 700px;
}

#page-footer {
	clear: both;
	font-size: 0.75em;
	text-align: center;
}

#content {
	padding: 30px 10px 10px;
	position: relative;
}

#content h1 {
	color: #115098;
	line-height: 1.2em;
	margin-bottom: 0;
}

#main {
	float: left;
	width: 94%;
	margin: 0 0 0 3%;
	min-height: 350px;
}

.rtl #main {
	float: right;
	margin: 0 3% 0 0;
}

* html #main {
	height: 350px;
}

#acp {
	margin: 4px 0;
	padding: 3px 1px;
	min-width: 550px;
	background-color: #FFFFFF;
	border: 1px #999999 solid;
}

.panel {
	background: #F3F3F3;
	padding: 0;
}

fieldset {
	margin: 15px 0;
	padding: 10px;
	border-top: 1px solid #D7D7D7;
	border-right: 1px solid #CCCCCC;
	border-bottom: 1px solid #CCCCCC;
	border-left: 1px solid #D7D7D7;
	background-color: #FFFFFF;
	position: relative;
}

.rtl fieldset {
	border-top: 1px solid #D7D7D7;
	border-right: 1px solid #D7D7D7;
	border-bottom: 1px solid #CCCCCC;
	border-left: 1px solid #CCCCCC;
}

* html fieldset {
	padding: 0 10px 5px 10px;
}

fieldset p {
	font-size: 0.85em;
}

legend {
	padding: 1px 0;
	font-family: Tahoma,arial,Verdana,Sans-serif;
	font-size: .9em;
	font-weight: bold;
	color: #115098;
	margin-top: -.4em;
	position: relative;
	text-transform: none;
	line-height: 1.2em;
	top: -.2em;
	vertical-align: middle;
}

* html legend {
	margin: 0 0 -10px -7px;
	line-height: 1em;
	font-size: .85em;
}

/* Holly hack, .rtl comes after html */
* html .rtl legend {
	margin: 0;
	margin-right: -7px;
}

fieldset.quick, p.quick {
	margin: 0 0 5px;
	padding: 5px 0 0;
	border: none;
	background-color: transparent;
	text-align: right;
}

.rtl fieldset.quick, .rtl p.quick {
	text-align: left;
}

fieldset.quick legend {
	display: none;
}

fieldset.tabulated {
	background: none;
	margin: 0;
	padding: 0;
	padding-top: 5px;
	border: 0;
}

fieldset.tabulated legend {
	display: none;
}

fieldset.nobg {
	margin: 15px 0 0 0;
	padding: 0;
	border: none;
	background-color: transparent;
}

fieldset.display-options {
	margin: 15px 0 2px 0;
	padding: 0 0 4px 0;
	border: none;
	background-color: transparent;
	text-align: center;
	font-size: 0.75em;
}

fieldset.display-options select, fieldset.display-options input, fieldset.display-options label {
	font-size: 1.00em;
	vertical-align: middle;
}

dl {
	font-family: Verdana, Helvetica, Arial, sans-serif;
	font-size: 1.00em;
}

dt {
	float: left;
	width: auto;
}

.rtl dt {
	float: right;
}

dd { color: #666666;}
dd + dd { padding-top: 5px;}
dt span { padding: 0 5px 0 0;}
.rtl dt span { padding: 0 0 0 5px;}

dt .explain { font-style: italic;}

dt label {
	font-size: 1.00em;
	text-align: left;
	font-weight: bold;
	color: #4A5A73;
}

.rtl dt label {
	text-align: right;
}

dd label {
	font-size: 1.00em;
	white-space: nowrap;
	margin: 0 10px 0 0;
	color: #4A5A73;
}

.rtl dd label {
	margin: 0 0 0 10px;
}

html>body dd label input { vertical-align: text-bottom;}	/* Tweak for Moz to align checkboxes/radio buttons nicely */

dd input {
	font-size: 1.00em;
	max-width: 100%;
	margin: 2px 0;
}

dd select {
	font-size: 100%;
	font-size: 1em;
	width: auto;
	max-width: 100%;
	margin: 2px 0;
}

dd textarea {
	font-size: 0.90em;
	width: 90%;
}

fieldset dl {
	margin-bottom: 10px;
	font-size: 0.85em;
}

fieldset dt {
	width: 45%;
	text-align: left;
	border: none;
	border-right: 1px solid #CCCCCC;
	padding-top: 3px;
}

.rtl fieldset dt {
	text-align: right;
	border: none;
	border-left: 1px solid #CCCCCC;
}

fieldset dd {
	margin: 0 0 0 45%;
	padding: 0 0 0 5px;
	border: none;
	border-left: 1px solid #CCCCCC;
	vertical-align: top;
	font-size: 1.00em;
}

.rtl fieldset dd {
	margin: 0 45% 0 0;
	padding: 0 5px 0 0;
	border: none;
	border-right: 1px solid #CCCCCC;
}

dd.full, .rtl dd.full {
	margin: 0;
	border: 0;
	padding: 0;
	padding-top: 3px;
	text-align: center;
	width: 95%;
}

fieldset dl:hover dt, fieldset dl:hover dd {
	border-color: #666666;
}

fieldset dl:hover dt label {
	color: #000000;
}

fieldset dl dd label:hover {
	color: #BC2A4D;
}

.clearfix, #tabs, .row, #content, fieldset dl, #page-body {
	height: 1%;
	overflow: hidden;
}
    -->
    </style>

</head>

<body class="ltr">
    <div id="wrap">
        <div id="page-body">
            <div id="acp">
            <div class="panel">
                <div id="content">
                    <div id="main" class="install-body">
                        <h1>Installation compatibility</h1>
                        <p>This script will be running a few simple test on your server
                        configuration and files to ensure that you are able to install and run 
                        the EVE API MOD correctly. Please ensure you read through the results 
                        thoroughly and do not proceed until all the required tests are passed.</p>
                        <fieldset>
                            <legend>PHP version and extensions</legend>
                            <p><strong>Required</strong> - You must be running at least version 5.2.0 of PHP in order to install this mod.</p>
                            <dl>
                                <dt>PHP version &gt;= 5.2.0:</dt>
                                <dd><strong style="color:<?php echo $phpversion_color ?>"><?php echo $phpversion_text ?></strong></dd>
                            </dl>
                            <dl>
                                <dt>PHP extension <var>cURL</var> is enabled:</dt>
                                <dd><strong style="color:<?php echo $extension_curl_color ?>"><?php echo $extension_curl_text ?></strong></dd>
                            </dl>
                            <dl>
                                <dt>PHP extension <var>OpenSSL</var> is enabled:</dt>
                                <dd><strong style="color:<?php echo $extension_openssl_color ?>"><?php echo $extension_openssl_text ?></strong></dd>
                            </dl>
                            <dl>
                                <dt>PHP extension <var>SimpleXML</var> is enabled:</dt>
                                <dd><strong style="color:<?php echo $extension_simplexml_color ?>"><?php echo $extension_simplexml_text ?></strong></dd>
                            </dl>
                            <dl>
                                <dt>phpBB modification <var>AutoMOD</var> equals version 1.0.2:</dt>
                                <dd><strong style="color:<?php echo $mod_automod_color ?>"><?php echo $mod_automod_text ?></strong></dd>
                            </dl>
                            <dl>
                                <dt>Cyerus&#39;s addiction equals freshly baked chocolate chip cookies:</dt>
                                <dd><strong style="color:chocolate">Hell yeah&#33;</strong></dd>
                            </dl>
                        </fieldset>
						
                        <fieldset>
                            <legend>TeamSpeak 3 integration</legend>
                            <p><strong>Required</strong> - You must have your TeamSpeak UID set in your profile in order to verify if the TeamSpeak integration is working correctly.</p>
                            <dl>
                                <dt>Username:</dt>
                                <dd><strong style="color:blue"><?php echo htmlentities($user->data['username']); ?></strong></dd>
                            </dl>
                            <dl>
                                <dt>UID:</dt>
                                <dd><strong style="color:<?php echo $teamspeak_UID_color ?>"><?php echo htmlentities($teamspeak_UID_text) ?></strong></dd>
                            </dl>
                            <dl>
                                <dt>Error code:</dt>
                                <dd><strong style="color:<?php echo $error_color ?>"><?php echo $errorCode ?></strong></dd>
                            </dl>
                            <dl>
                                <dt>Error message:</dt>
                                <dd><strong style="color:<?php echo $error_color ?>"><?php echo htmlentities($errorMsg) ?></strong></dd>
                            </dl>
                        </fieldset>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div id="page-footer">
            Powered by <a href="https://forums.eveonline.com/default.aspx?g=posts&m=184934">EVE API v<?php echo $config['eveapi_version']; ?> for phpBB 3.x</a> - <a href="#" onclick="CCPEVE.showInfo(1377, 1934722728)">Cyerus</a>
    </div>
    </div>
</body>
</html>

<?php } ?>
