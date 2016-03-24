<?
require "include/bittorrent.php";
hit_start();
dbconn();
loggedinorreturn();
hit_count();
stdhead("Search");
?>
<table width=750 class=main border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<p>(this page is under construction)</p>
<form method="get" action=browse.php>
<p align="center">
Search:
<input type="text" name="search" size="40" value="<?= htmlspecialchars($searchstr) ?>" />
in
<select name="cat">
<option value="0">(all types)</option>
<?


$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
    $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
    if ($cat["id"] == $_GET["cat"])
        $catdropdown .= " selected=\"selected\"";
    $catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
}

$deadchkbox = "<input type=\"checkbox\" name=\"incldead\" value=\"1\"";
if ($_GET["incldead"])
    $deadchkbox .= " checked=\"checked\"";
$deadchkbox .= " /> including dead torrents\n";

?>
<?= $catdropdown ?>
</select>
<?= $deadchkbox ?>
<input type="submit" value="Search!" />
</p>
</form>
</td></tr></table>
<?
hit_end();
stdfoot();

?>