<?php
  require "include/bittorrent.php";
  dbconn(false);
  loggedinorreturn();

  $action = $_GET["action"];
  $pollid = $_GET["pollid"];
  $returnto = $_GET["returnto"];

  if ($action == "delete")
  {
  	if (get_user_class() < UC_MODERATOR)
  		stderr("Error", "Permission denied.");

  	if (!is_valid_id($pollid))
			stderr("Error", "Invalid ID.");

   	$sure = $_GET["sure"];
   	if (!$sure)
    	stderr("Delete poll","Do you really want to delete a poll? Click\n" .
    		"<a href=?action=delete&pollid=$pollid&returnto=$returnto&sure=1>here</a> if you are sure.");

		mysql_query("DELETE FROM pollanswers WHERE pollid = $pollid") or sqlerr();
		mysql_query("DELETE FROM polls WHERE id = $pollid") or sqlerr();
		if ($returnto == "main")
			header("Location: $DEFAULTBASEURL");
		else
			header("Location: $DEFAULTBASEURL/polls.php?deleted=1");
		die;
  }

  $rows = mysql_query("SELECT COUNT(*) FROM polls") or sqlerr();
  $row = mysql_fetch_row($rows);
  $pollcount = $row[0];
  if ($pollcount == 0)
  	stderr("Sorry...", "There are no polls!");
  $polls = mysql_query("SELECT * FROM polls ORDER BY id DESC LIMIT 1," . ($pollcount - 1 )) or sqlerr();
  stdhead("Previous polls");
  print("<h1>Previous polls</h1>");

    function srt($a,$b)
    {
      if ($a[0] > $b[0]) return -1;
      if ($a[0] < $b[0]) return 1;
      return 0;
    }

  while ($poll = mysql_fetch_assoc($polls))
  {
    $o = array($poll["option0"], $poll["option1"], $poll["option2"], $poll["option3"], $poll["option4"],
    $poll["option5"], $poll["option6"], $poll["option7"], $poll["option8"], $poll["option9"],
    $poll["option10"], $poll["option11"], $poll["option12"], $poll["option13"], $poll["option14"],
    $poll["option15"], $poll["option16"], $poll["option17"], $poll["option18"], $poll["option19"]);

    print("<p><table width=750 border=1 cellspacing=0 cellpadding=10><tr><td align=center>\n");

    print("<p class=sub>");
    $added = gmdate("Y-m-d",strtotime($poll['added'])) . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($poll["added"]))) . " ago)";

    print("$added");

    if (get_user_class() >= UC_ADMINISTRATOR)
    {
    	print(" - [<a href=makepoll.php?action=edit&pollid=$poll[id]><b>Edit</b></a>]\n");
			print(" - [<a href=?action=delete&pollid=$poll[id]><b>Delete</b></a>]\n");
		}

		print("<a name=$poll[id]>");

		print("</p>\n");

    print("<table class=main border=1 cellspacing=0 cellpadding=5><tr><td class=text>\n");

    print("<p align=center><b>" . $poll["question"] . "</b></p>");

    $pollanswers = mysql_query("SELECT selection FROM pollanswers WHERE pollid=" . $poll["id"] . " AND  selection < 20") or sqlerr();

    $tvotes = mysql_num_rows($pollanswers);

    $vs = array(); // count for each option ([0]..[19])
    $os = array(); // votes and options: array(array(123, "Option 1"), array(45, "Option 2"))

    // Count votes
    while ($pollanswer = mysql_fetch_row($pollanswers))
      $vs[$pollanswer[0]] += 1;

    reset($o);
    for ($i = 0; $i < count($o); ++$i)
      if ($o[$i])
        $os[$i] = array($vs[$i], $o[$i]);

    // now os is an array like this:
    if ($poll["sort"] == "yes")
    	usort($os, srt);

    print("<table width=100% class=main border=0 cellspacing=0 cellpadding=0>\n");
    $i = 0;
    while ($a = $os[$i])
    {
	  	if ($tvotes > 0)
	  		$p = round($a[0] / $tvotes * 100);
	  	else
				$p = 0;
      if ($i % 2)
        $c = "";
      else
        $c = " bgcolor=#ECE9D8";
      print("<tr><td class=embedded$c>" . $a[1] . "&nbsp;&nbsp;</td><td class=embedded$c>" .
        "<img src=pic/bar_left.gif><img src=pic/bar.gif height=9 width=" . ($p * 3) . "><img src=pic/bar_right.gif> $p%</td></tr>\n");
      ++$i;
    }
    print("</table>\n");
	$tvotes = number_format($tvotes);
    print("<p align=center>Votes: $tvotes</p>\n");

    print("</td></tr></table>\n");

    print("</td></tr></table></p>\n");

  }

  stdfoot();
?>