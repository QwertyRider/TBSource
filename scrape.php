<?

require_once("include/bittorrent.php");
require_once("include/benc.php");

hit_start();

dbconn(false);

$r = "d" . benc_str("files") . "d";

$fields = "info_hash, times_completed, seeders, leechers";

if (!isset($_GET["info_hash"]))
	$query = "SELECT $fields FROM torrents ORDER BY info_hash";
else
	$query = "SELECT $fields FROM torrents WHERE " . hash_where("info_hash", $_GET["info_hash"]);

$res = mysql_query($query);

while ($row = mysql_fetch_assoc($res)) {
	$r .= "20:" . hash_pad($row["info_hash"]) . "d" .
		benc_str("complete") . "i" . $row["seeders"] . "e" .
		benc_str("downloaded") . "i" . $row["times_completed"] . "e" .
		benc_str("incomplete") . "i" . $row["leechers"] . "e" .
		"e";
}

$r .= "ee";

header("Content-Type: text/plain");
print($r);

hit_end();

?>
