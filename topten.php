<?php

	ob_start("ob_gzhandler");

  require "include/bittorrent.php";
  dbconn(false);
  loggedinorreturn();

/*
  function donortable($res, $frame_caption)
  {
    begin_frame($frame_caption, true);
    begin_table();
?>
<tr>
<td class=colhead>Rank</td>
<td class=colhead align=left>User</td>
<td class=colhead align=right>Donated</td>
</tr>
<?
    $num = 0;
    while ($a = mysql_fetch_assoc($res))
    {
        ++$num;
		$this = $a["donated"];
		if ($this == $last)
			$rank = "";
		else
		{
		  $rank = $num;
		}
	if ($rank && $num > 10)
    	break;
      print("<tr><td>$rank</td><td align=left><a href=userdetails.php?id=$a[id]><b>$a[username]" .
         "</b></a></td><td align=right>$$this</td></tr>");
		$last = $this;
    }
    end_table();
    end_frame();
  }
*/

  function usertable($res, $frame_caption)
  {
  	global $CURUSER;
    begin_frame($frame_caption, true);
    begin_table();
?>
<tr>
<td class=colhead>Rank</td>
<td class=colhead align=left>User</td>
<td class=colhead>Uploaded</td>
<td class=colhead align=left>UL speed</td>
<td class=colhead>Downloaded</td>
<td class=colhead align=left>DL speed</td>
<td class=colhead align=right>Ratio</td>
<td class=colhead align=left>Joined</td>

</tr>
<?
    $num = 0;
    while ($a = mysql_fetch_assoc($res))
    {
      ++$num;
      $highlight = $CURUSER["id"] == $a["userid"] ? " bgcolor=#BBAF9B" : "";
      if ($a["downloaded"])
      {
        $ratio = $a["uploaded"] / $a["downloaded"];
        $color = get_ratio_color($ratio);
        $ratio = number_format($ratio, 2);
        if ($color)
          $ratio = "<font color=$color>$ratio</font>";
      }
      else
        $ratio = "Inf.";
      print("<tr$highlight><td align=center>$num</td><td align=left$highlight><a href=userdetails.php?id=" .
      		$a["userid"] . "><b>" . $a["username"] . "</b>" .
      		"</td><td align=right$highlight>" . mksize($a["uploaded"]) .
					"</td><td align=right$highlight>" . mksize($a["upspeed"]) . "/s" .
         	"</td><td align=right$highlight>" . mksize($a["downloaded"]) .
      		"</td><td align=right$highlight>" . mksize($a["downspeed"]) . "/s" .
      		"</td><td align=right$highlight>" . $ratio .
      		"</td><td align=left>" . gmdate("Y-m-d",strtotime($a["added"])) . " (" .
      		get_elapsed_time(sql_timestamp_to_unix_timestamp($a["added"])) . " ago)</td></tr>");
    }
    end_table();
    end_frame();
  }

  function _torrenttable($res, $frame_caption)
  {
    begin_frame($frame_caption, true);
    begin_table();
?>
<tr>
<td class=colhead align=center>Rank</td>
<td class=colhead align=left>Name</td>
<td class=colhead align=right>Sna.</td>
<td class=colhead align=right>Data</td>
<td class=colhead align=right>Se.</td>
<td class=colhead align=right>Le.</td>
<td class=colhead align=right>To.</td>
<td class=colhead align=right>Ratio</td>
</tr>
<?
    $num = 0;
    while ($a = mysql_fetch_assoc($res))
    {
      ++$num;
      if ($a["leechers"])
      {
        $r = $a["seeders"] / $a["leechers"];
        $ratio = "<font color=" . get_ratio_color($r) . ">" . number_format($r, 2) . "</font>";
      }
      else
        $ratio = "Inf.";
      print("<tr><td align=center>$num</td><td align=left><a href=details.php?id=" . $a["id"] . "&hit=1><b>" .
        $a["name"] . "</b></a></td><td align=right>" . number_format($a["times_completed"]) .
				"</td><td align=right>" . mksize($a["data"]) . "</td><td align=right>" . number_format($a["seeders"]) .
        "</td><td align=right>" . number_format($a["leechers"]) . "</td><td align=right>" . ($a["leechers"] + $a["seeders"]) .
        "</td><td align=right>$ratio</td>\n");
    }
    end_table();
    end_frame();
  }

  function countriestable($res, $frame_caption, $what)
  {
    global $CURUSER;
    begin_frame($frame_caption, true);
    begin_table();
?>
<tr>
<td class=colhead>Rank</td>
<td class=colhead align=left>Country</td>
<td class=colhead align=right><?=$what?></td>
</tr>
<?
  	$num = 0;
		while ($a = mysql_fetch_assoc($res))
		{
	    ++$num;
	    if ($what == "Users")
	      $value = number_format($a["num"]);
	    elseif ($what == "Uploaded")
	      $value = mksize($a["ul"]);
	    elseif ($what == "Average")
	    	$value = mksize($a["ul_avg"]);
 	    elseif ($what == "Ratio")
 	    	$value = number_format($a["r"],2);
	    print("<tr><td align=center>$num</td><td align=left><table border=0 class=main cellspacing=0 cellpadding=0><tr><td class=embedded>".
	      "<img align=center src=pic/flag/$a[flagpic]></td><td class=embedded style='padding-left: 5px'><b>$a[name]</b></td>".
	      "</tr></table></td><td align=right>$value</td></tr>\n");
	  }
    end_table();
    end_frame();
  }

  function peerstable($res, $frame_caption)
  {
    begin_frame($frame_caption, true);
    begin_table();

		print("<tr><td class=colhead>Rank</td><td class=colhead>Username</td><td class=colhead>Upload rate</td><td class=colhead>Download rate</td></tr>");

		$n = 1;
		while ($arr = mysql_fetch_assoc($res))
		{
      $highlight = $CURUSER["id"] == $arr["userid"] ? " bgcolor=#BBAF9B" : "";
			print("<tr><td$highlight>$n</td><td$highlight><a href=userdetails.php?id=" . $arr["userid"] . "><b>" . $arr["username"] . "</b></td><td$highlight>" . mksize($arr["uprate"]) . "/s</td><td$highlight>" . mksize($arr["downrate"]) . "/s</td></tr>\n");
			++$n;
		}

    end_table();
    end_frame();
  }

  stdhead("Top 10");
  begin_main_frame();
//  $r = mysql_query("SELECT * FROM users ORDER BY donated DESC, username LIMIT 100") or die;
//  donortable($r, "Top 10 Donors");
	$type = 0 + $_GET["type"];
	if (!in_array($type,array(1,2,3,4)))
		$type = 1;
	$limit = 0 + $_GET["lim"];
	$subtype = $_GET["subtype"];

	print("<p align=center>"  .
		($type == 1 && !$limit ? "<b>Users</b>" : "<a href=topten.php?type=1>Users</a>") .	" | " .
 		($type == 2 && !$limit ? "<b>Torrents</b>" : "<a href=topten.php?type=2>Torrents</a>") . " | " .
		($type == 3 && !$limit ? "<b>Countries</b>" : "<a href=topten.php?type=3>Countries</a>") . " | " .
		($type == 4 && !$limit ? "<b>Peers</b>" : "<a href=topten.php?type=4>Peers</a>") . "</p>\n");

	$pu = get_user_class() >= UC_POWER_USER;

  if (!$pu)
  	$limit = 10;

  if ($type == 1)
  {
    $mainquery = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed FROM users WHERE enabled = 'yes'";

  	if (!$limit || $limit > 250)
  		$limit = 10;

  	if ($limit == 10 || $subtype == "ul")
  	{
			$order = "uploaded DESC";
			$r = mysql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
	  	usertable($r, "Top $limit Uploaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&amp;lim=100&amp;subtype=ul>Top 100</a>] - [<a href=topten.php?type=1&amp;lim=250&amp;subtype=ul>Top 250</a>]</font>" : ""));
	  }

    if ($limit == 10 || $subtype == "dl")
  	{
			$order = "downloaded DESC";
		  $r = mysql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		  usertable($r, "Top $limit Downloaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&amp;lim=100&amp;subtype=dl>Top 100</a>] - [<a href=topten.php?type=1&amp;lim=250&amp;subtype=dl>Top 250</a>]</font>" : ""));
	  }

    if ($limit == 10 || $subtype == "uls")
  	{
			$order = "upspeed DESC";
			$r = mysql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
	  	usertable($r, "Top $limit Fastest Uploaders <font class=small>(average, includes inactive time)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&amp;lim=100&amp;subtype=uls>Top 100</a>] - [<a href=topten.php?type=1&amp;lim=250&amp;subtype=uls>Top 250</a>]</font>" : ""));
	  }

    if ($limit == 10 || $subtype == "dls")
  	{
			$order = "downspeed DESC";
			$r = mysql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
	  	usertable($r, "Top $limit Fastest Downloaders <font class=small>(average, includes inactive time)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&amp;lim=100&amp;subtype=dls>Top 100</a>] - [<a href=topten.php?type=1&amp;lim=250&amp;subtype=dls>Top 250</a>]</font>" : ""));
	  }

    if ($limit == 10 || $subtype == "bsh")
  	{
			$order = "uploaded / downloaded DESC";
			$extrawhere = " AND downloaded > 1073741824";
	  	$r = mysql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
	  	usertable($r, "Top $limit Best Sharers <font class=small>(with minimum 1 GB downloaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&amp;lim=100&amp;subtype=bsh>Top 100</a>] - [<a href=topten.php?type=1&amp;lim=250&amp;subtype=bsh>Top 250</a>]</font>" : ""));
		}

    if ($limit == 10 || $subtype == "wsh")
  	{
			$order = "uploaded / downloaded ASC, downloaded DESC";
  		$extrawhere = " AND downloaded > 1073741824";
	  	$r = mysql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
	  	usertable($r, "Top $limit Worst Sharers <font class=small>(with minimum 1 GB downloaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&amp;lim=100&amp;subtype=wsh>Top 100</a>] - [<a href=topten.php?type=1&amp;lim=250&amp;subtype=wsh>Top 250</a>]</font>" : ""));
	  }
  }

  elseif ($type == 2)
  {
   	if (!$limit || $limit > 50)
  		$limit = 10;

   	if ($limit == 10 || $subtype == "act")
  	{
		  $r = mysql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT $limit") or sqlerr();
		  _torrenttable($r, "Top $limit Most Active Torrents" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=2&amp;lim=25&amp;subtype=act>Top 25</a>] - [<a href=topten.php?type=2&amp;lim=50&amp;subtype=act>Top 50</a>]</font>" : ""));
	  }

   	if ($limit == 10 || $subtype == "sna")
   	{
	  	$r = mysql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY times_completed DESC LIMIT $limit") or sqlerr();
		  _torrenttable($r, "Top $limit Most Snatched Torrents" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=2&amp;lim=25&amp;subtype=sna>Top 25</a>] - [<a href=topten.php?type=2&amp;lim=50&amp;subtype=sna>Top 50</a>]</font>" : ""));
	  }

   	if ($limit == 10 || $subtype == "mdt")
   	{
		  $r = mysql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND leechers >= 5 AND times_completed > 0 GROUP BY t.id ORDER BY data DESC, added ASC LIMIT $limit") or sqlerr();
		  _torrenttable($r, "Top $limit Most Data Transferred Torrents" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=2&amp;lim=25&amp;subtype=mdt>Top 25</a>] - [<a href=topten.php?type=2&amp;lim=50&amp;subtype=mdt>Top 50</a>]</font>" : ""));
		}

   	if ($limit == 10 || $subtype == "bse")
   	{
		  $r = mysql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND seeders >= 5 GROUP BY t.id ORDER BY seeders / leechers DESC, seeders DESC, added ASC LIMIT $limit") or sqlerr();
	  	_torrenttable($r, "Top $limit Best Seeded Torrents <font class=small>(with minimum 5 seeders)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=2&amp;lim=25&amp;subtype=bse>Top 25</a>] - [<a href=topten.php?type=2&amp;lim=50&amp;subtype=bse>Top 50</a>]</font>" : ""));
    }

   	if ($limit == 10 || $subtype == "wse")
   	{
		  $r = mysql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND leechers >= 5 AND times_completed > 0 GROUP BY t.id ORDER BY seeders / leechers ASC, leechers DESC LIMIT $limit") or sqlerr();
		  _torrenttable($r, "Top $limit Worst Seeded Torrents <font class=small>(with minimum 5 leechers, excluding unsnatched torrents)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=2&amp;lim=25&amp;subtype=wse>Top 25</a>] - [<a href=topten.php?type=2&amp;lim=50&amp;subtype=wse>Top 50</a>]</font>" : ""));
		}
  }
  elseif ($type == 3)
  {
  	if (!$limit || $limit > 25)
  		$limit = 10;

   	if ($limit == 10 || $subtype == "us")
   	{
		  $r = mysql_query("SELECT name, flagpic, COUNT(users.country) as num FROM countries, users WHERE users.country = countries.id GROUP BY name ORDER BY num DESC LIMIT $limit") or sqlerr();
		  countriestable($r, "Top $limit Countries<font class=small> (users)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=3&amp;lim=25&amp;subtype=us>Top 25</a>]</font>" : ""),"Users");
    }

   	if ($limit == 10 || $subtype == "ul")
   	{
	  	$r = mysql_query("SELECT c.name, c.flagpic, sum(u.uploaded) AS ul FROM users u, countries c WHERE u.country = c.id AND u.enabled = 'yes' GROUP BY c.name ORDER BY ul DESC LIMIT $limit") or sqlerr();
		  countriestable($r, "Top $limit Countries<font class=small> (total uploaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=3&amp;lim=25&amp;subtype=ul>Top 25</a>]</font>" : ""),"Uploaded");
    }

		if ($limit == 10 || $subtype == "avg")
		{
		  $r = mysql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/count(u.id) AS ul_avg FROM users u, countries c WHERE u.country = c.id AND u.enabled = 'yes' GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776 AND count(u.id) >= 100 ORDER BY ul_avg DESC LIMIT $limit") or sqlerr();
		  countriestable($r, "Top $limit Countries<font class=small> (average total uploaded per user, with minimum 1TB uploaded and 100 users)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=3&amp;lim=25&amp;subtype=avg>Top 25</a>]</font>" : ""),"Average");
    }

		if ($limit == 10 || $subtype == "r")
		{
		  $r = mysql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/sum(u.downloaded) AS r FROM users u, countries c WHERE u.country = c.id AND u.enabled = 'yes' GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776 AND sum(u.downloaded) > 1099511627776 AND count(u.id) >= 100 ORDER BY r DESC LIMIT $limit") or sqlerr();
		  countriestable($r, "Top $limit Countries<font class=small> (ratio, with minimum 1TB uploaded, 1TB downloaded and 100 users)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=3&amp;lim=25&amp;subtype=r>Top 25</a>]</font>" : ""),"Ratio");
	  }
  }
	elseif ($type == 4)
	{
//		print("<h1 align=center><font color=red>Under construction!</font></h1>\n");
  	if (!$limit || $limit > 250)
  		$limit = 10;

	    if ($limit == 10 || $subtype == "ul")
  		{
//				$r = mysql_query("SELECT users.id AS userid, peers.id AS peerid, username, peers.uploaded, peers.downloaded, peers.uploaded / (UNIX_TIMESTAMP(NOW()) - (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_action)) - UNIX_TIMESTAMP(started)) AS uprate, peers.downloaded / (UNIX_TIMESTAMP(NOW()) - (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_action)) - UNIX_TIMESTAMP(started)) AS downrate FROM peers JOIN users ON peers.userid = users.id ORDER BY uprate DESC LIMIT $limit") or sqlerr();
//				peerstable($r, "Top $limit Fastest Uploaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=4&amp;lim=100&amp;subtype=ul>Top 100</a>] - [<a href=topten.php?type=4&amp;lim=250&amp;subtype=ul>Top 250</a>]</font>" : ""));

//				$r = mysql_query("SELECT users.id AS userid, peers.id AS peerid, username, peers.uploaded, peers.downloaded, (peers.uploaded - peers.uploadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS uprate, (peers.downloaded - peers.downloadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS downrate FROM peers JOIN users ON peers.userid = users.id ORDER BY uprate DESC LIMIT $limit") or sqlerr();
//				peerstable($r, "Top $limit Fastest Uploaders (timeout corrected)" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=4&amp;lim=100&amp;subtype=ul>Top 100</a>] - [<a href=topten.php?type=4&amp;lim=250&amp;subtype=ul>Top 250</a>]</font>" : ""));

				$r = mysql_query( "SELECT users.id AS userid, username, (peers.uploaded - peers.uploadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS uprate, IF(seeder = 'yes',(peers.downloaded - peers.downloadoffset)  / (finishedat - UNIX_TIMESTAMP(started)),(peers.downloaded - peers.downloadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started))) AS downrate FROM peers , users WHERE peers.userid = users.id ORDER BY uprate DESC LIMIT $limit") or sqlerr();
				peerstable($r, "Top $limit Fastest Uploaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=4&amp;lim=100&amp;subtype=ul>Top 100</a>] - [<a href=topten.php?type=4&amp;lim=250&amp;subtype=ul>Top 250</a>]</font>" : ""));
	  	}

	    if ($limit == 10 || $subtype == "dl")
  		{
//				$r = mysql_query("SELECT users.id AS userid, peers.id AS peerid, username, peers.uploaded, peers.downloaded, (peers.uploaded - peers.uploadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS uprate, (peers.downloaded - peers.downloadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS downrate FROM peers JOIN users ON peers.userid = users.id ORDER BY downrate DESC LIMIT $limit") or sqlerr();
//				peerstable($r, "Top $limit Fastest Downloaders (timeout corrected)" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=4&amp;lim=100&amp;subtype=dl>Top 100</a>] - [<a href=topten.php?type=4&amp;lim=250&amp;subtype=dl>Top 250</a>]</font>" : ""));

				$r = mysql_query("SELECT users.id AS userid, peers.id AS peerid, username, peers.uploaded, peers.downloaded,(peers.uploaded - peers.uploadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS uprate, IF(seeder = 'yes',(peers.downloaded - peers.downloadoffset)  / (finishedat - UNIX_TIMESTAMP(started)),(peers.downloaded - peers.downloadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started))) AS downrate FROM peers, users WHERE peers.userid = users.id ORDER BY downrate DESC LIMIT $limit") or sqlerr();
				peerstable($r, "Top $limit Fastest Downloaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=4&amp;lim=100&amp;subtype=dl>Top 100</a>] - [<a href=topten.php?type=4&amp;lim=250&amp;subtype=dl>Top 250</a>]</font>" : ""));
	  	}
	}
  end_main_frame();
  print("<p><font class=small>Started recording account xfer stats on 2003-08-31</font></p>");
  stdfoot();
?>


