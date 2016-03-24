<?

require_once("include/bittorrent.php");

hit_start();

function bark($msg) {
	genbark($msg, "Edit failed!");
}

if (!mkglobal("id:name:descr:type"))
	bark("missing form data");

$id = 0 + $id;
if (!$id)
	die();

dbconn();

hit_count();

loggedinorreturn();

$res = mysql_query("SELECT owner, filename, save_as FROM torrents WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
	die();

if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
	bark("You're not the owner! How did that happen?\n");

$updateset = array();

$fname = $row["filename"];
preg_match('/^(.+)\.torrent$/si', $fname, $matches);
$shortfname = $matches[1];
$dname = $row["save_as"];

$nfoaction = $_POST['nfoaction'];
if ($nfoaction == "update")
{
  $nfofile = $_FILES['nfo'];
  if (!$nfofile) die("No data " . var_dump($_FILES));
  if ($nfofile['size'] > 65535)
    bark("NFO is too big! Max 65,535 bytes.");
  $nfofilename = $nfofile['tmp_name'];
  if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0)
    $updateset[] = "nfo = " . sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", file_get_contents($nfofilename)));
}
else
  if ($nfoaction == "remove")
    $updateset[] = "nfo = ''";

$updateset[] = "name = " . sqlesc($name);
$updateset[] = "search_text = " . sqlesc(searchfield("$shortfname $dname $torrent"));
$updateset[] = "descr = " . sqlesc($descr);
$updateset[] = "ori_descr = " . sqlesc($descr);
$updateset[] = "category = " . (0 + $type);
if ($CURUSER["admin"] == "yes") {
	if ($_POST["banned"]) {
		$updateset[] = "banned = 'yes'";
		$_POST["visible"] = 0;
	}
	else
		$updateset[] = "banned = 'no'";
}
$updateset[] = "visible = '" . ($_POST["visible"] ? "yes" : "no") . "'";

mysql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id");

write_log("Torrent $id ($name) was edited by $CURUSER[username]");

$returl = "details.php?id=$id&edited=1";
if (isset($_POST["returnto"]))
	$returl .= "&returnto=" . urlencode($_POST["returnto"]);
header("Refresh: 0; url=$returl");

hit_end();

?>
