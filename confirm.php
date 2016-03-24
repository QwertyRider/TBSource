<?

require_once("include/bittorrent.php");

hit_start();

$id = 0 + $HTTP_GET_VARS["id"];
$md5 = $HTTP_GET_VARS["secret"];

if (!$id)
	httperr();

dbconn();

hit_count();

$res = mysql_query("SELECT passhash, editsecret, status FROM users WHERE id = $id");
$row = mysql_fetch_array($res);

if (!$row)
	httperr();

if ($row["status"] != "pending") {
	header("Refresh: 0; url=../../ok.php?type=confirmed");
	exit();
}

$sec = hash_pad($row["editsecret"]);
if ($md5 != md5($sec))
	httperr();

mysql_query("UPDATE users SET status='confirmed', editsecret='' WHERE id=$id AND status='pending'");

if (!mysql_affected_rows())
	httperr();

logincookie($id, $row["passhash"]);

header("Refresh: 0; url=../../ok.php?type=confirm");

hit_end();

?>