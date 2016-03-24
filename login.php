<?

require_once("include/bittorrent.php");

hit_start();

dbconn();

hit_count();

stdhead("Login");

unset($returnto);
if (!empty($_GET["returnto"])) {
	$returnto = $_GET["returnto"];
	if (!$_GET["nowarn"]) {
		print("<h1>Not logged in!</h1>\n");
		print("<p><b>Error:</b> The page you tried to view can only be used when you're logged in.</p>\n");
	}
}

?>
<form method="post" action="takelogin.php">
<p>Note: You need cookies enabled to log in.</p>
<table border="0" cellpadding=5>
<tr><td class=rowhead>Username:</td><td align=left><input type="text" size=40 name="username" /></td></tr>
<tr><td class=rowhead>Password:</td><td align=left><input type="password" size=40 name="password" /></td></tr>
<!--<tr><td class=rowhead>Duration:</td><td align=left><input type=checkbox name=logout value='yes' checked>Log me out after 15 minutes inactivity</td></tr>-->
<tr><td colspan="2" align="center"><input type="submit" value="Log in!" class=btn></td></tr>
</table>
<?

if (isset($returnto))
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($returnto) . "\" />\n");

?>
</form>
<p>Don't have an account? <a href="signup.php">Sign up</a> right now!</p>
<?

stdfoot();

hit_end();

?>
