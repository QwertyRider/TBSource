<?php
  require "include/bittorrent.php";

  if ($HTTP_SERVER_VARS["REQUEST_METHOD"] != "POST")
    stderr("Error", "Method");

  dbconn();

  loggedinorreturn();
  $n_pms = $_POST["n_pms"];
  if ($n_pms)
  {  			                                                      //////  MM  ///
    if (get_user_class() < UC_MODERATOR)
	  stderr("Error", "Permission denied");

    $msg = trim($_POST["msg"]);
		if (!$msg)
	  	stderr("Error","Please enter something!");

    $sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);

    $from_is = $_POST['pmees'];

    $query = "INSERT INTO messages (sender, receiver, added, msg, poster) ".
             "SELECT $sender_id, u.id, '" . get_date_time() . "', " . sqlesc($msg) .
             ", $sender_id " . $from_is;

    mysql_query($query) or sqlerr(__FILE__, __LINE__);
    $n = mysql_affected_rows();

    $comment = $_POST['comment'];
    $snapshot = $_POST['snap'];

    // add a custom text or stats snapshot to comments in profile
    if ($comment || $snapshot)
    {
	    $res = mysql_query("SELECT u.id, u.uploaded, u.downloaded, u.modcomment ".$from_is) or sqlerr(__FILE__, __LINE__);
	    if (mysql_num_rows($res) > 0)
	    {
	      $l = 0;
	      while ($user = mysql_fetch_array($res))
	      {
	        unset($new);
	        $old = $user['modcomment'];
	        if ($comment)
	          $new = $comment;
	        if ($snapshot)
	        {
	          $new .= ($new?"\n":"") .
	            "MMed, " . gmdate("Y-m-d") . ", " .
	            "UL: " . mksizegb($user['uploaded']) . ", " .
	            "DL: " . mksizegb($user['downloaded']) . ", " .
	            "r: " . ratios($user['uploaded'],$user['downloaded'], False) . " - " .
	            ($_POST['sender'] == "system"?"System":$CURUSER['username']);
	        }
	      	$new .= $old?("\n".$old):$old;
		      mysql_query("UPDATE users SET modcomment = " . sqlesc($new) . " WHERE id = " . $user['id'])
		        or sqlerr(__FILE__, __LINE__);
	  	    if (mysql_affected_rows())
	    	    $l++;
	      }
	    }
    }
  }
  else
  {               																							//////  PM  ///
  	$receiver = $_POST["receiver"];
	  $origmsg = $_POST["origmsg"];
	  $save = $_POST["save"];
 	  $returnto = $_POST["returnto"];

	  if (!is_valid_id($receiver) || ($origmsg && !is_valid_id($origmsg)))
	  	stderr("Error","Invalid ID");

	  $msg = trim($_POST["msg"]);
	  if (!$msg)
	    stderr("Error","Please enter something!");

	  $location = ($save == 'yes') ? "both" : "in";

	  $res = mysql_query("SELECT acceptpms, notifs, UNIX_TIMESTAMP(last_access) as la FROM users WHERE id=$receiver") or sqlerr(__FILE__, __LINE__);
	  $user = mysql_fetch_assoc($res);
	  if (!$user)
	    stderr("Error", "No user with ID $receiver.");

	  //Make sure recipient wants this message
		if (get_user_class() < UC_MODERATOR)
		{
    	if ($user["acceptpms"] == "yes")
	    {
	      $res2 = mysql_query("SELECT * FROM blocks WHERE userid=$receiver AND blockid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
	      if (mysql_num_rows($res2) == 1)
	        stderr("Refused", "This user has blocked PMs from you.");
	    }
	    elseif ($user["acceptpms"] == "friends")
	    {
	      $res2 = mysql_query("SELECT * FROM friends WHERE userid=$receiver AND friendid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
	      if (mysql_num_rows($res2) != 1)
	        stderr("Refused", "This user only accepts PMs from users in his friends list.");
	    }
	    elseif ($user["acceptpms"] == "no")
	      stderr("Refused", "This user does not accept PMs.");
	  }

	  mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg, location) VALUES(" . $CURUSER["id"] . ", " .
	  $CURUSER["id"] . ", $receiver, '" . get_date_time() . "', " .
	  sqlesc($msg) . ", " . sqlesc($location) . ")") or sqlerr(__FILE__, __LINE__);

	  if (strpos($user['notifs'], '[pm]') !== false)
	  {
	    if (gmtime() - $user["la"] >= 300)
	    {
	    $username = $CURUSER["username"];
$body = <<<EOD
You have received a PM from $username!

You can use the URL below to view the message (you may have to login).

$DEFAULTBASEURL/inbox.php

--
$SITENAME
EOD;
	    @mail($user["email"], "You have received a PM from " . $username . "!",
	    	$body, "From: $SITEEMAIL", "-f$SITEEMAIL");
	    }
	  }
	  $delete = $_POST["delete"];

	  if ($origmsg)
	  {
      if ($delete == "yes")
      {
	      // Make sure receiver of $origmsg is current user
	      $res = mysql_query("SELECT * FROM messages WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);
	      if (mysql_num_rows($res) == 1)
	      {
	        $arr = mysql_fetch_assoc($res);
	        if ($arr["receiver"] != $CURUSER["id"])
	          stderr("w00t","This shouldn't happen.");
	        if ($arr["location"] == "in")
	        	mysql_query("DELETE FROM messages WHERE id=$origmsg AND location = 'in'") or sqlerr(__FILE__, __LINE__);
	        elseif ($arr["location"] == "both")
	        	mysql_query("UPDATE messages SET location = 'out' WHERE id=$origmsg AND location = 'both'") or sqlerr(__FILE__, __LINE__);
	      }
      }
   	  if (!$returnto)
   	  	$returnto = "$DEFAULTBASEURL/inbox.php";
	  }

    if ($returnto)
    {
      header("Location: $returnto");
      die;
    }

	  stdhead();
	  stdmsg("Succeeded", (($n_pms > 1) ? "$n messages out of $n_pms were" : "Message was").
	    " successfully sent!" . ($l ? " $l profile comment" . (($l>1) ? "s were" : " was") . " updated!" : ""));
	}
	stdfoot();
	exit;
?>