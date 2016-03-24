<?

require "include/bittorrent.php";
dbconn();
loggedinorreturn();

$maxfilesize = 256 * 1024;


if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST")
{
	$file = $HTTP_POST_FILES["file"];
	if (!isset($file) || $file["size"] < 1)
		stderr("Upload failed", "Nothing received!");
	if ($file["size"] > $maxfilesize)
		stderr("Upload failed", "Sorry, that file is too large for the bit-bucket.");
	$filename = $file["name"];
	if (strpos($filename, "..") !== false || strpos($filename, "/") !== false)
		stderr("Upload failed", "Bad file name.");
	$tgtfile = "bitbucket/$filename";
	if (file_exists($tgtfile))
		stderr("Upload failed", "Sorry, a file with the name <b>" . htmlspecialchars($filename) . "</b> already exists in the bit-bucket.");

	$it = @exif_imagetype($file["tmp_name"]);
	if ($it != IMAGETYPE_GIF && $it != IMAGETYPE_JPEG && $it != IMAGETYPE_PNG)
		stderr("Upload failed", "Sorry, the file you uploaded was not recognized as a valid image file.");

	$i = strrpos($filename, ".");
	if ($i !== false)
	{
		$ext = strtolower(substr($filename, $i));
		if (($it == IMAGETYPE_GIF && $ext != ".gif") || ($it == IMAGETYPE_JPEG && $ext != ".jpg") || ($it == IMAGETYPE_PNG && $ext != ".png"))
			stderr("Error", "Invalid file name extension: <b>$ext</b>");
	}
	else
		stderr("Error", "File name needs an extension.");
	move_uploaded_file($file["tmp_name"], $tgtfile) or stderr("Error", "Internal error 2.");
	$url = str_replace(" ", "%20", htmlspecialchars("$DEFAULTBASEURL/bitbucket/$filename"));
	stderr("Success", "Use the following URL to access the file: <b><a href=\"$url\">$url</a></b><p><a href=/bitbucket-upload>Upload another file</a>.");
}

stdhead("Bit-bucket upload");
?>
<h1>Bit-bucket upload</h1>
<form method=post action="bitbucket-upload" enctype="multipart/form-data">
<p><b>Maximum file size: <?=number_format($maxfilesize); ?> bytes.</b></p>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead>Upload file</td><td><input type=file name=file size=60></td></tr>
<tr><td colspan=2 align=center><input type=submit value="Upload" class=btn></td></tr>
</table>
</form>
<p>
<table class=main width=410 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<font class=small><b>Disclaimer:</b> Do not upload unauthorized or illegal pictures. Uploaded pictures should be considered "public domain"; do not upload pictures you wouldn't want a stranger to have access to.</font>
</td></tr></table>
<?
stdfoot();

?>