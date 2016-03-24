<?

$tzs["-720"] = "(GMT - 12:00 hours) Enitwetok, Kwajalien";
$tzs["-660"] = "(GMT - 11:00 hours) Midway Island, Samoa";
$tzs["-600"] = "(GMT - 10:00 hours) Hawaii";
$tzs["-540"] = "(GMT - 9:00 hours) Alaska";
$tzs["-480"] = "(GMT - 8:00 hours) Pacific Time (US &amp; Canada)";
$tzs["-420"] = "(GMT - 7:00 hours) Mountain Time (US &amp; Canada)";
$tzs["-360"] = "(GMT - 6:00 hours) Central Time (US &amp; Canada), Mexico City";
$tzs["-300"] = "(GMT - 5:00 hours) Eastern Time (US &amp; Canada), Bogota, Lima, Quito";
$tzs["-240"] = "(GMT - 4:00 hours) Atlantic Time (Canada), Caracas, La Paz";
$tzs["-210"] = "(GMT - 3:30 hours) Newfoundland";
$tzs["-180"] = "(GMT - 3:00 hours) Brazil, Buenos Aires, Georgetown, Falkland Isl.";
$tzs["-120"] = "(GMT - 2:00 hours) Mid-Atlantic, Ascention Isl., St Helena";
$tzs["-60"]  = "(GMT - 1:00 hour) Azores, Cape Verde Islands";
$tzs["0"]    = "(GMT) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia";
$tzs["60"]   = "(GMT + 1:00 hour) Amsterdam, Berlin, Copenhagen, Madrid, Paris, Rome";
$tzs["120"]  = "(GMT + 2:00 hours) Kaliningrad, South Africa, Warsaw";
$tzs["180"]  = "(GMT + 3:00 hours) Baghdad, Riyadh, Moscow, Nairobi";
$tzs["210"]  = "(GMT + 3:30 hours) Tehran";
$tzs["240"]  = "(GMT + 4:00 hours) Adu Dhabi, Baku, Muscat, Tbilisi";
$tzs["270"]  = "(GMT + 4:30 hours) Kabul";
$tzs["300"]  = "(GMT + 5:00 hours) Ekaterinburg, Islamabad, Karachi, Tashkent";
$tzs["330"]  = "(GMT + 5:30 hours) Bombay, Calcutta, Madras, New Delhi";
$tzs["360"]  = "(GMT + 6:00 hours) Almaty, Colomba, Dhakra";
$tzs["420"]  = "(GMT + 7:00 hours) Bangkok, Hanoi, Jakarta";
$tzs["480"]  = "(GMT + 8:00 hours) Beijing, Hong Kong, Perth, Singapore, Taipei";
$tzs["540"]  = "(GMT + 9:00 hours) Osaka, Sapporo, Seoul, Tokyo, Yakutsk";
$tzs["570"]  = "(GMT + 9:30 hours) Adelaide, Darwin";
$tzs["600"]  = "(GMT + 10:00 hours) Melbourne, Papua New Guinea, Sydney, Vladivostok";
$tzs["660"]  = "(GMT + 11:00 hours) Magadan, New Caledonia, Solomon Islands";
$tzs["720"]  = "(GMT + 12:00 hours) Auckland, Wellington, Fiji, Marshall Island";

function get_user_timezone($id) {
        $sql = "SELECT * FROM users WHERE id=$id LIMIT 1";
        $query = mysql_query($sql);
        if (mysql_num_rows($query) != "0") {
 $kasutaja = mysql_fetch_array($query);
 $timezone = $kasutaja["tzoffset"];
 return "$timezone"; } else {
         return "3"; } //Default timezone
 }

$smilies = array(
  ":-)" => "smile1.gif",
  ":smile:" => "smile2.gif",
  ":-D" => "grin.gif",
  ":lol:" => "laugh.gif",
  ":w00t:" => "w00t.gif",
  ":-P" => "tongue.gif",
  ";-)" => "wink.gif",
  ":-|" => "noexpression.gif",
  ":-/" => "confused.gif",
  ":-(" => "sad.gif",
  ":'-(" => "cry.gif",
  ":weep:" => "weep.gif",
  ":-O" => "ohmy.gif",
  ":o)" => "clown.gif",
  "8-)" => "cool1.gif",
  "|-)" => "sleeping.gif",
  ":innocent:" => "innocent.gif",
  ":whistle:" => "whistle.gif",
  ":unsure:" => "unsure.gif",
  ":closedeyes:" => "closedeyes.gif",
  ":cool:" => "cool2.gif",
  ":fun:" => "fun.gif",
  ":thumbsup:" => "thumbsup.gif",
  ":thumbsdown:" => "thumbsdown.gif",
  ":blush:" => "blush.gif",
  ":unsure:" => "unsure.gif",
  ":yes:" => "yes.gif",
  ":no:" => "no.gif",
  ":love:" => "love.gif",
  ":?:" => "question.gif",
  ":!:" => "excl.gif",
  ":idea:" => "idea.gif",
  ":arrow:" => "arrow.gif",
  ":arrow2:" => "arrow2.gif",
  ":hmm:" => "hmm.gif",
  ":hmmm:" => "hmmm.gif",
  ":huh:" => "huh.gif",
  ":geek:" => "geek.gif",
  ":look:" => "look.gif",
  ":rolleyes:" => "rolleyes.gif",
  ":kiss:" => "kiss.gif",
  ":shifty:" => "shifty.gif",
  ":blink:" => "blink.gif",
  ":smartass:" => "smartass.gif",
  ":sick:" => "sick.gif",
  ":crazy:" => "crazy.gif",
  ":wacko:" => "wacko.gif",
  ":alien:" => "alien.gif",
  ":wizard:" => "wizard.gif",
  ":wave:" => "wave.gif",
  ":wavecry:" => "wavecry.gif",
  ":baby:" => "baby.gif",
  ":angry:" => "angry.gif",
  ":ras:" => "ras.gif",
  ":sly:" => "sly.gif",
  ":devil:" => "devil.gif",
  ":evil:" => "evil.gif",
  ":evilmad:" => "evilmad.gif",
  ":sneaky:" => "sneaky.gif",
  ":axe:" => "axe.gif",
  ":slap:" => "slap.gif",
  ":wall:" => "wall.gif",
  ":rant:" => "rant.gif",
  ":jump:" => "jump.gif",
  ":yucky:" => "yucky.gif",
  ":nugget:" => "nugget.gif",
  ":smart:" => "smart.gif",
  ":shutup:" => "shutup.gif",
  ":shutup2:" => "shutup2.gif",
  ":crockett:" => "crockett.gif",
  ":zorro:" => "zorro.gif",
  ":snap:" => "snap.gif",
  ":beer:" => "beer.gif",
  ":beer2:" => "beer2.gif",
  ":drunk:" => "drunk.gif",
  ":strongbench:" => "strongbench.gif",
  ":weakbench:" => "weakbench.gif",
  ":dumbells:" => "dumbells.gif",
  ":music:" => "music.gif",
  ":stupid:" => "stupid.gif",
  ":dots:" => "dots.gif",
  ":offtopic:" => "offtopic.gif",
  ":spam:" => "spam.gif",
  ":oops:" => "oops.gif",
  ":lttd:" => "lttd.gif",
  ":please:" => "please.gif",
  ":sorry:" => "sorry.gif",
  ":hi:" => "hi.gif",
  ":yay:" => "yay.gif",
  ":cake:" => "cake.gif",
  ":hbd:" => "hbd.gif",
  ":band:" => "band.gif",
  ":punk:" => "punk.gif",
	":rofl:" => "rofl.gif",
  ":bounce:" => "bounce.gif",
  ":mbounce:" => "mbounce.gif",
  ":thankyou:" => "thankyou.gif",
  ":gathering:" => "gathering.gif",
  ":hang:" => "hang.gif",
  ":chop:" => "chop.gif",
  ":rip:" => "rip.gif",
  ":whip:" => "whip.gif",
  ":judge:" => "judge.gif",
  ":chair:" => "chair.gif",
  ":tease:" => "tease.gif",
  ":box:" => "box.gif",
  ":boxing:" => "boxing.gif",
  ":guns:" => "guns.gif",
  ":shoot:" => "shoot.gif",
  ":shoot2:" => "shoot2.gif",
  ":flowers:" => "flowers.gif",
  ":wub:" => "wub.gif",
  ":lovers:" => "lovers.gif",
  ":kissing:" => "kissing.gif",
  ":kissing2:" => "kissing2.gif",
  ":console:" => "console.gif",
  ":group:" => "group.gif",
  ":hump:" => "hump.gif",
  ":hooray:" => "hooray.gif",
  ":happy2:" => "happy2.gif",
  ":clap:" => "clap.gif",
  ":clap2:" => "clap2.gif",
	":weirdo:" => "weirdo.gif",
  ":yawn:" => "yawn.gif",
  ":bow:" => "bow.gif",
	":dawgie:" => "dawgie.gif",
	":cylon:" => "cylon.gif",
  ":book:" => "book.gif",
  ":fish:" => "fish.gif",
  ":mama:" => "mama.gif",
  ":pepsi:" => "pepsi.gif",
  ":medieval:" => "medieval.gif",
  ":rambo:" => "rambo.gif",
  ":ninja:" => "ninja.gif",
  ":hannibal:" => "hannibal.gif",
  ":party:" => "party.gif",
  ":snorkle:" => "snorkle.gif",
  ":evo:" => "evo.gif",
  ":king:" => "king.gif",
  ":chef:" => "chef.gif",
  ":mario:" => "mario.gif",
  ":pope:" => "pope.gif",
  ":fez:" => "fez.gif",
  ":cap:" => "cap.gif",
  ":cowboy:" => "cowboy.gif",
  ":pirate:" => "pirate.gif",
  ":pirate2:" => "pirate2.gif",
  ":rock:" => "rock.gif",
  ":cigar:" => "cigar.gif",
  ":icecream:" => "icecream.gif",
  ":oldtimer:" => "oldtimer.gif",
	":trampoline:" => "trampoline.gif",
	":banana:" => "bananadance.gif",
  ":smurf:" => "smurf.gif",
  ":yikes:" => "yikes.gif",
  ":osama:" => "osama.gif",
  ":saddam:" => "saddam.gif",
  ":santa:" => "santa.gif",
  ":indian:" => "indian.gif",
  ":pimp:" => "pimp.gif",
  ":nuke:" => "nuke.gif",
  ":jacko:" => "jacko.gif",
  ":ike:" => "ike.gif",
  ":greedy:" => "greedy.gif",
	":super:" => "super.gif",
  ":wolverine:" => "wolverine.gif",
  ":spidey:" => "spidey.gif",
  ":spider:" => "spider.gif",
  ":bandana:" => "bandana.gif",
  ":construction:" => "construction.gif",
  ":sheep:" => "sheep.gif",
  ":police:" => "police.gif",
	":detective:" => "detective.gif",
  ":bike:" => "bike.gif",
	":fishing:" => "fishing.gif",
  ":clover:" => "clover.gif",
  ":horse:" => "horse.gif",
  ":shit:" => "shit.gif",
  ":soldiers:" => "soldiers.gif",
);

$privatesmilies = array(
  ":)" => "smile1.gif",
//  ";)" => "wink.gif",
  ":wink:" => "wink.gif",
  ":D" => "grin.gif",
  ":P" => "tongue.gif",
  ":(" => "sad.gif",
  ":'(" => "cry.gif",
  ":|" => "noexpression.gif",
  // "8)" => "cool1.gif",   we don't want this as a smilie...
  ":Boozer:" => "alcoholic.gif",
  ":deadhorse:" => "deadhorse.gif",
  ":spank:" => "spank.gif",
  ":yoji:" => "yoji.gif",
  ":locked:" => "locked.gif",
  ":grrr:" => "angry.gif", 			// legacy
  "O:-" => "innocent.gif",			// legacy
  ":sleeping:" => "sleeping.gif",	// legacy
  "-_-" => "unsure.gif",			// legacy
  ":clown:" => "clown.gif",
  ":mml:" => "mml.gif",
  ":rtf:" => "rtf.gif",
  ":morepics:" => "morepics.gif",
  ":rb:" => "rb.gif",
  ":rblocked:" => "rblocked.gif",
  ":maxlocked:" => "maxlocked.gif",
  ":hslocked:" => "hslocked.gif",
);

// Set this to the line break character sequence of your system
$linebreak = "\r\n";

function get_row_count($table, $suffix = "")
{
  if ($suffix)
    $suffix = " $suffix";
  ($r = mysql_query("SELECT COUNT(*) FROM $table$suffix")) or die(mysql_error());
  ($a = mysql_fetch_row($r)) or die(mysql_error());
  return $a[0];
}

function stdmsg($heading, $text)
{
  print("<table class=main width=750 border=0 cellpadding=0 cellspacing=0><tr><td class=embedded>\n");
  if ($heading)
    print("<h2>$heading</h2>\n");
  print("<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
  print($text . "</td></tr></table></td></tr></table>\n");
}


function stderr($heading, $text)
{
  stdhead();
  stdmsg($heading, $text);
  stdfoot();
  die;
}

function sqlerr($file = '', $line = '')
{
  print("<table border=0 bgcolor=blue align=left cellspacing=0 cellpadding=10 style='background: blue'>" .
    "<tr><td class=embedded><font color=white><h1>SQL Error</h1>\n" .
  "<b>" . mysql_error() . ($file != '' && $line != '' ? "<p>in $file, line $line</p>" : "") . "</b></font></td></tr></table>");
  die;
}

// Returns the current time in GMT in MySQL compatible format.
function get_date_time($timestamp = 0)
{
if ($timestamp)
return date("Y-m-d H:i:s", $timestamp);
else
  $idcookie = $_COOKIE['uid'];
  return gmdate("Y-m-d H:i:s", time() + (60 * get_user_timezone($idcookie)));
}

function encodehtml($s, $linebreaks = true)
{
  $s = str_replace("<", "&lt;", str_replace("&", "&amp;", $s));
  if ($linebreaks)
    $s = nl2br($s);
  return $s;
}

function get_dt_num()
{
  return gmdate("YmdHis");
}

function format_urls($s)
{
	return preg_replace(
    	"/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps|irc):\/\/[^()<>\s]+)/i",
	    "\\1<a href=\"\\2\">\\2</a>", $s);
}

/*

// Removed this fn, I've decided we should drop the redir script...
// it's pretty useless since ppl can still link to pics...
// -Rb

function format_local_urls($s)
{
	return preg_replace(
    "/(<a href=redir\.php\?url=)((http|ftp|https|ftps|irc):\/\/(www\.)?torrentbits\.(net|org|com)(:8[0-3])?([^<>\s]*))>([^<]+)<\/a>/i",
    "<a href=\\2>\\8</a>", $s);
}
*/

//Finds last occurrence of needle in haystack
//in PHP5 use strripos() instead of this
function _strlastpos ($haystack, $needle, $offset = 0)
{
	$addLen = strlen ($needle);
	$endPos = $offset - $addLen;
	while (true)
	{
		if (($newPos = strpos ($haystack, $needle, $endPos + $addLen)) === false) break;
		$endPos = $newPos;
	}
	return ($endPos >= 0) ? $endPos : false;
}

function format_quotes($s)
{
  while ($old_s != $s)
  {
  	$old_s = $s;

	  //find first occurrence of [/quote]
	  $close = strpos($s, "[/quote]");
	  if ($close === false)
	  	return $s;

	  //find last [quote] before first [/quote]
	  //note that there is no check for correct syntax
	  $open = _strlastpos(substr($s,0,$close), "[quote");
	  if ($open === false)
	    return $s;

	  $quote = substr($s,$open,$close - $open + 8);

	  //[quote]Text[/quote]
	  $quote = preg_replace(
	    "/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
	    "<p class=sub><b>Quote:</b></p><table class=main border=1 cellspacing=0 cellpadding=10><tr><td style='border: 1px black dotted'>\\1</td></tr></table><br>", $quote);

	  //[quote=Author]Text[/quote]
	  $quote = preg_replace(
	    "/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
	    "<p class=sub><b>\\1 wrote:</b></p><table class=main border=1 cellspacing=0 cellpadding=10><tr><td style='border: 1px black dotted'>\\2</td></tr></table><br>", $quote);

	  $s = substr($s,0,$open) . $quote . substr($s,$close + 8);
  }

	return $s;
}

function format_comment($text, $strip_html = true)
{
	global $smilies, $privatesmilies;

	$s = $text;

  // This fixes the extraneous ;) smilies problem. When there was an html escaped
  // char before a closing bracket - like >), "), ... - this would be encoded
  // to &xxx;), hence all the extra smilies. I created a new :wink: label, removed
  // the ;) one, and replace all genuine ;) by :wink: before escaping the body.
  // (What took us so long? :blush:)- wyz

	$s = str_replace(";)", ":wink:", $s);

	if ($strip_html)
		$s = htmlspecialchars($s);

	// [*]
	$s = preg_replace("/\[\*\]/", "<li>", $s);

	// [b]Bold[/b]
	$s = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/", "<b>\\1</b>", $s);

	// [i]Italic[/i]
	$s = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/", "<i>\\1</i>", $s);

	// [u]Underline[/u]
	$s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/", "<u>\\1</u>", $s);

	// [u]Underline[/u]
	$s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/i", "<u>\\1</u>", $s);

	// [img]http://www/image.gif[/img]
	$s = preg_replace("/\[img\]([^\s'\"<>]+?)\[\/img\]/i", "<img border=0 src=\"\\1\">", $s);

	// [img=http://www/image.gif]
	$s = preg_replace("/\[img=([^\s'\"<>]+?)\]/i", "<img border=0 src=\"\\1\">", $s);

	// [color=blue]Text[/color]
	$s = preg_replace(
		"/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/i",
		"<font color=\\1>\\2</font>", $s);

	// [color=#ffcc99]Text[/color]
	$s = preg_replace(
		"/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]/i",
		"<font color=\\1>\\2</font>", $s);

	// [url=http://www.example.com]Text[/url]
	$s = preg_replace(
		"/\[url=([^()<>\s]+?)\]((\s|.)+?)\[\/url\]/i",
		"<a href=\"\\1\">\\2</a>", $s);

	// [url]http://www.example.com[/url]
	$s = preg_replace(
		"/\[url\]([^()<>\s]+?)\[\/url\]/i",
		"<a href=\"\\1\">\\1</a>", $s);

	// [size=4]Text[/size]
	$s = preg_replace(
		"/\[size=([1-7])\]((\s|.)+?)\[\/size\]/i",
		"<font size=\\1>\\2</font>", $s);

	// [font=Arial]Text[/font]
	$s = preg_replace(
		"/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/i",
		"<font face=\"\\1\">\\2</font>", $s);

//  //[quote]Text[/quote]
//  $s = preg_replace(
//    "/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
//    "<p class=sub><b>Quote:</b></p><table class=main border=1 cellspacing=0 cellpadding=10><tr><td style='border: 1px black dotted'>\\1</td></tr></table><br>", $s);

//  //[quote=Author]Text[/quote]
//  $s = preg_replace(
//    "/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
//    "<p class=sub><b>\\1 wrote:</b></p><table class=main border=1 cellspacing=0 cellpadding=10><tr><td style='border: 1px black dotted'>\\2</td></tr></table><br>", $s);

	// Quotes
	$s = format_quotes($s);

	// URLs
	$s = format_urls($s);
//	$s = format_local_urls($s);

	// Linebreaks
	$s = nl2br($s);

	// [pre]Preformatted[/pre]
	$s = preg_replace("/\[pre\]((\s|.)+?)\[\/pre\]/i", "<tt><nobr>\\1</nobr></tt>", $s);

	// [nfo]NFO-preformatted[/nfo]
	$s = preg_replace("/\[nfo\]((\s|.)+?)\[\/nfo\]/i", "<tt><nobr><font face='MS Linedraw' size=2 style='font-size: 10pt; line-height: " .
		"10pt'>\\1</font></nobr></tt>", $s);

	// Maintain spacing
	$s = str_replace("  ", " &nbsp;", $s);

	reset($smilies);
	while (list($code, $url) = each($smilies))
		$s = str_replace($code, "<img border=0 src=\"/pic/smilies/$url\" alt=\"" . htmlspecialchars($code) . "\">", $s);

	reset($privatesmilies);
	while (list($code, $url) = each($privatesmilies))
		$s = str_replace($code, "<img border=0 src=\"/pic/smilies/$url\">", $s);

	return $s;
}

define ("UC_USER", 0);
define ("UC_POWER_USER", 1);
define ("UC_VIP", 2);
define ("UC_UPLOADER", 3);
define ("UC_MODERATOR", 4);
define ("UC_ADMINISTRATOR", 5);
define ("UC_SYSOP", 6);

function get_user_class()
{
  global $CURUSER;
  return $CURUSER["class"];
}

function get_user_class_name($class)
{
  switch ($class)
  {
    case UC_USER: return "User";

    case UC_POWER_USER: return "Power User";

    case UC_VIP: return "VIP";

    case UC_UPLOADER: return "Uploader";

    case UC_MODERATOR: return "Moderator";

    case UC_ADMINISTRATOR: return "Administrator";

    case UC_SYSOP: return "SysOp";
  }
  return "";
}

function is_valid_user_class($class)
{
  return is_numeric($class) && floor($class) == $class && $class >= UC_USER && $class <= UC_SYSOP;
}

function is_valid_id($id)
{
  return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}


  //-------- Begins a main frame

  function begin_main_frame()
  {
    print("<table class=main width=750 border=0 cellspacing=0 cellpadding=0>" .
      "<tr><td class=embedded>\n");
  }

  //-------- Ends a main frame

  function end_main_frame()
  {
    print("</td></tr></table>\n");
  }

  function begin_frame($caption = "", $center = false, $padding = 10)
  {
    if ($caption)
      print("<h2>$caption</h2>\n");

    if ($center)
      $tdextra .= " align=center";

    print("<table width=100% border=1 cellspacing=0 cellpadding=$padding><tr><td$tdextra>\n");

  }

  function attach_frame($padding = 10)
  {
    print("</td></tr><tr><td style='border-top: 0px'>\n");
  }

  function end_frame()
  {
    print("</td></tr></table>\n");
  }

  function begin_table($fullwidth = false, $padding = 5)
  {
    if ($fullwidth)
      $width = " width=100%";
    print("<table class=main$width border=1 cellspacing=0 cellpadding=$padding>\n");
  }

  function end_table()
  {
    print("</td></tr></table>\n");
  }

  //-------- Inserts a smilies frame
  //         (move to globals)

  function insert_smilies_frame()
  {
    global $smilies, $BASEURL;

    begin_frame("Smilies", true);

    begin_table(false, 5);

    print("<tr><td class=colhead>Type...</td><td class=colhead>To make a...</td></tr>\n");

    while (list($code, $url) = each($smilies))
      print("<tr><td>$code</td><td><img src=$BASEURL/pic/smilies/$url></td>\n");

    end_table();

    end_frame();
  }


function sql_timestamp_to_unix_timestamp($s)
{
  return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}

  function get_ratio_color($ratio)
  {
    if ($ratio < 0.1) return "#ff0000";
    if ($ratio < 0.2) return "#ee0000";
    if ($ratio < 0.3) return "#dd0000";
    if ($ratio < 0.4) return "#cc0000";
    if ($ratio < 0.5) return "#bb0000";
    if ($ratio < 0.6) return "#aa0000";
    if ($ratio < 0.7) return "#990000";
    if ($ratio < 0.8) return "#880000";
    if ($ratio < 0.9) return "#770000";
    if ($ratio < 1) return "#660000";
    return "#000000";
  }

  function get_slr_color($ratio)
  {
    if ($ratio < 0.025) return "#ff0000";
    if ($ratio < 0.05) return "#ee0000";
    if ($ratio < 0.075) return "#dd0000";
    if ($ratio < 0.1) return "#cc0000";
    if ($ratio < 0.125) return "#bb0000";
    if ($ratio < 0.15) return "#aa0000";
    if ($ratio < 0.175) return "#990000";
    if ($ratio < 0.2) return "#880000";
    if ($ratio < 0.225) return "#770000";
    if ($ratio < 0.25) return "#660000";
    if ($ratio < 0.275) return "#550000";
    if ($ratio < 0.3) return "#440000";
    if ($ratio < 0.325) return "#330000";
    if ($ratio < 0.35) return "#220000";
    if ($ratio < 0.375) return "#110000";
    return "#000000";
  }

function write_log($text)
{
  $text = sqlesc($text);
  $added = sqlesc(get_date_time());
  mysql_query("INSERT INTO sitelog (added, txt) VALUES($added, $text)") or sqlerr(__FILE__, __LINE__);
}

function get_elapsed_time($ts)
{
  $mins = floor((gmtime() - $ts) / 60);
  $hours = floor($mins / 60);
  $mins -= $hours * 60;
  $days = floor($hours / 24);
  $hours -= $days * 24;
  $weeks = floor($days / 7);
  $days -= $weeks * 7;
  $t = "";
  if ($weeks > 0)
    return "$weeks week" . ($weeks > 1 ? "s" : "");
  if ($days > 0)
    return "$days day" . ($days > 1 ? "s" : "");
  if ($hours > 0)
    return "$hours hour" . ($hours > 1 ? "s" : "");
  if ($mins > 0)
    return "$mins min" . ($mins > 1 ? "s" : "");
  return "< 1 min";
}

?>
