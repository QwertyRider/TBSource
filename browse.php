<?

ob_start("ob_gzhandler");

require_once("include/bittorrent.php");

hit_start();

dbconn(false);

loggedinorreturn();
hit_count();

$cats = genrelist();

$searchstr = unesc($_GET["search"]);
$cleansearchstr = searchfield($searchstr);
if (empty($cleansearchstr))
	unset($cleansearchstr);

$orderby = "ORDER BY torrents.id DESC";

$addparam = "";
$wherea = array();
$wherecatina = array();

if ($_GET["incldead"] == 1)
{
	$addparam .= "incldead=1&amp;";
	if (!isset($CURUSER) || get_user_class < UC_ADMINISTRATOR)
		$wherea[] = "banned != 'yes'";
}
elseif ($_GET["incldead"] == 2)
{
	$addparam .= "incldead=2&amp;";
		$wherea[] = "visible = 'no'";
}
	else
		$wherea[] = "visible = 'yes'";

$category = $_GET["cat"];

$all = $_GET["all"];

if (!$all)
	if (!$_GET && $CURUSER["notifs"])
	{
	  $all = True;
	  foreach ($cats as $cat)
	  {
	    $all &= $cat[id];
	    if (strpos($CURUSER["notifs"], "[cat" . $cat[id] . "]") !== False)
	    {
	      $wherecatina[] = $cat[id];
	      $addparam .= "c$cat[id]=1&amp;";
	    }
	  }
	}
	elseif ($category)
	{
	  if (!is_valid_id($category))
	    stderr("Error", "Invalid category ID $category.");
	  $wherecatina[] = $category;
	  $addparam .= "cat=$category&amp;";
	}
	else
	{
	  $all = True;
	  foreach ($cats as $cat)
	  {
	    $all &= $_GET["c$cat[id]"];
	    if ($_GET["c$cat[id]"])
	    {
	      $wherecatina[] = $cat[id];
	      $addparam .= "c$cat[id]=1&amp;";
	    }
	  }
	}

if ($all)
{
	$wherecatina = array();
  $addparam = "";
}

if (count($wherecatina) > 1)
	$wherecatin = implode(",",$wherecatina);
elseif (count($wherecatina) == 1)
	$wherea[] = "category = $wherecatina[0]";

$wherebase = $wherea;

if (isset($cleansearchstr))
{
	$wherea[] = "MATCH (search_text, ori_descr) AGAINST (" . sqlesc($searchstr) . ")";
	//$wherea[] = "0";
	$addparam .= "search=" . urlencode($searchstr) . "&amp;";
	$orderby = "";
}

$where = implode(" AND ", $wherea);
if ($wherecatin)
	$where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";

if ($where != "")
	$where = "WHERE $where";

$res = mysql_query("SELECT COUNT(*) FROM torrents $where") or die(mysql_error());
$row = mysql_fetch_array($res);
$count = $row[0];

if (!$count && isset($cleansearchstr)) {
	$wherea = $wherebase;
	$orderby = "ORDER BY id DESC";
	$searcha = explode(" ", $cleansearchstr);
	$sc = 0;
	foreach ($searcha as $searchss) {
		if (strlen($searchss) <= 1)
			continue;
		$sc++;
		if ($sc > 5)
			break;
		$ssa = array();
		foreach (array("search_text", "ori_descr") as $sss)
			$ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
		$wherea[] = "(" . implode(" OR ", $ssa) . ")";
	}
	if ($sc) {
		$where = implode(" AND ", $wherea);
		if ($where != "")
			$where = "WHERE $where";
		$res = mysql_query("SELECT COUNT(*) FROM torrents $where");
		$row = mysql_fetch_array($res);
		$count = $row[0];
	}
}

$torrentsperpage = $CURUSER["torrentsperpage"];
if (!$torrentsperpage)
	$torrentsperpage = 15;

if ($count)
{
	list($pagertop, $pagerbottom, $limit) = pager($torrentsperpage, $count, "browse.php?" . $addparam);
	$query = "SELECT torrents.id, torrents.category, torrents.leechers, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments,torrents.numfiles,torrents.filename,torrents.owner,IF(torrents.nfo <> '', 1, 0) as nfoav," .
//	"IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, categories.name AS cat_name, categories.image AS cat_pic, users.username FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
	"categories.name AS cat_name, categories.image AS cat_pic, users.username FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
	$res = mysql_query($query) or die(mysql_error());
}
else
	unset($res);
if (isset($cleansearchstr))
	stdhead("Search results for \"$searchstr\"");
else
	stdhead();

?>

<STYLE TYPE="text/css" MEDIA=screen>

  a.catlink:link, a.catlink:visited{
		text-decoration: none;
	}

	a.catlink:hover {
		color: #A83838;
	}

</STYLE>

<form method="get" action="browse.php">
<table class=bottom>
<tr>
<td class=bottom>
	<table class=bottom>
	<tr>

<?
$i = 0;
foreach ($cats as $cat)
{
	$catsperrow = 7;
	print(($i && $i % $catsperrow == 0) ? "</tr><tr>" : "");
	print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><div align=right><a class=catlink href=browse.php?cat={$cat["id"]}>" . htmlspecialchars($cat["name"]) . "</a><input name=c{$cat["id"]} type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) ? "checked " : "") . "value=1></div></td>\n");
	$i++;
}

$alllink = "<div align=left>(<a href=browse.php?all=1><b>Show all</b></a>)</div>";

$ncats = count($cats);
$nrows = ceil($ncats/$catsperrow);
$lastrowcols = $ncats % $catsperrow;

if ($lastrowcols != 0)
{
	if ($catsperrow - $lastrowcols != 1)
		{
			print("<td class=bottom rowspan=" . ($catsperrow  - $lastrowcols - 1) . ">&nbsp;</td>");
		}
	print("<td class=bottom style=\"padding-left: 5px\">$alllink</td>\n");
}
?>
	</tr>
	</table>
</td>

<td class=bottom>
<table class=main>
	<tr>
		<td class=bottom style="padding: 1px;padding-left: 10px">
			<select name=incldead>
<option value="0">active</option>
<option value="1"<? print($_GET["incldead"] == 1 ? " selected" : ""); ?>>including dead</option>
<option value="2"<? print($_GET["incldead"] == 2 ? " selected" : ""); ?>>only dead</option>
			</select>
  	</td>
<?
if ($ncats % $catsperrow == 0)
	print("<td class=bottom style=\"padding-left: 15px\" rowspan=$nrows valign=center align=right>$alllink</td>\n");
?>

  </tr>
  <tr>
  	<td class=bottom style="padding: 1px;padding-left: 10px">
  	<div align=center>
  		<input type="submit" class=btn value="Go!"/>
  	</div>
  	</td>
  </tr>
  </table>
</td>
</tr>
</table>
</form>

<br>
<table width=750 class=main border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
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

if (isset($cleansearchstr))
print("<h2>Search results for \"" . htmlspecialchars($searchstr) . "\"</h2>\n");

if ($count) {
	print($pagertop);

	torrenttable($res);

	print($pagerbottom);
}
else {
	if (isset($cleansearchstr)) {
		print("<h2>Nothing found!</h2>\n");
		print("<p>Try again with a refined search string.</p>\n");
	}
	else {
		print("<h2>Nothing here!</h2>\n");
		print("<p>Sorry pal :(</p>\n");
	}
}

stdfoot();

hit_end();
?>
