<?
// This script downloads/adds/updates peerguardian IP ranges in the bans table.

require "include/bittorrent.php";

dbconn();

header("Content-type: text/plain");

if (get_user_class() < UC_ADMINISTRATOR) die;

$url = "http://www.bluetack.co.uk/config/antip2p.txt";

$f = @fopen($url, "r");
if (!$f)
  die("Cannot download: " . htmlspecialchars($url));

mysql_query("DELETE FROM bans WHERE comment LIKE 'PeerGuardian: %'") or sqlerr(__FILE__, __LINE__);

$uid = $CURUSER["id"];
$n = 0;
$o = 0;
$dt = sqlesc(get_date_time());
while (!feof($f))
{
	++$o;
	$s = rtrim(fgets($f));
	$i = strrpos($s, ":");
	if (!$i) continue;
	$comment = sqlesc("PeerGuardian: " . substr($s, 0, $i));
	$s = substr($s, $i + 1);
	$i = strpos($s, "-");
	$first = ip2long(substr($s, 0, $i));
	$last = ip2long(substr($s, $i + 1));
	if ($first == -1 || $last == -1) continue;
	$query = "INSERT INTO bans (added, addedby, first, last, comment) VALUES($dt, $uid, $first, $last, $comment)";
	$res = mysql_query($query) or sqlerr(__FILE__, __LINE__);
	if (mysql_affected_rows() != 1)
		die("Database insertion failed (" . htmlspecialchars($query) . ").");
	++$n;
}
$o -= $n;
print("Source: " . htmlspecialchars($url) . "\n$n ranges imported, $o line(s) was discarded.");

?>