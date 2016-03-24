<?
require "include/bittorrent.php";
if (!local_user()) die;
dbconn();
$res = mysql_query("SELECT id,torrent FROM peers") or sqlerr();
$n = 0;
while ($arr =  mysql_fetch_assoc($res))
{
  $res2 = mysql_query("SELECT id FROM torrents WHERE id=" . $arr["torrent"]) or sqlerr();
  if (mysql_num_rows($res2) == 0)
    ++$n;
}
echo $n;
?>