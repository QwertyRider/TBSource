<?
  require "include/bittorrent.php";
  dbconn(false);
  loggedinorreturn();
	$out = $_GET['out'];
  if ($out)		// Sentbox
  {
	  stdhead("Sentbox", false);
		print("<table class=main width=750 border=0 cellspacing=0 cellpadding=10><tr><td class=embedded>\n");
  	print("<h1 align=center>Sentbox</h1>\n");
   	print("<div align=center>(<a href=" . $_SERVER['PHP_SELF'] . ">Inbox</a>)</div>\n");
	  $res = mysql_query("SELECT * FROM messages WHERE sender=" . $CURUSER["id"] . " AND location IN ('out','both') ORDER BY added DESC") or die("barf!");
	  if (mysql_num_rows($res) == 0)
      stdmsg("Information","Your Sentbox is empty!");
	  else
	  while ($arr = mysql_fetch_assoc($res))
	  {
 	  	$res2 = mysql_query("SELECT username FROM users WHERE id=" . $arr["receiver"]) or sqlerr();
	    $arr2 = mysql_fetch_assoc($res2);
	    $receiver = "<a href=userdetails.php?id=" . $arr["receiver"] . ">" . $arr2["username"] . "</a>";
	  	$elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]));
	    print("<p><table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
	    print("To <b>$receiver</b> at\n" . $arr["added"] . " ($elapsed ago) GMT\n");
      if (get_user_class() >= UC_MODERATOR && $arr["unread"] == "yes")
	    	print("<b>(<font color=red>Unread!</font>)</b>");
	    print("<p><table class=main width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
	    print(format_comment($arr["msg"]));
	    print("</td></tr></table></p>\n<p>");
	    print("<table width=100%  border=0><tr><td class=embedded>\n");
			print("<a href=deletemessage.php?id=" . $arr["id"] . "&type=out><b>Delete</b></a></td>\n");
	    print("</tr></table></tr></table></p>\n");
	  }
  }
  else		// Inbox
  {
	  stdhead("Inbox", false);
		print("<table class=main width=750 border=0 cellspacing=0 cellpadding=10><tr><td class=embedded>\n");
  	print("<h1 align=center>Inbox</h1>\n");
   	print("<div align=center>(<a href=" . $_SERVER['PHP_SELF'] . "?out=1>Sentbox</a>)</div>\n");
  	$res = mysql_query("SELECT * FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location IN ('in','both') ORDER BY added DESC") or die("barf!");
	  if (mysql_num_rows($res) == 0)
      stdmsg("Information","Your Inbox is empty!");
	  else
    	while ($arr = mysql_fetch_assoc($res))
	    {
	      if (is_valid_id($arr["sender"]))
	      {
	        $res2 = mysql_query("SELECT username FROM users WHERE id=" . $arr["sender"]) or sqlerr();
	        $arr2 = mysql_fetch_assoc($res2);
	        $sender = "<a href=userdetails.php?id=" . $arr["sender"] . ">" . ($arr2["username"]?$arr2["username"]:"[Deleted]") . "</a>";
	      }
	      else
	        $sender = "System";
	    $elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]));
	      print("<p><table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
	      print("From <b>$sender</b> at\n" . $arr["added"] . " ($elapsed ago) GMT\n");
	      if ($arr["unread"] == "yes")
	      {
	        print("<b>(<font color=red>NEW!</font>)</b>");
	        mysql_query("UPDATE messages SET unread='false' WHERE id=" . $arr["id"]) or die("arghh");
	      }
	      print("<p><table class=main width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
	      print(format_comment($arr["msg"]));
	      print("</td></tr></table></p>\n<p>");
	      print("<table width=100%  border=0><tr><td class=embedded>\n");
	      print(($arr["sender"] ? "<a href=sendmessage.php?receiver=" . $arr["sender"] . "&replyto=" . $arr["id"] .
	        "><b>Reply</b></a>" : "<font class=gray><b>Reply</b></font>") .
	        " | <a href=deletemessage.php?id=" . $arr["id"] . "&type=in><b>Delete</b></a></td>\n");
				/*
	      if (get_user_class() >= UC_MODERATOR)
	      {
	        print("<td class=embedded><div align=right>Templates: &nbsp; ".
	          ($arr["sender"] ? "<a href=sendmessage.php?receiver=" .
	          $arr["sender"] . "&replyto=" . $arr["id"] . "&auto=1" .
	          "><b>FAQ</b></a>" : "<font class=gray><b>FAQ</b></font>").
	          " | What  else?".
	          "</div></td>\n");
	      }
        */
	      print("</tr></table></tr></table></p>\n");
	    }
  }
	print("</td></tr></table>\n");
	print("<p align=center>Do you need to <a href=users.php>find</a> someone?</p>\n");
  stdfoot();
?>