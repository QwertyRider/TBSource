<?php
  $url = '';
  while (list($var,$val) = each($HTTP_GET_VARS))
    $url .= "&$var=$val";
$i = strpos($url, "&url=");
if ($i !== false)
	$url = substr($url, $i + 5);
if (substr($url, 0, 4) == "www.")
	$url = "http://" . $url;
  print("<html><head><meta http-equiv=refresh content='0;url=$url'></head><body>\n");
  print("<table border=0 width=100% height=100%><tr><td><h2 align=center>Redirecting you to:<br>\n");
  print("$url</h2></td></tr></table></body></html>\n");
?>