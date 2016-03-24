<?

ob_start("ob_gzhandler");

require_once("include/bittorrent.php");

hit_start();

function getagent($httpagent)
{
	if (preg_match("/^Azureus ([0-9]+\\.[0-9]+\\.[0-9]+\\.[0-9]+)/", $httpagent, $matches))
		return "Azureus/$matches[1]";
	elseif (preg_match("/BitTorrent\\/S-([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
		return "Shadow's/$matches[1]";
	elseif (preg_match("/BitTorrent\\/U-([0-9]+\\.[0-9]+\\.[0-9]+)/", $httpagent, $matches))
		return "UPnP/$matches[1]";
	elseif (preg_match("/^BitTorrent\\/T-(.+)$/", $httpagent, $matches))
		return "BitTornado/$matches[1]";
	elseif (preg_match("/^BitTorrent\\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
		return "BitTorrent/$matches[1]";
	elseif (preg_match("/^Python-urllib\\/.+?, BitTorrent\\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
		return "BitTorrent/$matches[1]";
	elseif (ereg("^BitTorrent\\/BitSpirit$", $httpagent))
		return "BitSpirit";
	elseif (preg_match("/^BitTorrent\\/brst(.+)/", $httpagent, $matches))
		return "Burst/$matches[1]";
	elseif (preg_match("/^RAZA (.+)$/", $httpagent, $matches))
		return "Shareaza/$matches[1]";
	else
		return "---";
}

function dltable($name, $arr, $torrent)
{

	global $CURUSER;
	$s = "<b>" . count($arr) . " $name</b>\n";
	if (!count($arr))
		return $s;
	$s .= "\n";
	$s .= "<table width=100% class=main border=1 cellspacing=0 cellpadding=5>\n";
	$s .= "<tr><td class=colhead>User/IP</td>" .
          "<td class=colhead align=center>Connectable</td>".
          "<td class=colhead align=right>Uploaded</td>".
          "<td class=colhead align=right>Rate</td>".
          "<td class=colhead align=right>Downloaded</td>" .
          "<td class=colhead align=right>Rate</td>" .
          "<td class=colhead align=right>Ratio</td>" .
          "<td class=colhead align=right>Complete</td>" .
          "<td class=colhead align=right>Connected</td>" .
          "<td class=colhead align=right>Idle</td>" .
          "<td class=colhead align=left>Client</td></tr>\n";
	$now = time();
	$moderator = (isset($CURUSER) && get_user_class() >= UC_MODERATOR);
$mod = get_user_class() >= UC_MODERATOR;
	foreach ($arr as $e) {


                // user/ip/port
                // check if anyone has this ip
                ($unr = mysql_query("SELECT username, privacy FROM users WHERE id=$e[userid] ORDER BY last_access DESC LIMIT 1")) or die;
                $una = mysql_fetch_array($unr);
				if ($una["privacy"] == "strong") continue;
		$s .= "<tr>\n";
                if ($una["username"])
                  $s .= "<td><a href=userdetails.php?id=$e[userid]><b>$una[username]</b></a></td>\n";
                else
                  $s .= "<td>" . ($mod ? $e["ip"] : preg_replace('/\.\d+$/', ".xxx", $e["ip"])) . "</td>\n";
		$secs = max(1, ($now - $e["st"]) - ($now - $e["la"]));
		$revived = $e["revived"] == "yes";
        $s .= "<td align=center>" . ($e[connectable] == "yes" ? "Yes" : "<font color=red>No</font>") . "</td>\n";
		$s .= "<td align=right>" . mksize($e["uploaded"]) . "</td>\n";
		$s .= "<td align=right><nobr>" . mksize(($e["uploaded"] - $e["uploadoffset"]) / $secs) . "/s</nobr></td>\n";
		$s .= "<td align=right>" . mksize($e["downloaded"]) . "</td>\n";
		if ($e["seeder"] == "no")
			$s .= "<td align=right><nobr>" . mksize(($e["downloaded"] - $e["downloadoffset"]) / $secs) . "/s</nobr></td>\n";
		else
			$s .= "<td align=right><nobr>" . mksize(($e["downloaded"] - $e["downloadoffset"]) / max(1, $e["finishedat"] - $e[st])) .	"/s</nobr></td>\n";
                if ($e["downloaded"])
				{
                  $ratio = floor(($e["uploaded"] / $e["downloaded"]) * 1000) / 1000;
                    $s .= "<td align=\"right\"><font color=" . get_ratio_color($ratio) . ">" . number_format($ratio, 3) . "</font></td>\n";
				}
	               else
                  if ($e["uploaded"])
                    $s .= "<td align=right>Inf.</td>\n";
                  else
                    $s .= "<td align=right>---</td>\n";
		$s .= "<td align=right>" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</td>\n";
		$s .= "<td align=right>" . mkprettytime($now - $e["st"]) . "</td>\n";
		$s .= "<td align=right>" . mkprettytime($now - $e["la"]) . "</td>\n";
		$s .= "<td align=left>" . htmlspecialchars(getagent($e["agent"])) . "</td>\n";
		$s .= "</tr>\n";
	}
	$s .= "</table>\n";
	return $s;
}

dbconn(false);

loggedinorreturn();

hit_count();

$id = $_GET["id"];
$id = 0 + $id;
if (!isset($id) || !$id)
	die();

$res = mysql_query("SELECT torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, LENGTH(torrents.nfo) AS nfosz, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(torrents.last_action) AS lastseed, torrents.numratings, torrents.name, IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.numfiles, categories.name AS cat_name, users.username FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id")
	or sqlerr();
$row = mysql_fetch_array($res);

$owned = $moderator = 0;
	if (get_user_class() >= UC_MODERATOR)
		$owned = $moderator = 1;
	elseif ($CURUSER["id"] == $row["owner"])
		$owned = 1;
//}

if (!$row || ($row["banned"] == "yes" && !$moderator))
	stderr("Error", "No torrent with ID $id.");
else {
	if ($_GET["hit"]) {
		mysql_query("UPDATE torrents SET views = views + 1 WHERE id = $id");
		if ($_GET["tocomm"])
			header("Location: $DEFAULTBASEURL/details.php?id=$id&page=0#startcomments");
		elseif ($_GET["filelist"])
			header("Location: $DEFAULTBASEURL/details.php?id=$id&filelist=1#filelist");
		elseif ($_GET["toseeders"])
			header("Location: $DEFAULTBASEURL/details.php?id=$id&dllist=1#seeders");
		elseif ($_GET["todlers"])
			header("Location: $DEFAULTBASEURL/details.php?id=$id&dllist=1#leechers");
		else
			header("Location: $DEFAULTBASEURL/details.php?id=$id");
		hit_end();
		exit();
	}

	if (!isset($_GET["page"])) {
		stdhead("Details for torrent \"" . $row["name"] . "\"");

		if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
			$owned = 1;
		else
			$owned = 0;

		$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		if ($_GET["uploaded"]) {
			print("<h2>Successfully uploaded!</h2>\n");
			print("<p>You can start seeding now. <b>Note</b> that the torrent won't be visible until you do that!</p>\n");
		}
		elseif ($_GET["edited"]) {
			print("<h2>Successfully edited!</h2>\n");
			if (isset($_GET["returnto"]))
				print("<p><b>Go back to <a href=\"" . htmlspecialchars($_GET["returnto"]) . "\">whence you came</a>.</b></p>\n");
		}
		elseif (isset($_GET["searched"])) {
			print("<h2>Your search for \"" . htmlspecialchars($_GET["searched"]) . "\" gave a single result:</h2>\n");
		}
		elseif ($_GET["rated"])
			print("<h2>Rating added!</h2>\n");

$s=$row["name"];
		print("<h1>$s</h1>\n");
                print("<table width=750 border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");

		$url = "edit.php?id=" . $row["id"];
		if (isset($_GET["returnto"])) {
			$addthis = "&amp;returnto=" . urlencode($_GET["returnto"]);
			$url .= $addthis;
			$keepget .= $addthis;
		}
		$editlink = "a href=\"$url\" class=\"sublink\"";

//		$s = "<b>" . htmlspecialchars($row["name"]) . "</b>";
//		if ($owned)
//			$s .= " $spacer<$editlink>[Edit torrent]</a>";
//		tr("Name", $s, 1);

		print("<tr><td class=rowhead width=1%>Download</td><td width=99% align=left><a class=\"index\" href=\"download.php/$id/" . rawurlencode($row["filename"]) . "\">" . htmlspecialchars($row["filename"]) . "</a></td></tr>");
//		tr("Downloads&nbsp;as", $row["save_as"]);

		function hex_esc($matches) {
			return sprintf("%02x", ord($matches[0]));
		}
		tr("Info hash", preg_replace_callback('/./s', "hex_esc", hash_pad($row["info_hash"])));

		if (!empty($row["descr"]))
			tr("Description", str_replace(array("\n", "  "), array("<br>\n", "&nbsp; "), format_urls(htmlspecialchars($row["descr"]))), 1);
if (get_user_class() >= UC_POWER_USER && $row["nfosz"] > 0)
  print("<tr><td class=rowhead>NFO</td><td align=left><a href=viewnfo.php?id=$row[id]><b>View NFO</b></a> (" .
     mksize($row["nfosz"]) . ")</td></tr>\n");
		if ($row["visible"] == "no")
			tr("Visible", "<b>no</b> (dead)", 1);
		if ($moderator)
			tr("Banned", $row["banned"]);

		if (isset($row["cat_name"]))
			tr("Type", $row["cat_name"]);
		else
			tr("Type", "(none selected)");

		tr("Last&nbsp;seeder", "Last activity " . mkprettytime($row["lastseed"]) . " ago");
		tr("Size",mksize($row["size"]) . " (" . number_format($row["size"]) . " bytes)");
/*
		$s = "";
		$s .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td valign=\"top\" class=embedded>";
		if (!isset($row["rating"])) {
			if ($minvotes > 1) {
				$s .= "none yet (needs at least $minvotes votes and has got ";
				if ($row["numratings"])
					$s .= "only " . $row["numratings"];
				else
					$s .= "none";
				$s .= ")";
			}
			else
				$s .= "No votes yet";
		}
		else {
			$rpic = ratingpic($row["rating"]);
			if (!isset($rpic))
				$s .= "invalid?";
			else
				$s .= "$rpic (" . $row["rating"] . " out of 5 with " . $row["numratings"] . " vote(s) total)";
		}
		$s .= "\n";
		$s .= "</td><td class=embedded>$spacer</td><td valign=\"top\" class=embedded>";
		if (!isset($CURUSER))
			$s .= "(<a href=\"login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;nowarn=1\">Log in</a> to rate it)";
		else {
			$ratings = array(
					5 => "Kewl!",
					4 => "Pretty good",
					3 => "Decent",
					2 => "Pretty bad",
					1 => "Sucks!",
			);
			if (!$owned || $moderator) {
				$xres = mysql_query("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
				$xrow = mysql_fetch_array($xres);
				if ($xrow)
					$s .= "(you rated this torrent as \"" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "\")";
				else {
					$s .= "<form method=\"post\" action=\"takerate.php\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
					$s .= "<select name=\"rating\">\n";
					$s .= "<option value=\"0\">(add rating)</option>\n";
					foreach ($ratings as $k => $v) {
						$s .= "<option value=\"$k\">$k - $v</option>\n";
					}
					$s .= "</select>\n";
					$s .= "<input type=\"submit\" value=\"Vote!\" />";
					$s .= "</form>\n";
				}
			}
		}
		$s .= "</td></tr></table>";
		tr("Rating", $s, 1);

*/

		tr("Added", $row["added"]);
		tr("Views", $row["views"]);
		tr("Hits", $row["hits"]);
		tr("Snatched", $row["times_completed"] . " time(s)");

		$keepget = "";
		$uprow = (isset($row["username"]) ? ("<a href=userdetails.php?id=" . $row["owner"] . "><b>" . htmlspecialchars($row["username"]) . "</b></a>") : "<i>unknown</i>");
		if ($owned)
			$uprow .= " $spacer<$editlink><b>[Edit this torrent]</b></a>";
		tr("Upped by", $uprow, 1);

		if ($row["type"] == "multi") {
			if (!$_GET["filelist"])
				tr("Num files<br /><a href=\"details.php?id=$id&amp;filelist=1$keepget#filelist\" class=\"sublink\">[See full list]</a>", $row["numfiles"] . " files", 1);
			else {
				tr("Num files", $row["numfiles"] . " files", 1);

				$s = "<table class=main border=\"1\" cellspacing=0 cellpadding=\"5\">\n";

				$subres = mysql_query("SELECT * FROM files WHERE torrent = $id ORDER BY id");
$s.="<tr><td class=colhead>Path</td><td class=colhead align=right>Size</td></tr>\n";
				while ($subrow = mysql_fetch_array($subres)) {
					$s .= "<tr><td>" . $subrow["filename"] .
                            "</td><td align=\"right\">" . mksize($subrow["size"]) . "</td></tr>\n";
				}

				$s .= "</table>\n";
				tr("<a name=\"filelist\">File list</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[Hide list]</a>", $s, 1);
			}
		}

		if (!$_GET["dllist"]) {
			/*
			$subres = mysql_query("SELECT seeder, COUNT(*) FROM peers WHERE torrent = $id GROUP BY seeder");
			$resarr = array(yes => 0, no => 0);
			$sum = 0;
			while ($subrow = mysql_fetch_array($subres)) {
				$resarr[$subrow[0]] = $subrow[1];
				$sum += $subrow[1];
			}
			tr("Peers<br /><a href=\"details.php?id=$id&amp;dllist=1$keepget#seeders\" class=\"sublink\">[See full list]</a>", $resarr["yes"] . " seeder(s), " . $resarr["no"] . " leecher(s) = $sum peer(s) total", 1);
			*/
			tr("Peers<br /><a href=\"details.php?id=$id&amp;dllist=1$keepget#seeders\" class=\"sublink\">[See full list]</a>", $row["seeders"] . " seeder(s), " . $row["leechers"] . " leecher(s) = " . ($row["seeders"] + $row["leechers"]) . " peer(s) total", 1);
		}
		else {
			$downloaders = array();
			$seeders = array();
			$subres = mysql_query("SELECT seeder, finishedat, downloadoffset, uploadoffset, ip, port, uploaded, downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable, agent, UNIX_TIMESTAMP(last_action) AS la, userid FROM peers WHERE torrent = $id") or sqlerr();
			while ($subrow = mysql_fetch_array($subres)) {
				if ($subrow["seeder"] == "yes")
					$seeders[] = $subrow;
				else
					$downloaders[] = $subrow;
			}

			function leech_sort($a,$b) {
                                if ( isset( $_GET["usort"] ) ) return seed_sort($a,$b);				
                                $x = $a["to_go"];
				$y = $b["to_go"];
				if ($x == $y)
					return 0;
				if ($x < $y)
					return -1;
				return 1;
			}
			function seed_sort($a,$b) {
				$x = $a["uploaded"];
				$y = $b["uploaded"];
				if ($x == $y)
					return 0;
				if ($x < $y)
					return 1;
				return -1;
			}

			usort($seeders, "seed_sort");
			usort($downloaders, "leech_sort");

			tr("<a name=\"seeders\">Seeders</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[Hide list]</a>", dltable("Seeder(s)", $seeders, $row), 1);
			tr("<a name=\"leechers\">Leechers</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[Hide list]</a>", dltable("Leecher(s)", $downloaders, $row), 1);
		}

		print("</table></p>\n");
	}
	else {
		stdhead("Comments for torrent \"" . $row["name"] . "\"");
		print("<h1>Comments for <a href=details.php?id=$id>" . $row["name"] . "</a></h1>\n");
//		print("<p><a href=\"details.php?id=$id\">Back to full details</a></p>\n");
	}

	print("<p><a name=\"startcomments\"></a></p>\n");

	$commentbar = "<p align=center><a class=index href=comment.php?action=add&amp;tid=$id>Add a comment</a></p>\n";

	$subres = mysql_query("SELECT COUNT(*) FROM comments WHERE torrent = $id");
	$subrow = mysql_fetch_array($subres);
	$count = $subrow[0];

	if (!$count) {
		print("<h2>No comments yet</h2>\n");
	}
	else {
		list($pagertop, $pagerbottom, $limit) = pager(20, $count, "details.php?id=$id&", array(lastpagedefault => 1));

		$subres = mysql_query("SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, warned, ".
                  "username, title, class, donor FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = " .
                  "$id ORDER BY comments.id $limit") or sqlerr(__FILE__, __LINE__);
		$allrows = array();
		while ($subrow = mysql_fetch_array($subres))
			$allrows[] = $subrow;

		print($commentbar);
		print($pagertop);

		commenttable($allrows);

		print($pagerbottom);
	}

	print($commentbar);
}

stdfoot();

hit_end();

?>
