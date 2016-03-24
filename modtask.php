<?

require "include/bittorrent.php";

dbconn(false);

loggedinorreturn();

function puke($text = "w00t")
{
  stderr("w00t", $text);
}

if (get_user_class() < UC_MODERATOR)
  puke();

$action = $_POST["action"];

if ($action == "edituser")
{
  $userid = $_POST["userid"];
  $title = $_POST["title"];
  $avatar = $_POST["avatar"];
  $enabled = $_POST["enabled"];
  $warned = $_POST["warned"];
  $warnlength = 0 + $_POST["warnlength"];
  $warnpm = $_POST["warnpm"];
  $donor = $_POST["donor"];
  $modcomment = $_POST["modcomment"];
  if ($_POST['resetpasskey']) $updateset[] = "passkey=''";

  $class = 0 + $_POST["class"];
  if (!is_valid_id($userid) || !is_valid_user_class($class))
    stderr("Error", "Bad user ID or class ID.");
  // check target user class
  $res = mysql_query("SELECT warned, enabled, username, class FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
  $arr = mysql_fetch_assoc($res) or puke();
  $curenabled = $arr["enabled"];
  $curclass = $arr["class"];
  $curwarned = $arr["warned"];
  // User may not edit someone with same or higher class than himself!
  if ($curclass >= get_user_class())
    puke();

  if ($curclass != $class)
  {
    // Notify user
    $what = ($class > $curclass ? "promoted" : "demoted");
    $msg = sqlesc("You have been $what to '" . get_user_class_name($class) . "' by $CURUSER[username].");
    $added = sqlesc(get_date_time());
    mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    $updateset[] = "class = $class";
    $what = ($class > $curclass ? "Promoted" : "Demoted");
 		$modcomment = gmdate("Y-m-d") . " - $what to '" . get_user_class_name($class) . "' by $CURUSER[username].\n". $modcomment;
  }

  // some Helshad fun
  $fun = ($CURUSER['id'] == 277) ? " Tremble in fear, mortal." : "";

  if ($warned && $curwarned != $warned)
  {
		$updateset[] = "warned = " . sqlesc($warned);
		$updateset[] = "warneduntil = '0000-00-00 00:00:00'";
    if ($warned == 'no')
    {
			$modcomment = gmdate("Y-m-d") . " - Warning removed by " . $CURUSER['username'] . ".\n". $modcomment;
      $msg = sqlesc("Your warning has been removed by " . $CURUSER['username'] . ".");
    }
		$added = sqlesc(get_date_time());
		mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
  }
	elseif ($warnlength)
  {
    if ($warnlength == 255)
    {
			$modcomment = gmdate("Y-m-d") . " - Warned by " . $CURUSER['username'] . ".\nReason: $warnpm\n" . $modcomment;
      $msg = sqlesc("You have received a [url=rules.php#warning]warning[/url] from $CURUSER[username].$fun" . ($warnpm ? "\n\nReason: $warnpm" : ""));
			$updateset[] = "warneduntil = '0000-00-00 00:00:00'";
    }
    else
    {
	    $warneduntil = get_date_time(gmtime() + $warnlength * 604800);
	    $dur = $warnlength . " week" . ($warnlength > 1 ? "s" : "");
	    $msg = sqlesc("You have received a $dur [url=rules.php#warning]warning[/url] from " . $CURUSER['username'] . ".$fun" . ($warnpm ? "\n\nReason: $warnpm" : ""));
	    $modcomment = gmdate("Y-m-d") . " - Warned for $dur by " . $CURUSER['username'] .  ".\nReason: $warnpm\n" . $modcomment;
	    $updateset[] = "warneduntil = '$warneduntil'";
		}
 		$added = sqlesc(get_date_time());
		mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    $updateset[] = "warned = 'yes'";
	}

  if ($enabled != $curenabled)
  {
  	if ($enabled == 'yes')
  		$modcomment = gmdate("Y-m-d") . " - Enabled by " . $CURUSER['username'] . ".\n" . $modcomment;
  	else
  		$modcomment = gmdate("Y-m-d") . " - Disabled by " . $CURUSER['username'] . ".\n" . $modcomment;
  }

  $updateset[] = "enabled = " . sqlesc($enabled);
  $updateset[] = "donor = " . sqlesc($donor);
  $updateset[] = "avatar = " . sqlesc($avatar);
  $updateset[] = "title = " . sqlesc($title);
  $updateset[] = "modcomment = " . sqlesc($modcomment);
  mysql_query("UPDATE users SET  " . implode(", ", $updateset) . " WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
  $returnto = $_POST["returnto"];

  header("Location: $DEFAULTBASEURL/$returnto");
  die;
}

puke();

?>