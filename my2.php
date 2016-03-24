<?

require_once("include/bittorrent.php");

hit_start();

dbconn(false);

hit_count();

loggedinorreturn();
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location IN ('in', 'both')") or print(mysql_error());
$arr = mysql_fetch_row($res);
$messages = $arr[0];
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location IN ('in', 'both') AND unread='yes'") or print(mysql_error());
$arr = mysql_fetch_row($res);
$unread = $arr[0];
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE sender=" . $CURUSER["id"] . " AND location IN ('out', 'both')") or print(mysql_error());
$arr = mysql_fetch_row($res);
$outmessages = $arr[0];


stdhead($CURUSER["username"] . "'s private page", false);

if ($_GET["edited"]) {
	print("<h1>Profile updated!</h1>\n");
	if ($_GET["mailsent"])
		print("<h2>Confirmation email has been sent!</h2>\n");
}
elseif ($_GET["emailch"])
	print("<h1>Email address changed!</h1>\n");
else
	print("<h1>Welcome, <a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a>!</h1>\n");

?>
<table border="1" cellspacing="0" cellpadding="10" align="center">
<tr>
<td align="center" width="33%"><a href=logout.php><b>Logout</b></a></td>
<td align="center" width="33%"><a href=mytorrents.php><b>My torrents</b></a></td>
<td align="center" width="33%"><a href=friends.php><b>My users lists</b></a></td>
</tr>
<tr>
<td colspan="3">
<form method="post" action="takeprofedit.php">
<table border="1" cellspacing=0 cellpadding="5" width="100%">
<?

/***********************

$res = mysql_query("SELECT COUNT(*) FROM ratings WHERE user=" . $CURUSER["id"]);
$row = mysql_fetch_array($res);
tr("Ratings submitted", $row[0]);

$res = mysql_query("SELECT COUNT(*) FROM comments WHERE user=" . $CURUSER["id"]);
$row = mysql_fetch_array($res);
tr("Written comments", $row[0]);

****************/

$ss_r = mysql_query("SELECT * from stylesheets") or die;
$ss_sa = array();
while ($ss_a = mysql_fetch_array($ss_r))
{
  $ss_id = $ss_a["id"];
  $ss_name = $ss_a["name"];
  $ss_sa[$ss_name] = $ss_id;
}
ksort($ss_sa);
reset($ss_sa);
while (list($ss_name, $ss_id) = each($ss_sa))
{
  if ($ss_id == $CURUSER["stylesheet"]) $ss = " selected"; else $ss = "";
  $stylesheets .= "<option value=$ss_id$ss>$ss_name</option>\n";
}

$countries = "<option value=0>---- None selected ----</option>\n";
$ct_r = mysql_query("SELECT id,name FROM countries ORDER BY name") or die;
while ($ct_a = mysql_fetch_array($ct_r))
  $countries .= "<option value=$ct_a[id]" . ($CURUSER["country"] == $ct_a['id'] ? " selected" : "") . ">$ct_a[name]</option>\n";

function format_tz($a)
{
	$h = floor($a);
	$m = ($a - floor($a)) * 60;
	return ($a >= 0?"+":"-") . (strlen(abs($h)) > 1?"":"0") . abs($h) .
		":" . ($m==0?"00":$m);
}

tr("Accept PMs",
"<input type=radio name=acceptpms" . ($CURUSER["acceptpms"] == "yes" ? " checked" : "") . " value=yes>All (except blocks)
<input type=radio name=acceptpms" .  ($CURUSER["acceptpms"] == "friends" ? " checked" : "") . " value=friends>Friends only
<input type=radio name=acceptpms" .  ($CURUSER["acceptpms"] == "no" ? " checked" : "") . " value=no>Staff only"
,1);



tr("Delete PMs", "<input type=checkbox name=deletepms" . ($CURUSER["deletepms"] == "yes" ? " checked" : "") . "> (Default value for \"Delete PM on reply\")",1);
tr("Save PMs", "<input type=checkbox name=savepms" . ($CURUSER["savepms"] == "yes" ? " checked" : "") . "> (Default value for \"Save PM to Sentbox\")",1);

$r = mysql_query("SELECT id,name FROM categories ORDER BY name") or sqlerr();
//$categories = "Default browsing categories:<br>\n";
if (mysql_num_rows($r) > 0)
{
	$categories .= "<table><tr>\n";
	$i = 0;
	while ($a = mysql_fetch_assoc($r))
	{
	  $categories .=  ($i && $i % 2 == 0) ? "</tr><tr>" : "";
	  $categories .= "<td class=bottom style='padding-right: 5px'><input name=cat$a[id] type=\"checkbox\" " . (strpos($CURUSER['notifs'], "[cat$a[id]]") !== false ? " checked" : "") . " value='yes'>&nbsp;" . htmlspecialchars($a["name"]) . "</td>\n";
	  ++$i;
	}
	$categories .= "</tr></table>\n";
}

tr("Email notification", "<input type=checkbox name=pmnotif" . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked" : "") . " value=yes> Notify me when I have received a PM<br>\n" .
	 "<input type=checkbox name=emailnotif" . (strpos($CURUSER['notifs'], "[email]") !== false ? " checked" : "") . " value=yes> Notify me when a torrent is uploaded in one of <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; my default browsing categories.\n"
   , 1);
tr("Browse default<br>categories",$categories,1);
tr("Stylesheet", "<select name=stylesheet>\n$stylesheets\n</select>",1);
tr("Country", "<select name=country>\n$countries\n</select>",1);
tr("My avatar", "URL: <input name=avatar size=45 value=\"" . htmlspecialchars($CURUSER["avatar"]) .
  "\"><br>\n<font class=small size=1>Width should be 150 pixels (will be resized if necessary).\n<br>If you need a host for the picture, try the <a href=bitbucket-upload.php>bit-bucket</a>.</font><br><input type=checkbox name=avataroffensive value='yes'>This avatar may be offensive to some people<br><font class=small size=1>Please check this box if your avatar depicts nudity, or may<br>otherwise be potentially offensive or unsuitable for minors.</font>",1);
tr("Show avatars", "<input type=radio name=avatars" . ($CURUSER["avatars"] == "yes" ? " checked" : "") . "> All<br><input type=radio name=avatars" . ($CURUSER["avatars"] == "yes" ? " checked" : "") . "> All except potentially offensive<br><input type=radio name=avatars" . ($CURUSER["avatars"] == "yes" ? " checked" : "") . "> None<br>",1);
tr("Torrents per page", "<input type=text size=10 name=torrentsperpage value=$CURUSER[torrentsperpage]> (0=use default setting)",1);
tr("Topics per page", "<input type=text size=10 name=topicsperpage value=$CURUSER[topicsperpage]> (0=use default setting)",1);
tr("Posts per page", "<input type=text size=10 name=postsperpage value=$CURUSER[postsperpage]> (0=use default setting)",1);
tr("Info", "<textarea name=info cols=50 rows=4>" . $CURUSER["info"] . "</textarea><br>Displayed on your public page. May contain <a href=tags.php target=_new>BB codes</a>.", 1);
tr("Email address", "<input type=\"text\" name=\"email\" size=50 value=\"" . htmlspecialchars($CURUSER["email"]) . "\" />", 1);
print("<tr><td colspan=\"2\" align=left><b>Note:</b> In order to change your email address, you will receive another<br>confirmation email to your new address.</td></tr>\n");
tr("Change password", "<input type=\"password\" name=\"chpassword\" size=\"50\" />", 1);
tr("Type password again", "<input type=\"password\" name=\"passagain\" size=\"50\" />", 1);

function priv($name, $descr) {
	global $CURUSER;
	if ($CURUSER["privacy"] == $name)
		return "<input type=\"radio\" name=\"privacy\" value=\"$name\" checked=\"checked\" /> $descr";
	return "<input type=\"radio\" name=\"privacy\" value=\"$name\" /> $descr";
}

/* tr("Privacy level",  priv("normal", "Normal") . " " . priv("low", "Low (email address will be shown)") . " " . priv("strong", "Strong (no info will be made available)"), 1); */

?>
<tr><td colspan="2" align="center"><input type="submit" value="Submit changes!" style='height: 25px'> <input type="reset" value="Revert changes!" style='height: 25px'></td></tr>
</table>
</form>
</td>
</tr>
</table>
<?
if ($messages){
  print("<p>You have $messages message" . ($messages != 1 ? "s" : "") . " ($unread new) in your <a href=inbox.php><b>inbox</b></a>,<br>\n");
	if ($outmessages)
		print("and $outmessages message" . ($outmessages != 1 ? "s" : "") . " in your <a href=inbox.php?out=1><b>sentbox</b></a>.\n</p>");
	else
		print("and your <a href=inbox.php?out=1>sentbox</a> is empty.</p>");
}
else
{
  print("<p>Your <a href=inbox.php>inbox</a> is empty, <br>\n");
	if ($outmessages)
		print("and you have $outmessages message" . ($outmessages != 1 ? "s" : "") . " in your <a href=inbox.php?out=1><b>sentbox</b></a>.\n</p>");
	else
		print("and so is your <a href=inbox.php?out=1>sentbox</a>.</p>");
}

print("<p><a href=users.php><b>Find User/Browse User List</b></a></p>");
stdfoot();

hit_end();

?>