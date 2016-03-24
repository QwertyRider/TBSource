<?
	ob_start("ob_gzhandler");

  require "include/bittorrent.php";

  dbconn(false);

  loggedinorreturn();

  $action = $HTTP_GET_VARS["action"];

  function catch_up()
  {
	die("This feature is currently unavailable.");
    global $CURUSER;

    $userid = $CURUSER["id"];

    $res = mysql_query("SELECT id, lastpost FROM topics") or sqlerr(__FILE__, __LINE__);

    while ($arr = mysql_fetch_assoc($res))
    {
      $topicid = $arr["id"];

      $postid = $arr["lastpost"];

      $r = mysql_query("SELECT id,lastpostread FROM readposts WHERE userid=$userid and topicid=$topicid") or sqlerr(__FILE__, __LINE__);

      if (mysql_num_rows($r) == 0)
        mysql_query("INSERT INTO readposts (userid, topicid, lastpostread) VALUES($userid, $topicid, $postid)") or sqlerr(__FILE__, __LINE__);

      else
      {
        $a = mysql_fetch_assoc($r);

        if ($a["lastpostread"] < $postid)
          mysql_query("UPDATE readposts SET lastpostread=$postid WHERE id=" . $a["id"]) or sqlerr(__FILE__, __LINE__);
      }
    }
  }

  //-------- Returns the minimum read/write class levels of a forum

  function get_forum_access_levels($forumid)
  {
    $res = mysql_query("SELECT minclassread, minclasswrite, minclasscreate FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      return false;

    $arr = mysql_fetch_assoc($res);

    return array("read" => $arr["minclassread"], "write" => $arr["minclasswrite"], "create" => $arr["minclasscreate"]);
  }

  //-------- Returns the forum ID of a topic, or false on error

  function get_topic_forum($topicid)
  {
    $res = mysql_query("SELECT forumid FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      return false;

    $arr = mysql_fetch_row($res);

    return $arr[0];
  }

  //-------- Returns the ID of the last post of a forum

  function update_topic_last_post($topicid)
  {
    $res = mysql_query("SELECT id FROM posts WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res) or die("No post found");

    $postid = $arr[0];

    mysql_query("UPDATE topics SET lastpost=$postid WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
  }

  function get_forum_last_post($forumid)
  {
    $res = mysql_query("SELECT lastpost FROM topics WHERE forumid=$forumid ORDER BY lastpost DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $postid = $arr[0];

    if ($postid)
      return $postid;

    else
      return 0;
  }

  //-------- Inserts a quick jump menu

  function insert_quick_jump_menu($currentforum = 0)
  {
    print("<p align=center><form method=get action=? name=jump>\n");

    print("<input type=hidden name=action value=viewforum>\n");

    print("Quick jump: ");

    print("<select name=forumid onchange=\"if(this.options[this.selectedIndex].value != -1){ forms['jump'].submit() }\">\n");

    $res = mysql_query("SELECT * FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);

    while ($arr = mysql_fetch_assoc($res))
    {
      if (get_user_class() >= $arr["minclassread"])
        print("<option value=" . $arr["id"] . ($currentforum == $arr["id"] ? " selected>" : ">") . $arr["name"] . "\n");
    }

    print("</select>\n");

    print("<input type=submit value='Go!'>\n");

    print("</form>\n</p>");
  }

  //-------- Inserts a compose frame

  function insert_compose_frame($id, $newtopic = true, $quote = false)
  {
    global $maxsubjectlength, $CURUSER;

    if ($newtopic)
    {
      $res = mysql_query("SELECT name FROM forums WHERE id=$id") or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_assoc($res) or die("Bad forum id");

      $forumname = $arr["name"];

      print("<p align=center>New topic in <a href=?action=viewforum&forumid=$id>$forumname</a> forum</p>\n");
    }
    else
    {
      $res = mysql_query("SELECT * FROM topics WHERE id=$id") or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_assoc($res) or stderr("Forum error", "Topic not found.");

      $subject = $arr["subject"];

      print("<p align=center>Reply to topic: <a href=?action=viewtopic&topicid=$id>$subject</a></p>");
    }

    begin_frame("Compose", true);

    print("<form method=post action=?action=post>\n");

    if ($newtopic)
      print("<input type=hidden name=forumid value=$id>\n");

    else
      print("<input type=hidden name=topicid value=$id>\n");

    begin_table();

    if ($newtopic)
      print("<tr><td class=rowhead>Subject</td>" .
        "<td align=left style='padding: 0px'><input type=text size=100 maxlength=$maxsubjectlength name=subject " .
        "style='border: 0px; height: 19px'></td></tr>\n");

    if ($quote)
    {
       $postid = $_GET["postid"];
       if (!is_valid_id($postid))
         die;

	   $res = mysql_query("SELECT posts.*, users.username FROM posts JOIN users ON posts.userid = users.id WHERE posts.id=$postid") or sqlerr(__FILE__, __LINE__);

	   if (mysql_num_rows($res) != 1)
	     stderr("Error", "No post with ID $postid.");

	   $arr = mysql_fetch_assoc($res);
    }

    print("<tr><td class=rowhead>Body</td><td align=left style='padding: 0px'>" .
    "<textarea name=body cols=100 rows=20 style='border: 0px'>".
    ($quote?(("[quote=".htmlspecialchars($arr["username"])."]".htmlspecialchars($arr["body"])."[/quote]")):"").
    "</textarea></td></tr>\n");

    print("<tr><td colspan=2 align=center><input type=submit class=btn value='Submit'></td></tr>\n");

    end_table();

    print("</form>\n");

		print("<p align=center><a href=tags.php target=_blank>Tags</a> | <a href=smilies.php target=_blank>Smilies</a></p>\n");

    end_frame();

    //------ Get 10 last posts if this is a reply

    if (!$newtopic)
    {
      $postres = mysql_query("SELECT * FROM posts WHERE topicid=$id ORDER BY id DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);

      begin_frame("10 last posts, in reverse order");

      while ($post = mysql_fetch_assoc($postres))
      {
        //-- Get poster details

        $userres = mysql_query("SELECT * FROM users WHERE id=" . $post["userid"] . " LIMIT 1") or sqlerr(__FILE__, __LINE__);

        $user = mysql_fetch_assoc($userres);

      	$avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($user["avatar"]) : "");
//	    $avatar = $user["avatar"];

        if (!$avatar)
          $avatar = "pic/default_avatar.gif";

        print("<p class=sub>#" . $post["id"] . " by " . $user["username"] . " at " . $post["added"] . " GMT</p>");

        begin_table(true);

        print("<tr valign=top><td width=150 align=center style='padding: 0px'>" . ($avatar ? "<img width=150 src=$avatar>" : "").
          "</td><td class=comment>" . format_comment($post["body"]) . "</td></tr>\n");

        end_table();

      }

      end_frame();

    }

  insert_quick_jump_menu();

  }

  //-------- Global variables

  $maxsubjectlength = 40;
  $postsperpage = $CURUSER["postsperpage"];
	if (!$postsperpage) $postsperpage = 25;

  //-------- Action: New topic

  if ($action == "newtopic")
  {
    $forumid = $_GET["forumid"];

    if (!is_valid_id($forumid))
      die;

    stdhead("New topic");

    begin_main_frame();

    insert_compose_frame($forumid);

    end_main_frame();

    stdfoot();

    die;
  }

  //-------- Action: Post

  if ($action == "post")
  {
    $forumid = 0 + $_POST["forumid"];
    $topicid = 0 + $_POST["topicid"];

    if (!is_valid_id($forumid) && !is_valid_id($topicid))
      stderr("Error", "Bad forum or topic ID.");

    $newtopic = $forumid > 0;

    $subject = $_POST["subject"];

    if ($newtopic)
    {
      $subject = trim($subject);

      if (!$subject)
        stderr("Error", "You must enter a subject.");

      if (strlen($subject) > $maxsubjectlength)
        stderr("Error", "Subject is limited to $maxsubjectlength characters.");
    }
    else
      $forumid = get_topic_forum($topicid) or die("Bad topic ID");

    //------ Make sure sure user has write access in forum

    $arr = get_forum_access_levels($forumid) or die("Bad forum ID");

    if (get_user_class() < $arr["write"] || ($newtopic && get_user_class() < $arr["create"]))
      stderr("Error", "Permission denied.");

    $body = trim($_POST["body"]);

    if ($body == "")
      stderr("Error", "No body text.");

    $userid = $CURUSER["id"];

    if ($newtopic)
    {
      //---- Create topic

      $subject = sqlesc($subject);

      mysql_query("INSERT INTO topics (userid, forumid, subject) VALUES($userid, $forumid, $subject)") or sqlerr(__FILE__, __LINE__);

      $topicid = mysql_insert_id() or stderr("Error", "No topic ID returned");
    }
    else
    {
      //---- Make sure topic exists and is unlocked

      $res = mysql_query("SELECT * FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_assoc($res) or die("Topic id n/a");

      if ($arr["locked"] == 'yes' && get_user_class() < UC_MODERATOR)
        stderr("Error", "This topic is locked.");

      //---- Get forum ID

      $forumid = $arr["forumid"];
    }

    //------ Insert post

    $added = "'" . get_date_time() . "'";

    $body = sqlesc($body);

    mysql_query("INSERT INTO posts (topicid, userid, added, body) " .
    "VALUES($topicid, $userid, $added, $body)") or sqlerr(__FILE__, __LINE__);

    $postid = mysql_insert_id() or die("Post id n/a");

    //------ Update topic last post

    update_topic_last_post($topicid);

    //------ All done, redirect user to the post

    $headerstr = "Location: $DEFAULTBASEURL/forums.php?action=viewtopic&topicid=$topicid&page=last";

    if ($newtopic)
      header($headerstr);

    else
      header("$headerstr#$postid");

    die;
  }

  //-------- Action: View topic

  if ($action == "viewtopic")
  {
    $topicid = $_GET["topicid"];

    $page = $_GET["page"];

    if (!is_valid_id($topicid))
      die;

    $userid = $CURUSER["id"];

    //------ Get topic info

    $res = mysql_query("SELECT * FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res) or stderr("Forum error", "Topic not found");

    $locked = ($arr["locked"] == 'yes');
    $subject = $arr["subject"];
    $sticky = $arr["sticky"] == "yes";
    $forumid = $arr["forumid"];

	//------ Update hits column

    mysql_query("UPDATE topics SET views = views + 1 WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    //------ Get forum

    $res = mysql_query("SELECT * FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res) or die("Forum = NULL");

    $forum = $arr["name"];

    if ($CURUSER["class"] < $arr["minclassread"])
		stderr("Error", "You are not permitted to view this topic.");

    //------ Get post count

    $res = mysql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $postcount = $arr[0];

    //------ Make page menu

    $pagemenu = "<p>\n";

    $perpage = $postsperpage;

    $pages = ceil($postcount / $perpage);

    if ($page[0] == "p")
  	{
	    $findpost = substr($page, 1);
	    $res = mysql_query("SELECT id FROM posts WHERE topicid=$topicid ORDER BY added") or sqlerr(__FILE__, __LINE__);
	    $i = 1;
	    while ($arr = mysql_fetch_row($res))
	    {
	      if ($arr[0] == $findpost)
	        break;
	      ++$i;
	    }
	    $page = ceil($i / $perpage);
	  }

    if ($page == "last")
      $page = $pages;
    else
    {
      if($page < 1)
        $page = 1;
      elseif ($page > $pages)
        $page = $pages;
    }

    $offset = $page * $perpage - $perpage;

    for ($i = 1; $i <= $pages; ++$i)
    {
      if ($i == $page)
        $pagemenu .= "<font class=gray><b>$i</b></font>\n";

      else
        $pagemenu .= "<a href=?action=viewtopic&topicid=$topicid&page=$i><b>$i</b></a>\n";
    }

    if ($page == 1)
      $pagemenu .= "<br><font class=gray><b>&lt;&lt; Prev</b></font>";

    else
      $pagemenu .= "<br><a href=?action=viewtopic&topicid=$topicid&page=" . ($page - 1) .
        "><b>&lt;&lt; Prev</b></a>";

    $pagemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    if ($page == $pages)
      $pagemenu .= "<font class=gray><b>Next &gt;&gt;</b></font></p>\n";

    else
      $pagemenu .= "<a href=?action=viewtopic&topicid=$topicid&page=" . ($page + 1) .
        "><b>Next &gt;&gt;</b></a></p>\n";

    //------ Get posts

    $res = mysql_query("SELECT * FROM posts WHERE topicid=$topicid ORDER BY id LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__);

    stdhead("View topic");

    print("<a name=top><h1><a href=?action=viewforum&forumid=$forumid>$forum</a> &gt; $subject</h1>\n");

    print($pagemenu);

    //------ Print table

    begin_main_frame();

    begin_frame();

    $pc = mysql_num_rows($res);

    $pn = 0;

    $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=" . $CURUSER["id"] . " AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);

    $a = mysql_fetch_row($r);

    $lpr = $a[0];

    if (!$lpr)
      mysql_query("INSERT INTO readposts (userid, topicid) VALUES($userid, $topicid)") or sqlerr(__FILE__, __LINE__);

    while ($arr = mysql_fetch_assoc($res))
    {
      ++$pn;

      $postid = $arr["id"];

      $posterid = $arr["userid"];

      $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

      //---- Get poster details

      $res2 = mysql_query("SELECT username,class,avatar,donor,title,enabled,warned FROM users WHERE id=$posterid") or sqlerr(__FILE__, __LINE__);

      $arr2 = mysql_fetch_assoc($res2);

      $postername = $arr2["username"];

      if ($postername == "")
      {
        $by = "unknown[$posterid]";

        $avatar = "";
      }
      else
      {
//		if ($arr2["enabled"] == "yes")
	        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($arr2["avatar"]) : "");
//	    else
//			$avatar = "pic/disabled_avatar.gif";

        $title = $arr2["title"];

        if (!$title)
          $title = get_user_class_name($arr2["class"]);

        $by = "<a href=userdetails.php?id=$posterid><b>$postername</b></a>" . ($arr2["donor"] == "yes" ? "<img src=".
        "pic/star.gif alt='Donor'>" : "") . ($arr2["enabled"] == "no" ? "<img src=".
        "pic/disabled.gif alt=\"This account is disabled\" style='margin-left: 2px'>" : ($arr2["warned"] == "yes" ? "<a href=rules.php#warning class=altlink><img src=pic/warned.gif alt=\"Warned\" border=0></a>" : "")) . " ($title)";
      }

      if (!$avatar)
        $avatar = "pic/default_avatar.gif";

      print("<a name=$postid>\n");

      if ($pn == $pc)
      {
        print("<a name=last>\n");
        if ($postid > $lpr)
          mysql_query("UPDATE readposts SET lastpostread=$postid WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);
      }

      print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded width=99%>#$postid by $by at $added");

      if (!$locked || get_user_class() >= UC_MODERATOR)
				print(" - [<a href=?action=quotepost&topicid=$topicid&postid=$postid><b>Quote</b></a>]");

      if (($CURUSER["id"] == $posterid && !$locked) || get_user_class() >= UC_MODERATOR)
        print(" - [<a href=?action=editpost&postid=$postid><b>Edit</b></a>]");

      if (get_user_class() >= UC_MODERATOR)
        print(" - [<a href=?action=deletepost&postid=$postid><b>Delete</b></a>]");

      print("</td><td class=embedded width=1%><a href=#top><img src=pic/top.gif border=0 alt='Top'></a></td></tr>");

      print("</table></p>\n");

      begin_table(true);

      $body = format_comment($arr["body"]);

      if (is_valid_id($arr['editedby']))
      {
        $res2 = mysql_query("SELECT username FROM users WHERE id=$arr[editedby]");
        if (mysql_num_rows($res2) == 1)
        {
          $arr2 = mysql_fetch_assoc($res2);
          $body .= "<p><font size=1 class=small>Last edited by <a href=userdetails.php?id=$arr[editedby]><b>$arr2[username]</b></a> at $arr[editedat] GMT</font></p>\n";
        }
      }


      print("<tr valign=top><td width=150 align=center style='padding: 0px'>" .
        ($avatar ? "<img width=150 src=\"$avatar\">" : ""). "</td><td class=comment>$body</td></tr>\n");

      end_table();
    }

    //------ Mod options

	  if (get_user_class() >= UC_MODERATOR)
	  {
	    attach_frame();

	    $res = mysql_query("SELECT id,name,minclasswrite FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);
	    print("<table border=0 cellspacing=0 cellpadding=0>\n");

	    print("<form method=post action=?action=setsticky>\n");
	    print("<input type=hidden name=topicid value=$topicid>\n");
	    print("<input type=hidden name=returnto value=$DEFAULTBASEURL$HTTP_SERVER_VARS[REQUEST_URI]>\n");
	    print("<tr><td class=embedded align=right>Sticky:</td>\n");
	    print("<td class=embedded><input type=radio name=sticky value='yes' " . ($sticky ? " checked" : "") . "> Yes <input type=radio name=sticky value='no' " . (!$sticky ? " checked" : "") . "> No\n");
	    print("<input type=submit value='Set'></td></tr>");
	    print("</form>\n");

	    print("<form method=post action=?action=setlocked>\n");
	    print("<input type=hidden name=topicid value=$topicid>\n");
	    print("<input type=hidden name=returnto value=$DEFAULTBASEURL$HTTP_SERVER_VARS[REQUEST_URI]>\n");
	    print("<tr><td class=embedded align=right>Locked:</td>\n");
	    print("<td class=embedded><input type=radio name=locked value='yes' " . ($locked ? " checked" : "") . "> Yes <input type=radio name=locked value='no' " . (!$locked ? " checked" : "") . "> No\n");
	    print("<input type=submit value='Set'></td></tr>");
	    print("</form>\n");

	    print("<form method=post action=?action=renametopic>\n");
	    print("<input type=hidden name=topicid value=$topicid>\n");
	    print("<input type=hidden name=returnto value=$DEFAULTBASEURL$HTTP_SERVER_VARS[REQUEST_URI]>\n");
	    print("<tr><td class=embedded align=right>Rename topic:</td><td class=embedded><input type=text name=subject size=60 maxlength=$maxsubjectlength value=\"" . htmlspecialchars($subject) . "\">\n");
	    print("<input type=submit value='Okay'></td></tr>");
	    print("</form>\n");

	    print("<form method=post action=?action=movetopic&topicid=$topicid>\n");
	    print("<tr><td class=embedded>Move this thread to:&nbsp;</td><td class=embedded><select name=forumid>");

	    while ($arr = mysql_fetch_assoc($res))
	      if ($arr["id"] != $forumid && get_user_class() >= $arr["minclasswrite"])
	        print("<option value=" . $arr["id"] . ">" . $arr["name"] . "\n");

	    print("</select> <input type=submit value='Okay'></form></td></tr>\n");
	    print("<tr><td class=embedded>Delete topic</td><td class=embedded>\n");
	    print("<form method=get action=/forums>\n");
	    print("<input type=hidden name=action value=deletetopic>\n");
	    print("<input type=hidden name=topicid value=$topicid>\n");
	    print("<input type=hidden name=forumid value=$forumid>\n");
	    print("<input type=checkbox name=sure value=1>I'm sure\n");
	    print("<input type=submit value='Okay'>\n");
	    print("</form>\n");
	    print("</td></tr>\n");
	    print("</table>\n");
	  }

  	end_frame();

  	end_main_frame();

  	print($pagemenu);

  	if ($locked && get_user_class() < UC_MODERATOR)
  		print("<p>This topic is locked; no new posts are allowed.</p>\n");

  	else
  	{
	    $arr = get_forum_access_levels($forumid) or die;

	    if (get_user_class() < $arr["write"])
	      print("<p><i>You are not permitted to post in this forum.</i></p>\n");

	    else
	      $maypost = true;
	  }

	  //------ "View unread" / "Add reply" buttons

	  print("<p><table class=main border=0 cellspacing=0 cellpadding=0><tr>\n");
	  print("<td class=embedded><form method=get action=?>\n");
	  print("<input type=hidden name=action value=viewunread>\n");
	  print("<input type=submit value='View Unread' class=btn>\n");
	  print("</form></td>\n");

    if ($maypost)
    {
      print("<td class=embedded style='padding-left: 10px'><form method=get action=?>\n");
      print("<input type=hidden name=action value=reply>\n");
      print("<input type=hidden name=topicid value=$topicid>\n");
      print("<input type=submit value='Add Reply' class=btn>\n");
      print("</form></td>\n");
    }
    print("</tr></table></p>\n");

    //------ Forum quick jump drop-down

    insert_quick_jump_menu($forumid);

    stdfoot();

    die;
  }

  //-------- Action: Quote

	if ($action == "quotepost")
	{
		$topicid = $_GET["topicid"];

		if (!is_valid_id($topicid))
			stderr("Error", "Invalid topic ID $topicid.");

    stdhead("Post reply");

    begin_main_frame();

    insert_compose_frame($topicid, false, true);

    end_main_frame();

    stdfoot();

    die;
  }

  //-------- Action: Reply

  if ($action == "reply")
  {
    $topicid = $_GET["topicid"];

    if (!is_valid_id($topicid))
      die;

    stdhead("Post reply");

    begin_main_frame();

    insert_compose_frame($topicid, false);

    end_main_frame();

    stdfoot();

    die;
  }

  //-------- Action: Move topic

  if ($action == "movetopic")
  {
    $forumid = $_POST["forumid"];

    $topicid = $_GET["topicid"];

    if (!is_valid_id($forumid) || !is_valid_id($topicid) || get_user_class() < UC_MODERATOR)
      die;

    // Make sure topic and forum is valid

    $res = @mysql_query("SELECT minclasswrite FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      stderr("Error", "Forum not found.");

    $arr = mysql_fetch_row($res);

    if (get_user_class() < $arr[0])
      die;

    $res = @mysql_query("SELECT subject,forumid FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      stderr("Error", "Topic not found.");

    $arr = mysql_fetch_assoc($res);

    if ($arr["forumid"] != $forumid)
      @mysql_query("UPDATE topics SET forumid=$forumid WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    // Redirect to forum page

    header("Location: $DEFAULTBASEURL/forums.php?action=viewforum&forumid=$forumid");

    die;
  }

  //-------- Action: Delete topic

  if ($action == "deletetopic")
  {
    $topicid = $_GET["topicid"];
    $forumid = $_GET["forumid"];

    if (!is_valid_id($topicid) || get_user_class() < UC_MODERATOR)
      die;

    $sure = $_GET["sure"];

    if (!$sure)
    {
      stderr("Delete topic", "Sanity check: You are about to delete a topic. Click\n" .
      "<a href=?action=deletetopic&topicid=$topicid&sure=1>here</a> if you are sure.");
    }

    mysql_query("DELETE FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    mysql_query("DELETE FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

    header("Location: $DEFAULTBASEURL/forums.php?action=viewforum&forumid=$forumid");

    die;
  }

  //-------- Action: Edit post

  if ($action == "editpost")
  {
    $postid = $HTTP_GET_VARS["postid"];

    if (!is_valid_id($postid))
      die;

    $res = mysql_query("SELECT * FROM posts WHERE id=$postid") or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) != 1)
			stderr("Error", "No post with ID $postid.");

		$arr = mysql_fetch_assoc($res);

    $res2 = mysql_query("SELECT locked FROM topics WHERE id = " . $arr["topicid"]) or sqlerr(__FILE__, __LINE__);
		$arr2 = mysql_fetch_assoc($res2);

 		if (mysql_num_rows($res) != 1)
			stderr("Error", "No topic associated with post ID $postid.");

		$locked = ($arr2["locked"] == 'yes');

    if (($CURUSER["id"] != $arr["userid"] || $locked) && get_user_class() < UC_MODERATOR)
      stderr("Error", "Denied!");

    if ($HTTP_SERVER_VARS['REQUEST_METHOD'] == 'POST')
    {
    	$body = $HTTP_POST_VARS['body'];

    	if ($body == "")
    	  stderr("Error", "Body cannot be empty!");

      $body = sqlesc($body);

      $editedat = sqlesc(get_date_time());

      mysql_query("UPDATE posts SET body=$body, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$postid") or sqlerr(__FILE__, __LINE__);

		$returnto = $HTTP_POST_VARS["returnto"];

			if ($returnto != "")
			{
				$returnto .= "&page=p$postid#$postid";
				header("Location: $returnto");
			}
			else
				stderr("Success", "Post was edited successfully.");
    }

    stdhead();

    print("<h1>Edit Post</h1>\n");

    print("<form method=post action=?action=editpost&postid=$postid>\n");
    print("<input type=hidden name=returnto value=\"" . htmlspecialchars($HTTP_SERVER_VARS["HTTP_REFERER"]) . "\">\n");

    print("<table border=1 cellspacing=0 cellpadding=5>\n");

    print("<tr><td style='padding: 0px'><textarea name=body cols=100 rows=20 style='border: 0px'>" . htmlspecialchars($arr["body"]) . "</textarea></td></tr>\n");

    print("<tr><td align=center><input type=submit value='Okay' class=btn></td></tr>\n");

    print("</table>\n");

    print("</form>\n");

    stdfoot();

  	die;
  }

  //-------- Action: Delete post

  if ($action == "deletepost")
  {
    $postid = $_GET["postid"];

    $sure = $_GET["sure"];

    if (get_user_class() < UC_MODERATOR || !is_valid_id($postid))
      die;

    //------- Get topic id

    $res = mysql_query("SELECT topicid FROM posts WHERE id=$postid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res) or stderr("Error", "Post not found");

    $topicid = $arr[0];

    //------- We can not delete the post if it is the only one of the topic

    $res = mysql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    if ($arr[0] < 2)
      stderr("Error", "Can't delete post; it is the only post of the topic. You should\n" .
      "<a href=?action=deletetopic&topicid=$topicid&sure=1>delete the topic</a> instead.\n");


    //------- Get the id of the last post before the one we're deleting

    $res = mysql_query("SELECT id FROM posts WHERE topicid=$topicid AND id < $postid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($res) == 0)
			$redirtopost = "";
		else
		{
			$arr = mysql_fetch_row($res);
			$redirtopost = "&page=p$arr[0]#$arr[0]";
		}

    //------- Make sure we know what we do :-)

    if (!$sure)
    {
      stderr("Delete post", "Sanity check: You are about to delete a post. Click\n" .
      "<a href=?action=deletepost&postid=$postid&sure=1>here</a> if you are sure.");
    }

    //------- Delete post

    mysql_query("DELETE FROM posts WHERE id=$postid") or sqlerr(__FILE__, __LINE__);

    //------- Update topic

    update_topic_last_post($topicid);

    header("Location: $DEFAULTBASEURL/forums.php?action=viewtopic&topicid=$topicid$redirtopost");

    die;
  }

  //-------- Action: Lock topic

  if ($action == "locktopic")
  {
    $forumid = $_GET["forumid"];
    $topicid = $_GET["topicid"];
    $page = $_GET["page"];

    if (!is_valid_id($topicid) || get_user_class() < UC_MODERATOR)
      die;

    mysql_query("UPDATE topics SET locked='yes' WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    header("Location: $DEFAULTBASEURL/forums.php?action=viewforum&forumid=$forumid&page=$page");

    die;
  }

  //-------- Action: Unlock topic

  if ($action == "unlocktopic")
  {
    $forumid = $_GET["forumid"];

    $topicid = $_GET["topicid"];

    $page = $_GET["page"];

    if (!is_valid_id($topicid) || get_user_class() < UC_MODERATOR)
      die;

    mysql_query("UPDATE topics SET locked='no' WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    header("Location: $DEFAULTBASEURL/forums.php?action=viewforum&forumid=$forumid&page=$page");

    die;
  }

  //-------- Action: Set locked on/off

  if ($action == "setlocked")
  {
    $topicid = 0 + $HTTP_POST_VARS["topicid"];

    if (!$topicid || get_user_class() < UC_MODERATOR)
      die;

	$locked = sqlesc($HTTP_POST_VARS["locked"]);
    mysql_query("UPDATE topics SET locked=$locked WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    header("Location: $HTTP_POST_VARS[returnto]");

    die;
  }

  //-------- Action: Set sticky on/off

  if ($action == "setsticky")
  {
    $topicid = 0 + $HTTP_POST_VARS["topicid"];

    if (!topicid || get_user_class() < UC_MODERATOR)
      die;

	$sticky = sqlesc($HTTP_POST_VARS["sticky"]);
    mysql_query("UPDATE topics SET sticky=$sticky WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    header("Location: $HTTP_POST_VARS[returnto]");

    die;
  }

  //-------- Action: Rename topic

  if ($action == 'renametopic')
  {
  	if (get_user_class() < UC_MODERATOR)
  	  die;

  	$topicid = $HTTP_POST_VARS['topicid'];

  	if (!is_valid_id($topicid))
  	  die;

  	$subject = $HTTP_POST_VARS['subject'];

  	if ($subject == '')
  	  stderr('Error', 'You must enter a new title!');

  	$subject = sqlesc($subject);

  	mysql_query("UPDATE topics SET subject=$subject WHERE id=$topicid") or sqlerr();

  	$returnto = $HTTP_POST_VARS['returnto'];

  	if ($returnto)
  	  header("Location: $returnto");

  	die;
  }

  //-------- Action: View forum

  if ($action == "viewforum")
  {
    $forumid = $_GET["forumid"];

    if (!is_valid_id($forumid))
      die;

    $page = $_GET["page"];

    $userid = $CURUSER["id"];

    //------ Get forum name

    $res = mysql_query("SELECT name, minclassread FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res) or die;

    $forumname = $arr["name"];

    if (get_user_class() < $arr["minclassread"])
      die("Not permitted");

    //------ Page links

    //------ Get topic count

    $perpage = $CURUSER["topicsperpage"];
	if (!$perpage) $perpage = 20;

    $res = mysql_query("SELECT COUNT(*) FROM topics WHERE forumid=$forumid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $num = $arr[0];

    if ($page == 0)
      $page = 1;

    $first = ($page * $perpage) - $perpage + 1;

    $last = $first + $perpage - 1;

    if ($last > $num)
      $last = $num;

    $pages = floor($num / $perpage);

    if ($perpage * $pages < $num)
      ++$pages;

    //------ Build menu

    $menu = "<p align=center><b>\n";

    $lastspace = false;

    for ($i = 1; $i <= $pages; ++$i)
    {
    	if ($i == $page)
        $menu .= "<font class=gray>$i</font>\n";

      elseif ($i > 3 && ($i < $pages - 2) && ($page - $i > 3 || $i - $page > 3))
    	{
    		if ($lastspace)
    		  continue;

  		  $menu .= "... \n";

     		$lastspace = true;
    	}

      else
      {
        $menu .= "<a href=?action=viewforum&forumid=$forumid&page=$i>$i</a>\n";

        $lastspace = false;
      }
      if ($i < $pages)
        $menu .= "</b>|<b>\n";
    }

    $menu .= "<br>\n";

    if ($page == 1)
      $menu .= "<font class=gray>&lt;&lt; Prev</font>";

    else
      $menu .= "<a href=?action=viewforum&forumid=$forumid&page=" . ($page - 1) . ">&lt;&lt; Prev</a>";

    $menu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    if ($last == $num)
      $menu .= "<font class=gray>Next &gt;&gt;</font>";

    else
      $menu .= "<a href=?action=viewforum&forumid=$forumid&page=" . ($page + 1) . ">Next &gt;&gt;</a>";

    $menu .= "</b></p>\n";

    $offset = $first - 1;

    //------ Get topics data

    $topicsres = mysql_query("SELECT * FROM topics WHERE forumid=$forumid ORDER BY sticky, lastpost DESC LIMIT $offset,$perpage") or
      stderr("SQL Error", mysql_error());

    stdhead("Forum");

    $numtopics = mysql_num_rows($topicsres);

    print("<h1>$forumname</h1>\n");

    if ($numtopics > 0)
    {
      print($menu);

      print("<table border=1 cellspacing=0 cellpadding=5>");

      print("<tr><td class=colhead align=left>Topic</td><td class=colhead>Replies</td><td class=colhead>Views</td>\n" .
        "<td class=colhead align=left>Author</td><td class=colhead align=left>Last&nbsp;post</td>\n");

      print("</tr>\n");

      while ($topicarr = mysql_fetch_assoc($topicsres))
      {
        $topicid = $topicarr["id"];

        $topic_userid = $topicarr["userid"];

        $topic_views = $topicarr["views"];

		$views = number_format($topic_views);

        $locked = $topicarr["locked"] == "yes";

        $sticky = $topicarr["sticky"] == "yes";

        //---- Get reply count

        $res = mysql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

        $arr = mysql_fetch_row($res);

        $posts = $arr[0];

        $replies = max(0, $posts - 1);

        $tpages = floor($posts / $postsperpage);

        if ($tpages * $postsperpage != $posts)
          ++$tpages;

        if ($tpages > 1)
        {
          $topicpages = " (<img src=pic/multipage.gif>";

          for ($i = 1; $i <= $tpages; ++$i)
            $topicpages .= " <a href=?action=viewtopic&topicid=$topicid&page=$i>$i</a>";

          $topicpages .= ")";
        }
        else
          $topicpages = "";

        //---- Get userID and date of last post

        $res = mysql_query("SELECT * FROM posts WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

        $arr = mysql_fetch_assoc($res);

        $lppostid = 0 + $arr["id"];

        $lpuserid = 0 + $arr["userid"];

        $lpadded = "<nobr>" . $arr["added"] . "</nobr>";

        //------ Get name of last poster

        $res = mysql_query("SELECT * FROM users WHERE id=$lpuserid") or sqlerr(__FILE__, __LINE__);

        if (mysql_num_rows($res) == 1)
        {
          $arr = mysql_fetch_assoc($res);

          $lpusername = "<a href=userdetails.php?id=$lpuserid><b>$arr[username]</b></a>";
        }
        else
          $lpusername = "unknown[$topic_userid]";

        //------ Get author

        $res = mysql_query("SELECT username FROM users WHERE id=$topic_userid") or sqlerr(__FILE__, __LINE__);

        if (mysql_num_rows($res) == 1)
        {
          $arr = mysql_fetch_assoc($res);

          $lpauthor = "<a href=userdetails.php?id=$topic_userid><b>$arr[username]</b></a>";
        }
        else
          $lpauthor = "unknown[$topic_userid]";

        //---- Print row

        $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);

        $a = mysql_fetch_row($r);

        $new = !$a || $lppostid > $a[0];

        $topicpic = ($locked ? ($new ? "lockednew" : "locked") : ($new ? "unlockednew" : "unlocked"));

        $subject = ($sticky ? "Sticky: " : "") . "<a href=?action=viewtopic&topicid=$topicid><b>" .
        encodehtml($topicarr["subject"]) . "</b></a>$topicpages";

        print("<tr><td align=left><table border=0 cellspacing=0 cellpadding=0><tr>" .
        "<td class=embedded style='padding-right: 5px'><img src=pic/$topicpic.gif>" .
        "</td><td class=embedded align=left>\n" .
        "$subject</td></tr></table></td><td align=right>$replies</td>\n" .
        "<td align=right>$views</td><td align=left>$lpauthor</td>\n" .
        "<td align=left>$lpadded<br>by&nbsp;$lpusername</td>\n");

        print("</tr>\n");
      } // while

      print("</table>\n");

      print($menu);

    } // if
    else
      print("<p align=center>No topics found</p>\n");

    print("<p><table class=main border=0 cellspacing=0 cellpadding=0><tr valing=center>\n");

    print("<td class=embedded><img src=pic/unlockednew.gif style='margin-right: 5px'></td><td class=embedded>New posts</td>\n");

    print("<td class=embedded><img src=pic/locked.gif style='margin-left: 10px; margin-right: 5px'>" .
    "</td><td class=embedded>Locked topic</td>\n");

    print("</tr></table></p>\n");

    $arr = get_forum_access_levels($forumid) or die;

    $maypost = get_user_class() >= $arr["write"] && get_user_class() >= $arr["create"];

    if (!$maypost)
      print("<p><i>You are not permitted to start new topics in this forum.</i></p>\n");

    print("<p><table border=0 class=main cellspacing=0 cellpadding=0><tr>\n");

    print("<td class=embedded><form method=get action=?><input type=hidden " .
    "name=action value=viewunread><input type=submit value='View unread' class=btn></form></td>\n");

    if ($maypost)
      print("<td class=embedded><form method=get action=?><input type=hidden " .
      "name=action value=newtopic><input type=hidden name=forumid " .
      "value=$forumid><input type=submit value='New topic' class=btn style='margin-left: 10px'></form></td>\n");

    print("</tr></table></p>\n");

    insert_quick_jump_menu($forumid);

    stdfoot();

    die;
  }

  //-------- Action: View unread posts

  if ($action == "viewunread")
  {
	die("This feature is currently unavailable.");
    $userid = $CURUSER['id'];

    $maxresults = 25;

    $res = mysql_query("SELECT id, forumid, subject, lastpost FROM topics ORDER BY lastpost") or sqlerr(__FILE__, __LINE__);

    stdhead();

    print("<h1>Topics with unread posts</h1>\n");

    $n = 0;

    $uc = get_user_class();

    while ($arr = mysql_fetch_assoc($res))
    {
      $topicid = $arr['id'];

      $forumid = $arr['forumid'];

      //---- Check if post is read
      $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);

      $a = mysql_fetch_row($r);

      if ($a && $a[0] == $arr['lastpost'])
        continue;

      //---- Check access & get forum name
      $r = mysql_query("SELECT name, minclassread FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

      $a = mysql_fetch_assoc($r);

      if ($uc < $a['minclassread'])
        continue;

      ++$n;

      if ($n > $maxresults)
        break;

      $forumname = $a['name'];

      if ($n == 1)
      {
        print("<table border=1 cellspacing=0 cellpadding=5>\n");

        print("<tr><td class=colhead align=left>Topic</td><td class=colhead align=left>Forum</td></tr>\n");
      }

      print("<tr><td align=left><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>" .
      "<img src=pic/unlockednew.gif style='margin-right: 5px'></td><td class=embedded>" .
      "<a href=?action=viewtopic&topicid=$topicid&page=last#last><b>" . htmlspecialchars($arr["subject"]) .
      "</b></a></td></tr></table></td><td align=left><a href=?action=viewforum&amp;forumid=$forumid><b>$forumname</b></a></td></tr>\n");
    }
    if ($n > 0)
    {
      print("</table>\n");

      if ($n > $maxresults)
        print("<p>More than $maxresults items found, displaying first $maxresults.</p>\n");

      print("<p><a href=?catchup><b>Catch up</b></a></p>\n");
    }
    else
      print("<b>Nothing found</b>");

    stdfoot();

    die;
  }

if ($action == "search")
{
	stdhead("Forum Search");
	print("<h1>Forum Search (<font color=red>BETA</font>)</h1>\n");
	$keywords = trim($HTTP_GET_VARS["keywords"]);
	if ($keywords != "")
	{
		$perpage = 50;
		$page = max(1, 0 + $HTTP_GET_VARS["page"]);
		$ekeywords = sqlesc($keywords);
		print("<p><b>Searched for \"" . htmlspecialchars($keywords) . "\"</b></p>\n");
		$res = mysql_query("SELECT COUNT(*) FROM posts WHERE MATCH (body) AGAINST ($ekeywords)") or sqlerr(__FILE__, __LINE__);
		$arr = mysql_fetch_row($res);
		$hits = 0 + $arr[0];
		if ($hits == 0)
			print("<p><b>Sorry, nothing found!</b></p>");
		else
		{
			$pages = 0 + ceil($hits / $perpage);
			if ($page > $pages) $page = $pages;
			for ($i = 1; $i <= $pages; ++$i)
				if ($page == $i)
					$pagemenu1 .= "<font class=gray><b>$i</b></font>\n";
				else
					$pagemenu1 .= "<a href=\"/forums?action=search&amp;keywords=" . htmlspecialchars($keywords) . "&amp;page=$i\"><b>$i</b></a>\n";
			if ($page == 1)
				$pagemenu2 = "<font class=gray><b>&lt;&lt; Prev</b></font>\n";
			else
				$pagemenu2 = "<a href=\"/forums?action=search&amp;keywords=" . htmlspecialchars($keywords) . "&amp;page=" . ($page - 1) . "\"><b>&lt;&lt; Prev</b></a>\n";
			$pagemenu2 .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
			if ($page == $pages)
				$pagemenu2 .= "<font class=gray><b>Next &gt;&gt;</b></font>\n";
			else
				$pagemenu2 .= "<a href=\"/forums?action=search&amp;keywords=" . htmlspecialchars($keywords) . "&amp;page=" . ($page + 1) . "\"><b>Next &gt;&gt;</b></a>\n";
			$offset = ($page * $perpage) - $perpage;
			$res = mysql_query("SELECT id, topicid,userid,added FROM posts WHERE MATCH (body) AGAINST ($ekeywords) LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__);
			$num = mysql_num_rows($res);
			print("<p>$pagemenu1<br>$pagemenu2</p>");
			print("<table border=1 cellspacing=0 cellpadding=5>\n");
			print("<tr><td class=colhead>Post</td><td class=colhead align=left>Topic</td><td class=colhead align=left>Forum</td><td class=colhead align=left>Posted by</td></tr>\n");
			for ($i = 0; $i < $num; ++$i)
			{
				$post = mysql_fetch_assoc($res);
				$res2 = mysql_query("SELECT forumid, subject FROM topics WHERE id=$post[topicid]") or
					sqlerr(__FILE__, __LINE__);
				$topic = mysql_fetch_assoc($res2);
				$res2 = mysql_query("SELECT name,minclassread FROM forums WHERE id=$topic[forumid]") or
					sqlerr(__FILE__, __LINE__);
				$forum = mysql_fetch_assoc($res2);
				if ($forum["name"] == "" || $forum["minclassread"] > $CURUSER["class"])
				{
					--$hits;
					continue;
				}
				$res2 = mysql_query("SELECT username FROM users WHERE id=$post[userid]") or
					sqlerr(__FILE__, __LINE__);
				$user = mysql_fetch_assoc($res2);
				if ($user["username"] == "")
					$user["username"] = "[$post[userid]]";
				print("<tr><td>$post[id]</td><td align=left><a href=?action=viewtopic&amp;topicid=$post[topicid]&amp;page=p$post[id]#$post[id]><b>" . htmlspecialchars($topic["subject"]) . "</b></a></td><td align=left><a href=?action=viewforum&amp;forumid=$topic[forumid]><b>" . htmlspecialchars($forum["name"]) . "</b></a><td align=left><a href=userdetails.php?id=$post[userid]><b>$user[username]</b></a><br>at $post[added]</tr>\n");
			}
			print("</table>\n");
			print("<p>$pagemenu2<br>$pagemenu1</p>");
			print("<p>Found $hits post" . ($hits != 1 ? "s" : "") . ".</p>");
			print("<p><b>Search again</b></p>\n");
		}
	}
	print("<form method=get action=/forums?>\n");
	print("<input type=hidden name=action value=search>\n");
	print("<table border=1 cellspacing=0 cellpadding=5>\n");
	print("<tr><td class=rowhead>Key words</td><td align=left><input type=text size=55 name=keywords value=\"" . htmlspecialchars($keywords) .
		 "\"><br>\n" .
		"<font class=small size=-1>Enter one or more words to search for.<br>Very common words and words with less than 3 characters are ignored.</font></td></tr>\n");
	print("<tr><td align=center colspan=2><input type=submit value='Search' class=btn></td></tr>\n");
	print("</table>\n</form>\n");
	stdfoot();
	die;
}

  //-------- Handle unknown action

  if ($action != "")
    stderr("Forum Error", "Unknown action '$action'.");

  //-------- Default action: View forums

  if (isset($_GET["catchup"]))
    catch_up();

  //-------- Get forums

  $forums_res = mysql_query("SELECT * FROM forums ORDER BY sort, name") or sqlerr(__FILE__, __LINE__);

  stdhead("Forums");

  print("<h1>Forums</h1>\n");

  print("<table border=1 cellspacing=0 cellpadding=5>\n");

  print("<tr><td class=colhead align=left>Forum</td><td class=colhead align=right>Topics</td>" .
  "<td class=colhead align=right>Posts</td>" .
  "<td class=colhead align=left>Last post</td></tr>\n");

  while ($forums_arr = mysql_fetch_assoc($forums_res))
  {
    if (get_user_class() < $forums_arr["minclassread"])
      continue;

    $forumid = $forums_arr["id"];

    $forumname = htmlspecialchars($forums_arr["name"]);

    $forumdescription = htmlspecialchars($forums_arr["description"]);

    $topiccount = number_format($forums_arr["topiccount"]);

    $postcount = number_format($forums_arr["postcount"]);
/*
    while ($topicids_arr = mysql_fetch_assoc($topicids_res))
    {
      $topicid = $topicids_arr['id'];

      $postcount_res = mysql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

      $postcount_arr = mysql_fetch_row($postcount_res);

      $postcount += $postcount_arr[0];
    }

    $postcount = number_format($postcount);
*/
    // Find last post ID

    $lastpostid = get_forum_last_post($forumid);

    // Get last post info

    $post_res = mysql_query("SELECT added,topicid,userid FROM posts WHERE id=$lastpostid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($post_res) == 1)
    {
      $post_arr = mysql_fetch_assoc($post_res) or die("Bad forum last_post");

      $lastposterid = $post_arr["userid"];

      $lastpostdate = $post_arr["added"];

      $lasttopicid = $post_arr["topicid"];

      $user_res = mysql_query("SELECT username FROM users WHERE id=$lastposterid") or sqlerr(__FILE__, __LINE__);

      $user_arr = mysql_fetch_assoc($user_res);

      $lastposter = htmlspecialchars($user_arr['username']);

      $topic_res = mysql_query("SELECT subject FROM topics WHERE id=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      $topic_arr = mysql_fetch_assoc($topic_res);

      $lasttopic = htmlspecialchars($topic_arr['subject']);

      $lastpost = "<nobr>$lastpostdate<br>" .
      "by <a href=userdetails.php?id=$lastposterid><b>$lastposter</b></a><br>" .
      "in <a href=?action=viewtopic&topicid=$lasttopicid&amp;page=p$lastpostid#$lastpostid><b>$lasttopic</b></a></nobr>";

      $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=$CURUSER[id] AND topicid=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      $a = mysql_fetch_row($r);

      if ($a && $a[0] >= $lastpostid)
        $img = "unlocked";
      else
        $img = "unlockednew";
    }
    else
    {
      $lastpost = "N/A";
      $img = "unlocked";
    }
    print("<tr><td align=left><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded style='padding-right: 5px'><img src=".
    "pic/$img.gif></td><td class=embedded><a href=?action=viewforum&forumid=$forumid><b>$forumname</b></a><br>\n" .
    "$forumdescription</td></tr></table></td><td align=right>$topiccount</td></td><td align=right>$postcount</td>" .
    "<td align=left>$lastpost</td></tr>\n");
  }

  print("</table>\n");

  print("<p align=center><a href=?action=search><b>Search</b></a> | <a href=?action=viewunread><b>View unread</b></a> | <a href=?catchup><b>Catch up</b></a></p>");

  stdfoot();
?>