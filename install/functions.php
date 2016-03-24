<?php
# error reporting..
error_reporting(E_ALL ^ E_NOTICE); 

function update_config()
{
	
	$config_data = '<?php'."\n\n";
	$config_data .= '$mysql_host = \'' . $_POST['server'] . '\';' . "\n";
	$config_data .= '$mysql_db = \'' . $_POST['dbname'] . '\';' . "\n";
	$config_data .= '$mysql_user = \'' . $_POST['dbuser'] . '\';' . "\n";
	$config_data .= '$mysql_pass = \'' . $_POST['dbpass'] . '\';' . "\n\n";
	$config_data .= 'define(\'TB_INSTALLED\', true);'."\n\n";	
	$config_data .= '?' . '>'; // Done this to prevent highlighting editors getting confused!
	if(!($fp = fopen('../include/secrets.php', 'w')))
	{
		die('Make secrets.php writable -> 666');
	}
	else
	{
			$result = @fputs($fp, $config_data, strlen($config_data));

			@fclose($fp);
	}
	
	if( !mysql_connect($_POST['server'],$_POST['dbuser'],$_POST['dbpass']) )
	{
		die('Cant connect to databaseserver');
	}
	if( !mysql_select_db($_POST['dbname']) )
	{
		die('Cant select database');
	}
}
 
	
function basic_query()
{
	
	$sql_lines = implode(' ', file(dirname(__FILE__) . '/install.sql'));
	$sql_lines = explode("\n", $sql_lines);
	
	include('../include/secrets.php');
	
	if( !mysql_connect($mysql_host,$mysql_user,$mysql_pass) )
	{
		die('Cant connect to databaseserver');
	}
	if( !mysql_select_db($mysql_db) )
	{
		die('Cant select database');
	}

	// Execute the SQL.
	$current_statement = '';
	$failures = array();
	$exists = array();
	foreach ($sql_lines as $count => $line)
	{
		// No comments allowed!
		if (substr($line, 0, 1) != '#')
			$current_statement .= "\n" . rtrim($line);

		// Is this the end of the query string?
		if (empty($current_statement) || (preg_match('~;[\s]*$~s', $line) == 0 && $count != count($sql_lines)))
			continue;

		// Does this table already exist?  If so, don't insert more data into it!
		if (preg_match('~^\s*INSERT INTO ([^\s\n\r]+?)~', $current_statement, $match) != 0 && in_array($match[1], $exists))
		{
			$current_statement = '';
			continue;
		}

		if (!mysql_query($current_statement))
		{
			$error_message = mysql_error($db_connection);

			// Error 1050: Table already exists!
			if (strpos($error_message, 'already exists') === false)
				$failures[$count] = $error_message;
			elseif (preg_match('~^\s*CREATE TABLE ([^\s\n\r]+?)~', $current_statement, $match) != 0)
				$exists[] = $match[1];
		}

		$current_statement = '';
	}
}
	
function insert_sysop()
{
	if( $_POST['sysoppass'] != $_POST['sysoppass2'] )
	{
		die('error:  The sysop passwords do not match!');
	}
	
	$username = $_POST['sysopuser'];
	$usermail = $_POST['sysopmail'];
	

	
	$secret = mksecret();
	$wantpasshash = md5($secret . $_POST['sysoppass'] . $secret);
	$editsecret = mksecret();

	$ret = mysql_query("INSERT INTO users (username, class, passhash, secret, editsecret, email, status, added) VALUES (" .
		implode(",", array_map("sqlesc", array($username, 6, $wantpasshash, $secret, $editsecret, $usermail, 'confirmed'))) .
		",'" . get_date_time() . "')");
	
	
}

function config()
{
	$online = gmdate("Y-m-d");
	$added = sqlesc(get_date_time());
	mysql_query("INSERT INTO config (name,value) VALUES ('siteonline','true')");
	mysql_query("INSERT INTO config (name,value) VALUES ('onlinesince','$online')");
	mysql_query("INSERT INTO config (name,value) VALUES ('sitename','".$_POST['sitename']."')");
	mysql_query("INSERT INTO config (name,value) VALUES ('domain','".$_POST['domain']."')");
	mysql_query("INSERT INTO config (name,value) VALUES ('announce_url','".$_POST['announce']."')");
	mysql_query("INSERT INTO config (name,value) VALUES ('sitemail','".$_POST['sitemail']."')");
	mysql_query("INSERT INTO config (name,value) VALUES ('funds',0 )");	
	mysql_query("INSERT INTO config (name,value) VALUES ('peerlimit',1000 )");
	mysql_query("INSERT INTO news (userid,body,added) VALUES (1,'Welcome to your new tbsource installation',$added)");
}
	
function finale()
{
	echo'Install Finished <BR>
	Please remove the install directory or use chmod to make it non-accessible<BR>
    
	You may login <BR>
	<a href="../index.php">HERE</a>
	';
}
  
function mksecret($len = 20) {
 $ret = "";
 for ($i = 0; $i < $len; $i++)
     $ret .= chr(mt_rand(0, 255));
 return $ret;
}

function get_date_time($timestamp = 0)
{
  if ($timestamp)
    return date("Y-m-d H:i:s", $timestamp);
  else
    return gmdate("Y-m-d H:i:s");
}

function sqlesc($x) {
 return "'".mysql_real_escape_string($x)."'";
}

  
?>
