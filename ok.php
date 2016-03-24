<?

require_once("include/bittorrent.php");

hit_start();

dbconn();

hit_count();

if (!mkglobal("type"))
	die();

if ($type == "signup" && mkglobal("email")) {
	stdhead("User signup");
        stdmsg("Signup successful!",
	"A confirmation email has been sent to the address you specified (" . htmlspecialchars($email) . "). You need to read and respond to this email before you can use your account. If you don't do this, the new account will be deleted automatically after a few days.");
	stdfoot();
}
elseif ($type == "confirmed") {
	stdhead("Already confirmed");
	print("<h1>Already confirmed</h1>\n");
	print("<p>This user account has already been confirmed. You can proceed to <a href=\"login.php\">log in</a> with it.</p>\n");
	stdfoot();
}
elseif ($type == "confirm") {
	if (isset($CURUSER)) {
		stdhead("Signup confirmation");
		print("<h1>Account successfully confirmed!</h1>\n");
		print("<p>Your account has been activated! You have been automatically logged in. You can now continue to the <a href=\"/\"><b>main page</b></a> and start using your account.</p>\n");
		print("<p>Before you start using torrentbits we urge you to read the <a href=\"rules.php\"><b>RULES</b></a> and the <a href=\"faq.php\"><b>FAQ</b></a>.</p>\n");
		stdfoot();
	}
	else {
		stdhead("Signup confirmation");
		print("<h1>Account successfully confirmed!</h1>\n");
		print("<p>Your account has been activated! However, it appears that you could not be logged in automatically. A possible reason is that you disabled cookies in your browser. You have to enable cookies to use your account. Please do that and then <a href=\"login.php\">log in</a> and try again.</p>\n");
		stdfoot();
	}
}
else
	die();

hit_end();

?>
