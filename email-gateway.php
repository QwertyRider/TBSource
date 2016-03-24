<?

require "include/bittorrent.php";
dbconn();

$id = 0 + $HTTP_GET_VARS["id"];
if (!$id)
	stderr("Error", "Bad or missing ID.");

$res = mysql_query("SELECT username, class, email FROM users WHERE id=$id");
$arr = mysql_fetch_assoc($res) or stderr("Error", "No such user.");
$username = $arr["username"];
if ($arr["class"] < UC_MODERATOR)
	stderr("Error", "The gateway can only be used to e-mail staff members.");

if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST")
{
	$to = $arr["email"];

	$from = substr(trim($HTTP_POST_VARS["from"]), 0, 80);
	if ($from == "") $from = "Anonymous";

	$from_email = substr(trim($HTTP_POST_VARS["from_email"]), 0, 80);
	if ($from_email == "") $from_email = "noreply@torrentbits.org";
	if (!strpos($from_email, "@")) stderr("Error", "The entered e-mail address does not seem to be valid.");

	$from = "$from <$from_email>";

	$subject = substr(trim($HTTP_POST_VARS["subject"]), 0, 80);
	if ($subject == "") $subject = "(No subject)";
	$subject = "Fw: $subject";

	$message = trim($HTTP_POST_VARS["message"]);
	if ($message == "") stderr("Error", "No message text!");

	$message = "Message submitted from $HTTP_SERVER_VARS[REMOTE_ADDR] at " . gmdate("Y-m-d H:i:s") . " GMT.\n" .
		"Note: By replying to this e-mail you will reveal your e-mail address.\n" .
		"---------------------------------------------------------------------\n\n" .
		$message . "\n\n" .
		"---------------------------------------------------------------------\n$SITENAME E-Mail Gateway\n";

	$success = mail($to, $subject, $message, "From: $from", "-f$SITEEMAIL");

	if ($success)
		stderr("Success", "E-mail successfully queued for delivery.");
	else
		stderr("Error", "The mail could not be sent. Please try again later.");
}

stdhead("E-mail gateway");
?>
<p><table border=0 class=main cellspacing=0 cellpadding=0><tr>
<td class=embedded><img src=pic/email.gif></td>
<td class=embedded style='padding-left: 10px'><font size=3><b>Send e-mail to <?=$username;?></b></font></td>
</tr></table></p>
<table border=1 cellspacing=0 cellpadding=5>
<form method=post action=email-gateway.php?id=<?=$id?>>
<tr><td class=rowhead>Your name</td><td><input type=text name=from size=80></td></tr>
<tr><td class=rowhead>Your e-mail</td><td><input type=text name=from_email size=80></td></tr>
<tr><td class=rowhead>Subject</td><td><input type=text name=subject size=80></td></tr>
<tr><td class=rowhead>Message</td><td><textarea name=message cols=80 rows=20></textarea></td></tr>
<tr><td colspan=2 align=center><input type=submit value="Send" class=btn></td></tr>
</form>
</table>
<p>
<font class=small><b>Note:</b> Your IP-address will be logged and visible to the recipient to prevent abuse.<br>
Make sure to supply a valid e-mail address if you expect a reply.</font>
</p>
<? stdfoot(); ?>