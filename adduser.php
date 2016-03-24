<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
if (get_user_class() < UC_ADMINISTRATOR)
	stderr("Error", "Access denied.");
if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST")
{
	if ($HTTP_POST_VARS["username"] == "" || $HTTP_POST_VARS["password"] == "" || $HTTP_POST_VARS["email"] == "")
		stderr("Error", "Missing form data.");
	if ($HTTP_POST_VARS["password"] != $HTTP_POST_VARS["password2"])
		stderr("Error", "Passwords mismatch.");
	$username = sqlesc($HTTP_POST_VARS["username"]);
	$password = $HTTP_POST_VARS["password"];
	$email = sqlesc($HTTP_POST_VARS["email"]);
	$secret = mksecret();
	$passhash = sqlesc(md5($secret . $password . $secret));
  $secret = sqlesc($secret);

	mysql_query("INSERT INTO users (added, last_access, secret, username, passhash, status, email) VALUES(NOW(), NOW(), $secret, $username, $passhash, 'confirmed', $email)") or sqlerr(__FILE__, __LINE__);
	$res = mysql_query("SELECT id FROM users WHERE username=$username");
	$arr = mysql_fetch_row($res);
	if (!$arr)
		stderr("Error", "Unable to create the account. The user name is possibly already taken.");
	header("Location: $$DEFAULTBASEURL/userdetails.php?id=$arr[0]");
	die;
}
stdhead("Add user");
?>
<h1>Add user</h1>
<form method=post action=adduser.php>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead>User name</td><td><input type=text name=username size=40></td></tr>
<tr><td class=rowhead>Password</td><td><input type=password name=password size=40></td></tr>
<tr><td class=rowhead>Re-type password</td><td><input type=password name=password2 size=40></td></tr>
<tr><td class=rowhead>E-mail</td><td><input type=text name=email size=40></td></tr>
<tr><td colspan=2 align=center><input type=submit value="Okay" class=btn></td></tr>
</table>
</form>
<? stdfoot(); ?>