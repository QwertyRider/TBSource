<?

/*

Basic knowledge of how bencoding works is assumed. Details can be found
at <http://bitconjurer.org/BitTorrent/protocol.html>.



How to use these functions:

An "object" is defined to be an associative array with at least the keys
"type" and "value" present. The "type" key contains a string which is
one of "string", "integer", "list" or "dictionary". The "value" key
contains the appropriate thing, either a string, an integer, a list which
is just a flat array, or a dictionary, which is an associative array. In
the case of "list" and "dictionary", the values of the contained array
are agaib "objects".



Description of the functions:



string benc($obj);

Takes an object as argument and returns the bencoded form of it as string.
Returns the undefined/unset value on failure.

Examples:

benc(array(type => "string", value => "spam")) returns "4:spam".
benc(array(type => "integer", value => 3)) returns "i3e".
benc(array(type => "list", value => array(
array(type => "string", value => "spam"),
array(type => "string", value => "eggs")
)))
returns "l4:spam4:eggse"

benc(array(type => "dictionary", value => array(
cow => array(type => "string", value => "moo"),
spam => array(type => "string", value => "eggs"),
)))
returns "d3:cow3:moo4:spam4:eggse"




object bdec($str);

Returns the object that results from bdecoding the given string. Note
that those aren't real php objects, but merely "objects" as described
above. The returned objects have two additional keys: "string" and
"strlen". They represent the bencoded form of the returned objects, as
it was given in the original bencoded string. Use this to extract
certain portions of a bencoded string without having to re-encode it
(and avoiding possible re-ordering of dictionary keys). $x["strlen"]
is always equivalent to strlen($x["string"]). The "string" attribute
of the top-level returned object will be the same as the original
bencoded string, unless there's trailing garbage at the end of the
string.

This function returns the undefined/unset value on failure.

Example:

bdec("d4:spaml11:spiced pork3:hamee")
returns this monster:

Array
(
[type] => dictionary
[value] => Array
(
[spam] => Array
(
[type] => list
[value] => Array
(
[0] => Array
(
[type] => string
[value] => spiced pork
[strlen] => 14
[string] => 11:spiced pork
)

[1] => Array
(
[type] => string
[value] => ham
[strlen] => 5
[string] => 3:ham
)

)

[strlen] => 21
[string] => l11:spiced pork3:hame
)

)

[strlen] => 29
[string] => d4:spaml11:spiced pork3:hamee
)





object bdec_file($filename, $maxsize);

Opens the specified file, reads its contents (up to the specified length),
and returns whatever bdec() returns for those contents. This is a simple
convenience function.

*/

function benc($obj) {
if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
return;
$c = $obj["value"];
switch ($obj["type"]) {
case "string":
return benc_str($c);
case "integer":
return benc_int($c);
case "list":
return benc_list($c);
case "dictionary":
return benc_dict($c);
default:
return;
}
}

function benc_str($s) {
return strlen($s) . ":$s";
}

function benc_int($i) {
return "i" . $i . "e";
}

function benc_list($a) {
$s = "l";
foreach ($a as $e) {
$s .= benc($e);
}
$s .= "e";
return $s;
}

function benc_dict($d) {
$s = "d";
$keys = array_keys($d);
sort($keys);
foreach ($keys as $k) {
$v = $d[$k];
$s .= benc_str($k);
$s .= benc($v);
}
$s .= "e";
return $s;
}

function bdec_file($f, $ms) {
$fp = fopen($f, "rb");
if (!$fp)
return;
$e = fread($fp, $ms);
fclose($fp);
return bdec($e);
}

function bdec($s) {
if (preg_match('/^(\d+):/', $s, $m)) {
$l = $m[1];
$pl = strlen($l) + 1;
$v = substr($s, $pl, $l);
$ss = substr($s, 0, $pl + $l);
if (strlen($v) != $l)
return;
return array('type' => "string", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
}
if (preg_match('/^i(\d+)e/', $s, $m)) {
$v = $m[1];
$ss = "i" . $v . "e";
if ($v === "-0")
return;
if ($v[0] == "0" && strlen($v) != 1)
return;
return array('type' => "integer", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
}
switch ($s[0]) {
case "l":
return bdec_list($s);
case "d":
return bdec_dict($s);
default:
return;
}
}

function bdec_list($s) {
if ($s[0] != "l")
return;
$sl = strlen($s);
$i = 1;
$v = array();
$ss = "l";
for (;;) {
if ($i >= $sl)
return;
if ($s[$i] == "e")
break;
$ret = bdec(substr($s, $i));
if (!isset($ret) || !is_array($ret))
return;
$v[] = $ret;
$i += $ret["strlen"];
$ss .= $ret["string"];
}
$ss .= "e";
return array('type' => "list", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
}
//
function bdec_dict($s) {
if ($s[0] != "d")
return;
$sl = strlen($s);
$i = 1;
$v = array();
$ss = "d";
for (;;) {
if ($i >= $sl)
return;
if ($s[$i] == "e")
break;
$ret = bdec(substr($s, $i));
if (!isset($ret) || !is_array($ret) || $ret["type"] != "string")
return;
$k = $ret["value"];
$i += $ret["strlen"];
$ss .= $ret["string"];
if ($i >= $sl)
return;
$ret = bdec(substr($s, $i));
if (!isset($ret) || !is_array($ret))
return;
$v[$k] = $ret;
$i += $ret["strlen"];
$ss .= $ret["string"];
}
$ss .= "e";
return array('type' => "dictionary", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
}

?>
