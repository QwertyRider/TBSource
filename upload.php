<?

require_once("include/bittorrent.php");
hit_start();

dbconn(false);

hit_count();

loggedinorreturn();

stdhead("Upload");

if (get_user_class() < UC_UPLOADER)
{
  stdmsg("Sorry...", "You are not authorized to upload torrents.  (See <a href=\"faq.php#up\">Uploading</a> in the FAQ.)");
  stdfoot();
  exit;
}

?>
<div align=Center>
<form enctype="multipart/form-data" action="takeupload.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_torrent_size?>" />
<p>The tracker's announce url is <b><?= $announce_urls[0] ?></b></p>
<table border="1" cellspacing="0" cellpadding="10">
<?

tr("Torrent file", "<input type=file name=file size=80>\n", 1);
tr("Torrent name", "<input type=\"text\" name=\"name\" size=\"80\" /><br />(Taken from filename if not specified. <b>Please use descriptive names.</b>)\n", 1);
tr("NFO file", "<input type=file name=nfo size=80><br>(<b>Required.</b> Can only be viewed by power users.)\n", 1);
tr("Description", "<textarea name=\"descr\" rows=\"10\" cols=\"80\"></textarea>" .
  "<br>(HTML/BB code is <b>not</b> allowed.)", 1);

$s = "<select name=\"type\">\n<option value=\"0\">(choose one)</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
tr("Type", $s, 1);

?>
<tr><td align="center" colspan="2"><input type="submit" class=btn value="Do it!" /></td></tr>
</table>
</form>
<?

stdfoot();

hit_end();

?>