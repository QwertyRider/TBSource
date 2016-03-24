<?
require "include/bittorrent.php";

dbconn();

loggedinorreturn();

$search = trim($HTTP_GET_VARS['search']);
$class = $HTTP_GET_VARS['class'];
if ($class == '-' || !is_valid_id($class))
  $class = '';

if ($search != '' || $class)
{
  $query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
	if ($search)
		  $q = "search=" . htmlspecialchars($search);
}
else
{
	$letter = trim($_GET["letter"]);
  if (strlen($letter) > 1)
    die;

  if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false)
    $letter = "a";
  $query = "username LIKE '$letter%' AND status='confirmed'";
  $q = "letter=$letter";
}

if ($class)
{
  $query .= " AND class=$class";
  $q .= ($q ? "&amp;" : "") . "class=$class";
}

stdhead("Users");

print("<h1>Users</h1>\n");

print("<form method=get action=?>\n");
print("Search: <input type=text size=30 name=search>\n");
print("<select name=class>\n");
print("<option value='-'>(any class)</option>\n");
for ($i = 0;;++$i)
{
	if ($c = get_user_class_name($i))
	  print("<option value=$i" . ($class && $class == $i ? " selected" : "") . ">$c</option>\n");
	else
	  break;
}
print("</select>\n");
print("<input type=submit value='Okay'>\n");
print("</form>\n");

print("<p>\n");

for ($i = 97; $i < 123; ++$i)
{
	$l = chr($i);
	$L = chr($i - 32);
	if ($l == $letter)
    print("<b>$L</b>\n");
	else
    print("<a href=?letter=$l><b>$L</b></a>\n");
}

print("</p>\n");
  
$page = $_GET['page'];
$perpage = 100;

$res = mysql_query("SELECT COUNT(*) FROM users WHERE $query") or sqlerr();
$arr = mysql_fetch_row($res);
$pages = floor($arr[0] / $perpage);
if ($pages * $perpage < $arr[0])
  ++$pages;

if ($page < 1)
  $page = 1;
else
  if ($page > $pages)
    $page = $pages;

for ($i = 1; $i <= $pages; ++$i)
  if ($i == $page)
    $pagemenu .= "<b>$i</b>\n";
  else
    $pagemenu .= "<a href=?$q&page=$i><b>$i</b></a>\n";

if ($page == 1)
  $browsemenu .= "<b>&lt;&lt; Prev</b>";
else
  $browsemenu .= "<a href=?$q&page=" . ($page - 1) . "><b>&lt;&lt; Prev</b></a>";

$browsemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

if ($page == $pages)
  $browsemenu .= "<b>Next &gt;&gt;</b>";
else
  $browsemenu .= "<a href=?$q&page=" . ($page + 1) . "><b>Next &gt;&gt;</b></a>";

print("<p>$browsemenu<br>$pagemenu</p>");

$offset = ($page * $perpage) - $perpage;

$res = mysql_query("SELECT * FROM users WHERE $query ORDER BY username LIMIT $offset,$perpage") or sqlerr();
$num = mysql_num_rows($res);

print("<table border=1 cellspacing=0 cellpadding=5>\n");
print("<tr><td class=colhead align=left>User name</td><td class=colhead>Registered</td><td class=colhead>Last access</td><td class=colhead align=left>Class</td><td class=colhead>Country</td></tr>\n");
for ($i = 0; $i < $num; ++$i)
{
  $arr = mysql_fetch_assoc($res);
  if ($arr['country'] > 0)
  {
    $cres = mysql_query("SELECT name,flagpic FROM countries WHERE id=$arr[country]");
    if (mysql_num_rows($cres) == 1)
    {
      $carr = mysql_fetch_assoc($cres);
      $country = "<td style='padding: 0px' align=center><img src=pic/flag/$carr[flagpic] alt=\"$carr[name]\"></td>";
    }
  }
  else
    $country = "<td align=center>---</td>";
  if ($arr['added'] == '0000-00-00 00:00:00')
    $arr['added'] = '-';
  if ($arr['last_access'] == '0000-00-00 00:00:00')
    $arr['last_access'] = '-';
  print("<tr><td align=left><a href=userdetails.php?id=$arr[id]><b>$arr[username]</b></a>" .($arr["donated"] > 0 ? "<img src=pic/star.gif border=0 alt='Donor'>" : "")."</td>" .
  "<td>$arr[added]</td><td>$arr[last_access]</td>".
    "<td align=left>" . get_user_class_name($arr["class"]) . "</td>$country</tr>\n");
}
print("</table>\n");

print("<p>$pagemenu<br>$browsemenu</p>");

stdfoot();
die;

?>