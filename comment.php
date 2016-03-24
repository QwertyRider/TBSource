<?

require_once("include/bittorrent.php");

hit_start();

$action = $_GET["action"];

dbconn(false);

hit_count();

loggedinorreturn();

if ($action == "add")
{
  if ($_SERVER["REQUEST_METHOD"] == "POST")
  {
    $torrentid = 0 + $_POST["tid"];
	  if (!is_valid_id($torrentid))
			stderr("Error", "Invalid ID $torrentid.");

		$res = mysql_query("SELECT name FROM torrents WHERE id = $torrentid") or sqlerr(__FILE__,__LINE__);
		$arr = mysql_fetch_array($res);
		if (!$arr)
		  stderr("Error", "No torrent with ID $torrentid.");

	  $text = trim($_POST["text"]);
	  if (!$text)
			stderr("Error", "Comment body cannot be empty!");

	  mysql_query("INSERT INTO comments (user, torrent, added, text, ori_text) VALUES (" .
	      $CURUSER["id"] . ",$torrentid, '" . get_date_time() . "', " . sqlesc($text) .
	       "," . sqlesc($text) . ")");

	  $newid = mysql_insert_id();

	  mysql_query("UPDATE torrents SET comments = comments + 1 WHERE id = $torrentid");

	  header("Refresh: 0; url=details.php?id=$torrentid&viewcomm=$newid#comm$newid");

    hit_end();
	  die;
	}

  $torrentid = 0 + $_GET["tid"];
  if (!is_valid_id($torrentid))
		stderr("Error", "Invalid ID $torrentid.");

	$res = mysql_query("SELECT name FROM torrents WHERE id = $torrentid") or sqlerr(__FILE__,__LINE__);
	$arr = mysql_fetch_array($res);
	if (!$arr)
	  stderr("Error", "No torrent with ID $torrentid.");

	stdhead("Add a comment to \"" . $arr["name"] . "\"");

	print("<h1>Add a comment to \"" . htmlspecialchars($arr["name"]) . "\"</h1>\n");
	print("<p><form method=\"post\" action=\"comment.php?action=add\">\n");
	print("<input type=\"hidden\" name=\"tid\" value=\"$torrentid\"/>\n");
	print("<textarea name=\"text\" rows=\"10\" cols=\"60\"></textarea></p>\n");
	print("<p><input type=\"submit\" class=btn value=\"Do it!\" /></p></form>\n");

	$res = mysql_query("SELECT comments.id, text, comments.added, username, users.id as user, users.avatar FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = $torrentid ORDER BY comments.id DESC LIMIT 5");

	$allrows = array();
	while ($row = mysql_fetch_array($res))
	  $allrows[] = $row;

	if (count($allrows)) {
	  print("<h2>Most recent comments, in reverse order</h2>\n");
	  commenttable($allrows);
	}

  stdfoot();
  hit_end();
	die;
}
elseif ($action == "edit")
{
  $commentid = 0 + $_GET["cid"];
  if (!is_valid_id($commentid))
		stderr("Error", "Invalid ID $commentid.");

  $res = mysql_query("SELECT c.*, t.name FROM comments AS c JOIN torrents AS t ON c.torrent = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
  $arr = mysql_fetch_array($res);
  if (!$arr)
  	stderr("Error", "Invalid ID $commentid.");

	if ($arr["user"] != $CURUSER["id"] && get_user_class() < UC_MODERATOR)
		stderr("Error", "Permission denied.");

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
	  $text = $_POST["text"];
    $returnto = $_POST["returnto"];

	  if ($text == "")
	  	stderr("Error", "Comment body cannot be empty!");

	  $text = sqlesc($text);

	  $editedat = sqlesc(get_date_time());

	  mysql_query("UPDATE comments SET text=$text, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$commentid") or sqlerr(__FILE__, __LINE__);

		if ($returnto)
	  	header("Location: $returnto");
		else
		  header("Location: $DEFAULTBASEURL/");      // change later ----------------------

  	hit_end();
		die;
	}

 	stdhead("Edit comment to \"" . $arr["name"] . "\"");

	print("<h1>Edit comment to \"" . htmlspecialchars($arr["name"]) . "\"</h1><p>\n");
	print("<form method=\"post\" action=\"comment.php?action=edit&amp;cid=$commentid\">\n");
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . $_SERVER["HTTP_REFERER"] . "\" />\n");
	print("<input type=\"hidden\" name=\"cid\" value=\"$commentid\" />\n");
	print("<textarea name=\"text\" rows=\"10\" cols=\"60\">" . htmlspecialchars($arr["text"]) . "</textarea></p>\n");
	print("<p><input type=\"submit\" class=btn value=\"Do it!\" /></p></form>\n");

	stdfoot();
  hit_end();
	die;
}
elseif ($action == "delete")
{
	if (get_user_class() < UC_MODERATOR)
		stderr("Error", "Permission denied.");

  $commentid = 0 + $_GET["cid"];

  if (!is_valid_id($commentid))
		stderr("Error", "Invalid ID $commentid.");

  $sure = $_GET["sure"];

  if (!$sure)
  {
 		$referer = $_SERVER["HTTP_REFERER"];
		stderr("Delete comment", "You are about to delete a comment. Click\n" .
			"<a href=?action=delete&cid=$commentid&sure=1" .
			($referer ? "&returnto=" . urlencode($referer) : "") .
			">here</a> if you are sure.");
  }


	$res = mysql_query("SELECT torrent FROM comments WHERE id=$commentid")  or sqlerr(__FILE__,__LINE__);
	$arr = mysql_fetch_array($res);
	if ($arr)
		$torrentid = $arr["torrent"];

	mysql_query("DELETE FROM comments WHERE id=$commentid") or sqlerr(__FILE__,__LINE__);
	if ($torrentid && mysql_affected_rows() > 0)
		mysql_query("UPDATE torrents SET comments = comments - 1 WHERE id = $torrentid");

	$returnto = $_GET["returnto"];

	if ($returnto)
	  header("Location: $returnto");
	else
	  header("Location: $DEFAULTBASEURL/");      // change later ----------------------

  hit_end();
	die;
}
elseif ($action == "vieworiginal")
{
	if (get_user_class() < UC_MODERATOR)
		stderr("Error", "Permission denied.");

  $commentid = 0 + $_GET["cid"];

  if (!is_valid_id($commentid))
		stderr("Error", "Invalid ID $commentid.");

  $res = mysql_query("SELECT c.*, t.name FROM comments AS c JOIN torrents AS t ON c.torrent = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
  $arr = mysql_fetch_array($res);
  if (!$arr)
  	stderr("Error", "Invalid ID $commentid.");

  stdhead("Original comment");
  print("<h1>Original contents of comment #$commentid</h1><p>\n");
	print("<table width=500 border=1 cellspacing=0 cellpadding=5>");
  print("<tr><td class=comment>\n");
	echo htmlspecialchars($arr["ori_text"]);
  print("</td></tr></table>\n");

  $returnto = $_SERVER["HTTP_REFERER"];

//	$returnto = "details.php?id=$torrentid&amp;viewcomm=$commentid#$commentid";

	if ($returnto)
 		print("<p><font size=small>(<a href=$returnto>back</a>)</font></p>\n");

	stdfoot();
  hit_end();
	die;
}
else
	stderr("Error", "Unknown action $action");

die;
?>