<?

require_once("include/bittorrent.php");

hit_start();

dbconn(false);

hit_count();

loggedinorreturn();

stdhead($CURUSER["username"] . "'s torrents");

$where = "WHERE owner = " . $CURUSER["id"] . " AND banned != 'yes'";
$res = mysql_query("SELECT COUNT(*) FROM torrents $where");
$row = mysql_fetch_array($res);
$count = $row[0];

if (!$count) {
?>
<h1>No torrents</h1>
<p>You haven't uploaded any torrents yet, so there's nothing in this page.</p>
<?
}
else {
	list($pagertop, $pagerbottom, $limit) = pager(20, $count, "mytorrents.php?");

	$res = mysql_query("SELECT torrents.type, torrents.comments, torrents.leechers, torrents.seeders, IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.id, categories.name AS cat_name, categories.image AS cat_pic, torrents.name, save_as, numfiles, added, size, views, visible, hits, times_completed, category FROM torrents LEFT JOIN categories ON torrents.category = categories.id $where ORDER BY id DESC $limit");

	print($pagertop);

	torrenttable($res, "mytorrents");

	print($pagerbottom);
}

stdfoot();

hit_end();

?>
