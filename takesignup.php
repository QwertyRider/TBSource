<?

require_once("include/bittorrent.php");

hit_start();

dbconn();

$res = mysql_query("SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
if ($arr[0] >= $maxusers)
	stderr("Error", "Sorry, user limit reached. Please try again later.");

if (!mkglobal("wantusername:wantpassword:passagain:email"))
	die();

function bark($msg) {
  stdhead();
	stdmsg("Signup failed!", $msg);
  stdfoot();
  exit;
}

function validusername($username)
{
	if ($username == "")
	  return false;

	// The following characters are allowed in user names
	$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	for ($i = 0; $i < strlen($username); ++$i)
	  if (strpos($allowedchars, $username[$i]) === false)
	    return false;

	return true;
}

function isportopen($port)
{
	global $HTTP_SERVER_VARS;
	$sd = @fsockopen($HTTP_SERVER_VARS["REMOTE_ADDR"], $port, $errno, $errstr, 1);
	if ($sd)
	{
		fclose($sd);
		return true;
	}
	else
		return false;
}
/*
function isproxy()
{
	$ports = array(80, 88, 1075, 1080, 1180, 1182, 2282, 3128, 3332, 5490, 6588, 7033, 7441, 8000, 8080, 8085, 8090, 8095, 8100, 8105, 8110, 8888, 22788);
	for ($i = 0; $i < count($ports); ++$i)
		if (isportopen($ports[$i])) return true;
	return false;
}
*/
if (empty($wantusername) || empty($wantpassword) || empty($email))
	bark("Don't leave any fields blank.");

if (strlen($wantusername) > 12)
	bark("Sorry, username is too long (max is 12 chars)");

if ($wantpassword != $passagain)
	bark("The passwords didn't match! Must've typoed. Try again.");

if (strlen($wantpassword) < 6)
	bark("Sorry, password is too short (min is 6 chars)");

if (strlen($wantpassword) > 40)
	bark("Sorry, password is too long (max is 40 chars)");

if ($wantpassword == $wantusername)
	bark("Sorry, password cannot be same as user name.");

if (!validemail($email))
	bark("That doesn't look like a valid email address.");

if (!validusername($wantusername))
	bark("Invalid username.");

// make sure user agrees to everything...
if ($HTTP_POST_VARS["rulesverify"] != "yes" || $HTTP_POST_VARS["faqverify"] != "yes" || $HTTP_POST_VARS["ageverify"] != "yes")
	stderr("Signup failed", "Sorry, you're not qualified to become a member of this site.");

// check if email addy is already in use
$a = (@mysql_fetch_row(@mysql_query("select count(*) from users where email='$email'"))) or die(mysql_error());
if ($a[0] != 0)
  bark("The e-mail address $email is already in use.");

/*
// do simple proxy check
if (isproxy())
	bark("You appear to be connecting through a proxy server. Your organization or ISP may use a transparent caching HTTP proxy. Please try and access the site on <a href=http://torrentbits.org:81/signup.php>port 81</a> (this should bypass the proxy server). <p><b>Note:</b> if you run an Internet-accessible web server on the local machine you need to shut it down until the sign-up is complete.");
*/
hit_count();

$secret = mksecret();
$wantpasshash = md5($secret . $wantpassword . $secret);
$editsecret = mksecret();

$ret = mysql_query("INSERT INTO users (username, passhash, secret, editsecret, email, status, added) VALUES (" .
		implode(",", array_map("sqlesc", array($wantusername, $wantpasshash, $secret, $editsecret, $email, 'pending'))) .
		",'" . get_date_time() . "')");

if (!$ret) {
	if (mysql_errno() == 1062)
		bark("Username already exists!");
	bark("borked");
}

$id = mysql_insert_id();

//write_log("User account $id ($wantusername) was created");

$psecret = md5($editsecret);

$body = <<<EOD
You have requested a new user account on $SITENAME and you have
specified this address ($email) as user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.

To confirm your user registration, you have to follow this link:

$DEFAULTBASEURL/confirm.php?id=$id&secret=$psecret

After you do this, you will be able to use your new account. If you fail to
do this, you account will be deleted within a few days. We urge you to read
the RULES and FAQ before you start using torrentbits.
EOD;
mail($email, "$SITENAME user registration confirmation", $body, "From: $SITEEMAIL", "-f$SITEEMAIL");

header("Refresh: 0; url=ok.php?type=signup&email=" . urlencode($email));

hit_end();

?>