<?
require "include/bittorrent.php";

dbconn(false);

loggedinorreturn();

function bark($msg)
{
  stdhead();
  stdmsg("Error", $msg);
  stdfoot();
  exit;
}

function maketable($res)
{
  $ret = "<table class=main border=1 cellspacing=0 cellpadding=5>" .
    "<tr><td class=colhead align=center>Type</td><td class=colhead>Name</td><td class=colhead align=center>TTL</td><td class=colhead align=center>Size</td><td class=colhead align=right>Se.</td><td class=colhead align=right>Le.</td><td class=colhead align=center>Upl.</td>\n" .
    "<td class=colhead align=center>Downl.</td><td class=colhead align=center>Ratio</td></tr>\n";
  while ($arr = mysql_fetch_assoc($res))
  {
    if ($arr["downloaded"] > 0)
    {
      $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
      $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
    }
    else
      if ($arr["uploaded"] > 0)
        $ratio = "Inf.";
      else
        $ratio = "---";
	$catimage = htmlspecialchars($arr["image"]);
	$catname = htmlspecialchars($arr["catname"]);
	$ttl = (28*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($arr["added"])) / 3600);
	if ($ttl == 1) $ttl .= "<br>hour"; else $ttl .= "<br>hours";
	$size = str_replace(" ", "<br>", mksize($arr["size"]));
	$uploaded = str_replace(" ", "<br>", mksize($arr["uploaded"]));
	$downloaded = str_replace(" ", "<br>", mksize($arr["downloaded"]));
	$seeders = number_format($arr["seeders"]);
	$leechers = number_format($arr["leechers"]);
    $ret .= "<tr><td style='padding: 0px'><img src=\"pic/$catimage\" alt=\"$catname\" width=42 height=42></td>\n" .
		"<td><a href=details.php?id=$arr[torrent]&amp;hit=1><b>" . htmlspecialchars($arr["torrentname"]) .
		"</b></a></td><td align=center>$ttl</td><td align=center>$size</td><td align=right>$seeders</td><td align=right>$leechers</td><td align=center>$uploaded</td>\n" .
		"<td align=center>$downloaded</td><td align=center>$ratio</td></tr>\n";
  }
  $ret .= "</table>\n";
  return $ret;
}

$id = $_GET["id"];

if (!is_valid_id($id))
  bark("Bad ID $id.");

$r = @mysql_query("SELECT * FROM users WHERE id=$id") or sqlerr();
$user = mysql_fetch_array($r) or bark("No user with ID $id.");
if ($user["status"] == "pending") die;
$r = mysql_query("SELECT id, name, seeders, leechers, category FROM torrents WHERE owner=$id ORDER BY name") or sqlerr();
if (mysql_num_rows($r) > 0)
{
  $torrents = "<table class=main border=1 cellspacing=0 cellpadding=5>\n" .
    "<tr><td class=colhead>Type</td><td class=colhead>Name</td><td class=colhead>Seeders</td><td class=colhead>Leechers</td></tr>\n";
  while ($a = mysql_fetch_assoc($r))
  {
		$r2 = mysql_query("SELECT name, image FROM categories WHERE id=$a[category]") or sqlerr(__FILE__, __LINE__);
		$a2 = mysql_fetch_assoc($r2);
		$cat = "<img src=\"pic/$a2[image]\" alt=\"$a2[name]\">";
      $torrents .= "<tr><td style='padding: 0px'>$cat</td><td><a href=details.php?id=" . $a["id"] . "&hit=1><b>" . htmlspecialchars($a["name"]) . "</b></a></td>" .
        "<td align=right>$a[seeders]</td><td align=right>$a[leechers]</td></tr>\n";
  }
  $torrents .= "</table>";
}

if ($user["ip"] && (get_user_class() >= UC_MODERATOR || $user["id"] == $CURUSER["id"]))
{
  $ip = $user["ip"];
  $dom = @gethostbyaddr($user["ip"]);
  if ($dom == $user["ip"] || @gethostbyname($dom) != $user["ip"])
    $addr = $ip;
  else
  {
    $dom = strtoupper($dom);
    $domparts = explode(".", $dom);
    $domain = $domparts[count($domparts) - 2];
    if ($domain == "COM" || $domain == "CO" || $domain == "NET" || $domain == "NE" || $domain == "ORG" || $domain == "OR" )
      $l = 2;
    else
      $l = 1;
    $addr = "$ip ($dom)";
  }
}
if ($user[added] == "0000-00-00 00:00:00")
  $joindate = 'N/A';
else
  $joindate = "$user[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["added"])) . " ago)";
$lastseen = $user["last_access"];
if ($lastseen == "0000-00-00 00:00:00")
  $lastseen = "never";
else
{
  $lastseen .= " (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($lastseen)) . " ago)";
}
  $res = mysql_query("SELECT COUNT(*) FROM comments WHERE user=" . $user[id]) or sqlerr();
  $arr3 = mysql_fetch_row($res);
  $torrentcomments = $arr3[0];
  $res = mysql_query("SELECT COUNT(*) FROM posts WHERE userid=" . $user[id]) or sqlerr();
  $arr3 = mysql_fetch_row($res);
  $forumposts = $arr3[0];

//if ($user['donated'] > 0)
//  $don = "<img src=pic/starbig.gif>";

$res = mysql_query("SELECT name,flagpic FROM countries WHERE id=$user[country] LIMIT 1") or sqlerr();
if (mysql_num_rows($res) == 1)
{
  $arr = mysql_fetch_assoc($res);
  $country = "<td class=embedded><img src=\"pic/flag/$arr[flagpic]\" alt=\"$arr[name]\" style='margin-left: 8pt'></td>";
}

//if ($user["donor"] == "yes") $donor = "<td class=embedded><img src=pic/starbig.gif alt='Donor' style='margin-left: 4pt'></td>";
//if ($user["warned"] == "yes") $warned = "<td class=embedded><img src=pic/warnedbig.gif alt='Warned' style='margin-left: 4pt'></td>";

$res = mysql_query("SELECT torrent,added,uploaded,downloaded,torrents.name as torrentname,categories.name as catname,size,image,category,seeders,leechers FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id LEFT JOIN categories ON torrents.category = categories.id WHERE userid=$id AND seeder='no'") or sqlerr();
if (mysql_num_rows($res) > 0)
  $leeching = maketable($res);
$res = mysql_query("SELECT torrent,added,uploaded,downloaded,torrents.name as torrentname,categories.name as catname,size,image,category,seeders,leechers FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id LEFT JOIN categories ON torrents.category = categories.id WHERE userid=$id AND seeder='yes'") or sqlerr();
if (mysql_num_rows($res) > 0)
  $seeding = maketable($res);

stdhead("Details for " . $user["username"]);
$enabled = $user["enabled"] == 'yes';
print("<p><table class=main border=0 cellspacing=0 cellpadding=0>".
"<tr><td class=embedded><h1 style='margin:0px'>$user[username]" . get_user_icons($user, true) . "</h1></td>$country</tr></table></p>\n");

if (!$enabled)
  print("<p><b>This account has been disabled</b></p>\n");
elseif ($CURUSER["id"] <> $user["id"])
{
  $r = mysql_query("SELECT id FROM friends WHERE userid=$CURUSER[id] AND friendid=$id") or sqlerr(__FILE__, __LINE__);
  $friend = mysql_num_rows($r);
  $r = mysql_query("SELECT id FROM blocks WHERE userid=$CURUSER[id] AND blockid=$id") or sqlerr(__FILE__, __LINE__);
  $block = mysql_num_rows($r);

  if ($friend)
    print("<p>(<a href=friends.php?action=delete&type=friend&targetid=$id>remove from friends</a>)</p>\n");
  elseif($block)
    print("<p>(<a href=friends.php?action=delete&type=block&targetid=$id>remove from blocks</a>)</p>\n");
  else
  {
    print("<p>(<a href=friends.php?action=add&type=friend&targetid=$id>add to friends</a>)");
    print(" - (<a href=friends.php?action=add&type=block&targetid=$id>add to blocks</a>)</p>\n");
  }
}

begin_main_frame();
?>
<table width=100% border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead width=1%>Join&nbsp;date</td><td align=left width=99%><?=$joindate?></td></tr>
<tr><td class=rowhead>Last&nbsp;seen</td><td align=left><?=$lastseen?></td></tr>
<?
if (get_user_class() >= UC_MODERATOR)
  print("<tr><td class=rowhead>Email</td><td align=left><a href=mailto:$user[email]>$user[email]</a></td></tr>\n");
if ($addr)
  print("<tr><td class=rowhead>Address</td><td align=left>$addr</td></tr>\n");

//  if ($user["id"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR)
//	{
?>
<tr><td class=rowhead>Uploaded</td><td align=left><?=mksize($user["uploaded"])?></td></tr>
<tr><td class=rowhead>Downloaded</td><td align=left><?=mksize($user["downloaded"])?></td></tr>
<?
if ($user["downloaded"] > 0)
{
  $sr = $user["uploaded"] / $user["downloaded"];
  if ($sr >= 4)
    $s = "w00t";
  else if ($sr >= 2)
    $s = "grin";
  else if ($sr >= 1)
    $s = "smile1";
  else if ($sr >= 0.5)
    $s = "noexpression";
  else if ($sr >= 0.25)
    $s = "sad";
  else
    $s = "cry";
  $sr = floor($sr * 1000) / 1000;
  $sr = "<table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><font color=" . get_ratio_color($sr) . ">" . number_format($sr, 3) . "</font></td><td class=embedded>&nbsp;&nbsp;<img src=pic/smilies/$s.gif></td></tr></table>";
  print("<tr><td class=rowhead style='vertical-align: middle'>Share ratio</td><td align=left valign=center style='padding-top: 1px; padding-bottom: 0px'>$sr</td></tr>\n");
}
//}

//if ($user['donated'] > 0 && (get_user_class() >= UC_MODERATOR || $CURUSER["id"] == $user["id"]))
//  print("<tr><td class=rowhead>Donated</td><td align=left>$$user[donated]</td></tr>\n");
if ($user["avatar"])
	print("<tr><td class=rowhead>Avatar</td><td align=left><img src=\"" . htmlspecialchars($user["avatar"]) . "\"></td></tr>\n");
print("<tr><td class=rowhead>Class</td><td align=left>" . get_user_class_name($user["class"]) . "</td></tr>\n");
print("<tr><td class=rowhead>Torrent&nbsp;comments</td>");
if ($torrentcomments && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || get_user_class() >= UC_MODERATOR))
	print("<td align=left><a href=userhistory.php?action=viewcomments&id=$id>$torrentcomments</a></td></tr>\n");
else
	print("<td align=left>$torrentcomments</td></tr>\n");
print("<tr><td class=rowhead>Forum&nbsp;posts</td>");
if ($forumposts && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || get_user_class() >= UC_MODERATOR))
	print("<td align=left><a href=userhistory.php?action=viewposts&id=$id>$forumposts</a></td></tr>\n");
else
	print("<td align=left>$forumposts</td></tr>\n");

if ($torrents)
  print("<tr valign=top><td class=rowhead>Uploaded&nbsp;torrents</td><td align=left>$torrents</td></tr>\n");
if ($seeding)
  print("<tr valign=top><td class=rowhead>Currently&nbsp;seeding</td><td align=left>$seeding</td></tr>\n");
if ($leeching)
  print("<tr valign=top><td class=rowhead>Currently&nbsp;leeching</td><td align=left>$leeching</td></tr>\n");
if ($user["info"])
 print("<tr valign=top><td align=left colspan=2 class=text bgcolor=#F4F4F0>" . format_comment($user["info"]) . "</td></tr>\n");

if ($CURUSER["id"] != $user["id"])
	if (get_user_class() >= UC_MODERATOR)
  	$showpmbutton = 1;
	elseif ($user["acceptpms"] == "yes")
	{
		$r = mysql_query("SELECT id FROM blocks WHERE userid=$user[id] AND blockid=$CURUSER[id]") or sqlerr(__FILE__,__LINE__);
		$showpmbutton = (mysql_num_rows($r) == 1 ? 0 : 1);
	}
	elseif ($user["acceptpms"] == "friends")
	{
		$r = mysql_query("SELECT id FROM friends WHERE userid=$user[id] AND friendid=$CURUSER[id]") or sqlerr(__FILE__,__LINE__);
		$showpmbutton = (mysql_num_rows($r) == 1 ? 1 : 0);
	}
if ($showpmbutton)
	print("<tr><td colspan=2 align=center><form method=get action=sendmessage.php><input type=hidden name=receiver value=" .
		$user["id"] . "><input type=submit value=\"Send message\" style='height: 23px'></form></td></tr>");

print("</table>\n");

if (get_user_class() >= UC_MODERATOR && $user["class"] < get_user_class())
{
  begin_frame("Edit User", true);
  print("<form method=post action=modtask.php>\n");
  print("<input type=hidden name='action' value='edituser'>\n");
  print("<input type=hidden name='userid' value='$id'>\n");
  print("<input type=hidden name='returnto' value='userdetails.php?id=$id'>\n");
  print("<table class=main border=1 cellspacing=0 cellpadding=5>\n");
  print("<tr><td class=rowhead>Title</td><td colspan=2 align=left><input type=text size=60 name=title value=\"" . htmlspecialchars($user[title]) . "\"></tr>\n");
	$avatar = htmlspecialchars($user["avatar"]);
  print("<tr><td class=rowhead>Avatar&nbsp;URL</td><td colspan=2 align=left><input type=text size=60 name=avatar value=\"$avatar\"></tr>\n");
	// we do not want mods to be able to change user classes or amount donated...
	if ($CURUSER["class"] < UC_ADMINISTRATOR)
	  print("<input type=hidden name=donor value=$user[donor]>\n");
	else
	{
	  print("<tr><td class=rowhead>Donor</td><td colspan=2 align=left><input type=radio name=donor value=yes" .($user["donor"] == "yes" ? " checked" : "").">Yes <input type=radio name=donor value=no" .($user["donor"] == "no" ? " checked" : "").">No</td></tr>\n");
	}

	if (get_user_class() == UC_MODERATOR && $user["class"] > UC_VIP)
	  printf("<input type=hidden name=class value=$user[class]\n");
	else
	{
	  print("<tr><td class=rowhead>Class</td><td colspan=2 align=left><select name=class>\n");
	  if (get_user_class() == UC_MODERATOR)
	    $maxclass = UC_VIP;
	  else
	    $maxclass = get_user_class() - 1;
	  for ($i = 0; $i <= $maxclass; ++$i)
	    print("<option value=$i" . ($user["class"] == $i ? " selected" : "") . ">$prefix" . get_user_class_name($i) . "\n");
	  print("</select></td></tr>\n");
	}

	$modcomment = htmlspecialchars($user["modcomment"]);
	print("<tr><td class=rowhead>Comment</td><td colspan=2 align=left><textarea cols=60 rows=6 name=modcomment>$modcomment</textarea></td></tr>\n");
	$warned = $user["warned"] == "yes";

 	print("<tr><td class=rowhead" . (!$warned ? " rowspan=2": "") . ">Warned</td>
 	<td align=left width=20%>" .
  ( $warned
  ? "<input name=warned value='yes' type=radio checked>Yes<input name=warned value='no' type=radio>No"
 	: "No" ) ."</td>");

	if ($warned)
	{
		$warneduntil = $user['warneduntil'];
		if ($warneduntil == '0000-00-00 00:00:00')
    	print("<td align=center>(arbitrary duration)</td></tr>\n");
		else
		{
    	print("<td align=center>Until $warneduntil");
	    print(" (" . mkprettytime(strtotime($warneduntil) - gmtime()) . " to go)</td></tr>\n");
 	  }
  }
  else
  {
    print("<td>Warn for <select name=warnlength>\n");
    print("<option value=0>------</option>\n");
    print("<option value=1>1 week</option>\n");
    print("<option value=2>2 weeks</option>\n");
    print("<option value=4>4 weeks</option>\n");
    print("<option value=8>8 weeks</option>\n");
    print("<option value=255>Unlimited</option>\n");
    print("</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PM comment:</td></tr>\n");
    print("<tr><td colspan=2 align=left><input type=text size=60 name=warnpm></td></tr>");
  }
  print("<tr><td class=rowhead>Enabled</td><td colspan=2 align=left><input name=enabled value='yes' type=radio" . ($enabled ? " checked" : "") . ">Yes <input name=enabled value='no' type=radio" . (!$enabled ? " checked" : "") . ">No</td></tr>\n");
  print("<tr><td class=rowhead>Passkey</td><td colspan=2 align=left><input name=resetkey value=1 type=checkbox> Reset passkey</td></tr>n");
  print("</td></tr>");
  print("<tr><td colspan=3 align=center><input type=submit class=btn value='Okay'></td></tr>\n");
  print("</table>\n");
  print("</form>\n");
  end_frame();
}
end_main_frame();
stdfoot();

?>
