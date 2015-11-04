#!/usr/bin/php
<?php

error_reporting(0);

$auth = new JabberAuth();
$auth->setSecret("CHANGE_ME");						// Set the secret password to allow us access
$auth->setForumURL("http://your-webhost.com/forum");	// Set the location where the base phpBB forum can be found at ( without the trailing '/' )
$auth->startRun();										// We simply start process !

class JabberAuth 
{
	private $_log = true;
	private $_debug = false;
	private $_logfile = "/var/log/ejabberd/exauth_phpbb.log";
	private $_rlog;
	
	private $_location = "";
	private $_secret = "";
	private $_stdin;
	private $_stdout;

	function __construct()
	{
		$this->_rlog = fopen($this->_logfile, "a") or die("Error opening log file: ". $this->_logfile);
		
		$this->log("Starting pipe-auth ...");									// We notice that it's starting ...

		$this->_stdout = fopen("php://stdout", "w");							// We open STDOUT so we can read
		$this->_stdin  = fopen("php://stdin", "r");								// and STDIN so we can talk !
	}
	
	public function startRun()
	{
		do 
		{
			$l      = fgets($this->_stdin, 3);									// We take the length of string
			$length = unpack("n", $l);											// ejabberd give us something to play with ...
			$len    = $length["1"];												// and we now know how long to read.
			if($len > 0)														// if not, we'll fill logfile ... and disk full is just funny once
			{
				$this->debug("Reading $len bytes ... ");						// We notice ...
				$data = fgets($this->_stdin, $len + 1);
				$this->debug("IN: " . $data);
			}

			$length = strlen($data);											// compute data length
			if($length > 0)														// for debug mainly ...
			{ 
				$this->debug("GO: " . $data);
				$this->debug("Data length is: " . $length);
			}

			$ret = $this->processCommand($data);								// play with data !
			$this->debug("RE: " . $ret);										// this is what WE send.

			fwrite($this->_stdout, $ret);										// We reply ...
			$dump = unpack("nn", $ret);
			$dump = $dump["n"];
			$this->debug("OUT: ". $dump);

			$data = null;
			
			$this->log("", false);												// Create an empty line in the logfile.
		} while (true);
	}
	
	public function setSecret($secret)
	{
		$this->_secret = md5(md5($secret));
	}
	
	public function setForumURL($url)
	{
		$this->_location = $url;
	}
	
	private function processCommand($data = "")
	{
		$data = explode(":", $data);                                        // This is an array, where each node is part of what SM sent to us :
																			// 0 => the command, and the others are arguments .. e.g. : user, server, password ...

		switch($data[0]) 
		{
			case "isuser":													// this is the "isuser" command, used to check for user existance
				$parms = $data[1] . " @ " . $data[2];						// only for loging purpose
				
				$return = $this->checkUser($data[1]);						// User (Server = $data[2])
			break;

			case "auth": 		// check login, password
				$parms = $data[1]." @ ".$data[2]." using password ".md5(md5($data[3]));	// only for loging purpose
				
				$return = $this->checkPass($data[1], $data[3]);				// User, Password (Server = $data[2])
			break;

			case "setpass":
				$return = false;											// We do not want jabber to be able to change password
			break;

			default:
				$this->log("Shutting down ...");							// Sorry, have to go ...
				fclose($this->_stdin);										// We close everything ...
				fclose($this->_stdout); 

				exit(0);													// never had a problem with this using ejabberd, but might lead to problem ?
			break;
		}

		$return = ($return) ? 1 : 0;
		$returnText = ($return) ? "Correct" : "Wrong";

		if(strlen($data[0]) > 0 && strlen($parms) > 0) 
		{
			$this->log("Command: " . $data[0] . " -> " . $parms . " receiving " . $returnText . ".");
		}
		return pack("nn", 2, $return);
	}
	
	private function checkPass($user, $pass)
	{
		$fields = array(
			'user'		=>      urlencode(base64_encode($user)),
			'pass'		=>      urlencode(base64_encode($pass)),
			'challenge'	=>      urlencode($this->_secret),
			'type'		=>      urlencode('checkAuth')
		);

		return $this->doXML($fields);
	}
	
	private function checkUser($user)
	{
		$fields = array(
			'user'		=>      urlencode(base64_encode($user)),
			'challenge'	=>      urlencode($this->_secret),
			'type'		=>      urlencode('isUser')
		);

		return $this->doXML($fields);
	}
	
	private function doXML($fields)
	{
		$this->log("Quering forum using cURL");

		// Make an object to represent our server.
		$curl_file_contents = $this->curl_file_get_contents($this->_location . '/xmlrpc/forum_side.php', $fields);
		
		try
		{
			$xml = new SimpleXMLElement($curl_file_contents);
			$this->log("Response received from forum -> " . $xml->response);

			return ($xml->response == "true") ? true : false;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	private function log($message, $timestamp = true)
	{
		$time = "";
		if($timestamp) 
		{
			$time = "[" . date("D d M Y H:i:s") . "]";
		}
		
		if($this->_log && is_resource($this->_rlog)) 
		{
			fwrite($this->_rlog, $time ." ". $message ."\n");
		}
	}
	
	private function debug($message)
	{
		if($this->_debug)
		{
			$this->log($message);
		}
	}
	
	public function __destruct()
	{
		$this->log("Quiting..");
		
		if (is_resource($this->_rlog)){
			fclose($this->_rlog);
		}
	}
	
	private function curl_file_get_contents($url, $fields)
	{
		try
		{
			$ch = curl_init();
			$timeout = 10; // set to zero for no timeout
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_POST, count($fields));
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

			ob_start();
			curl_exec($ch);
			curl_close($ch);
			$file_contents = ob_get_contents();
			ob_end_clean();

			return $file_contents;
		}
		catch (Exception $e)
		{
			$this->log("cURL error: " . $e->getCode() . " -> " . $e->getMessage());
			
			return false;
		}
	}
}

?>
