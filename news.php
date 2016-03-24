<?php

require "include/bittorrent.php";

dbconn();
loggedinorreturn();

if (get_user_class() < UC_ADMINISTRATOR)
	stderr("Error", "Permission denied.");

$action = $_GET["action"];

//   Delete News Item    //////////////////////////////////////////////////////

if ($action == 'delete')
{
	$newsid = $_GET["newsid"];
  if (!is_valid_id($newsid))
  	stderr("Error","Invalid news item ID - Code 1.");

  $returnto = $_GET["returnto"];

  $sure = $_GET["sure"];
  if (!$sure)
    stderr("Delete news item","Do you really want to delete a news item? Click\n" .
    	"<a href=?action=delete&newsid=$newsid&returnto=$returnto&sure=1>here</a> if you are sure.");

  mysql_query("DELETE FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

	if ($returnto != "")
		header("Location: $returnto");
	else
		$warning = "News item was deleted successfully.";
}

//   Add News Item    /////////////////////////////////////////////////////////

if ($action == 'add')
{

	$body = $_POST["body"];
	if (!$body)
		stderr("Error","The news item cannot be empty!");

	$added = $_POST["added"];
	if (!$added)
		$added = sqlesc(get_date_time());

  mysql_query("INSERT INTO news (userid, added, body) VALUES (".
  	$CURUSER['id'] . ", $added, " . sqlesc($body) . ")") or sqlerr(__FILE__, __LINE__);
	if (mysql_affected_rows() == 1)
		$warning = "News item was added successfully.";
	else
		stderr("Error","Something weird just happened.");
}

//   Edit News Item    ////////////////////////////////////////////////////////

if ($action == 'edit')
{

	$newsid = $_GET["newsid"];

  if (!is_valid_id($newsid))
  	stderr("Error","Invalid news item ID - Code 2.");

  $res = mysql_query("SELECT * FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) != 1)
	  stderr("Error", "No news item with ID $newsid.");

	$arr = mysql_fetch_array($res);

  if ($_SERVER['REQUEST_METHOD'] == 'POST')
  {
  	$body = $_POST['body'];

    if ($body == "")
    	stderr("Error", "Body cannot be empty!");

    $body = sqlesc($body);

    $editedat = sqlesc(get_date_time());

    mysql_query("UPDATE news SET body=$body WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

    $returnto = $_POST['returnto'];

		if ($returnto != "")
			header("Location: $returnto");
		else
			$warning = "News item was edited successfully.";
  }
  else
  {
 	 	$returnto = $_GET['returnto'];
	  stdhead();
	  print("<h1>Edit News Item</h1>\n");
	  print("<form method=post action=?action=edit&newsid=$newsid>\n");
	  print("<table border=1 cellspacing=0 cellpadding=5>\n");
	  print("<tr><td><input type=hidden name=returnto value=$returnto></td></tr>\n");
	  print("<tr><td style='padding: 0px'><textarea name=body cols=145 rows=5 style='border: 0px'>" . htmlspecialchars($arr["body"]) . "</textarea></td></tr>\n");
	  print("<tr><td align=center><input type=submit value='Okay' class=btn></td></tr>\n");
	  print("</table>\n");
	  print("</form>\n");
	  stdfoot();
	  die;
  }
}

//   Other Actions and followup    ////////////////////////////////////////////

stdhead("Site news");
print("<h1>Submit News Item</h1>\n");
if ($warning)
	print("<p><font size=-3>($warning)</font></p>");
print("<form method=post action=?action=add>\n");
print("<table border=1 cellspacing=0 cellpadding=5>\n");
print("<tr><td style='padding: 10px'><textarea name=body cols=141 rows=5 style='border: 0px'></textarea>\n");
print("<br><br><div align=center><input type=submit value='Okay' class=btn></div></td></tr>\n");
print("</table></form><br><br>\n");

$res = mysql_query("SELECT * FROM news ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($res) > 0)
{


 	begin_main_frame();
	begin_frame();

	while ($arr = mysql_fetch_array($res))
	{
		$newsid = $arr["id"];
		$body = $arr["body"];
	  $userid = $arr["userid"];
	  $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

    $res2 = mysql_query("SELECT username, donor FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
    $arr2 = mysql_fetch_array($res2);

    $postername = $arr2["username"];

    if ($postername == "")
    	$by = "unknown[$userid]";
    else
    	$by = "<a href=userdetails.php?id=$userid><b>$postername</b></a>" .
    		($arr2["donor"] == "yes" ? "<img src=pic/star.gif alt='Donor'>" : "");

	  print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>");
    print("$added&nbsp;---&nbsp;by&nbsp$by");
    print(" - [<a href=?action=edit&newsid=$newsid><b>Edit</b></a>]");
    print(" - [<a href=?action=delete&newsid=$newsid><b>Delete</b></a>]");
    print("</td></tr></table></p>\n");

	  begin_table(true);
	  print("<tr valign=top><td class=comment>$body</td></tr>\n");
	  end_table();
	}
	end_frame();
	end_main_frame();
}
else
  stdmsg("Sorry", "No news available!");
stdfoot();
die;
?>