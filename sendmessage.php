<?php
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

// Standard Administrative PM Replies
$pm_std_reply[1] = "Read the bloody [url=http://torrentbits.org/faq.php]FAQ[/url] and stop bothering me!";
$pm_std_reply[2] = "Die! Die! Die!";

// Standard Administrative PMs
$pm_template['1'] = array("Ratio warning","Hi,\n
You may have noticed, if you have visited the forum, that TB is disabling the accounts of all users with low share ratios.\n
I am sorry to say that your ratio is a little too low to be acceptable.\n
If you would like your account to remain open, you must ensure that your ratio increases dramatically in the next day or two, to get as close to 1.0 as possible.\n
I am sure that you will appreciate the importance of sharing your downloads.
You may PM any Moderator, if you believe that you are being treated unfairly.\n
Thank you for your cooperation.");
$pm_template['2'] = array("Avatar warning", "Hi,\n
You may not be aware that there are new guidelines on avatar sizes in the [url=http://torrentbits.org/rules.php]rules[/url], in particular \"Resize
your images to a width of 150 px and a size of [b]no more than 150 KB[/b].\"\n
I'm sorry to say your avatar doesn't conform to them. Please change it as soon as possible.\n
We understand this may be an inconvenience to some users but feel it is in the community's best interest.\n
Thanks for the cooperation.");

// Standard Administrative MMs
$mm_template['1'] = $pm_template['1'];
$mm_template['2'] = array("Downtime warning","We'll be down for a few hours");
$mm_template['3'] = array("Change warning","The tracker has been updated. Read
the forums for details.");

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{						          ////////  MM  //
	if (get_user_class() < UC_MODERATOR)
		stderr("Error", "Permission denied");

  $n_pms = $_POST['n_pms'];
  $pmees = $_POST['pmees'];
  $auto = $_POST['auto'];

  if ($auto)
  	$body=$mm_template[$auto][1];

  stdhead("Send message", false);
	?>
  <table class=main width=750 border=0 cellspacing=0 cellpadding=0>
	<tr><td class=embedded><div align=center>
	<h1>Mass Message to <?=$n_pms?> user<?=($n_pms>1?"s":"")?>!</h1>
	<form method=post action=takemessage.php>
	<? if ($_SERVER["HTTP_REFERER"]) { ?>
	<input type=hidden name=returnto value=<?=$_SERVER["HTTP_REFERER"]?>>
	<? } ?>
	<table border=1 cellspacing=0 cellpadding=5>
	<tr><td colspan="2"><div align="center">
	<textarea name=msg cols=80 rows=15><?=$body?></textarea>
	</div></td></tr>
	<tr><td colspan="2"><div align="center"><b>Comment:&nbsp;&nbsp;</b>
  <input name="comment" type="text" size="70">
	</div></td></tr>
  <tr><td><div align="center"><b>From:&nbsp;&nbsp;</b>
	<?=$CURUSER['username']?>
	<input name="sender" type="radio" value="self" checked>
	&nbsp; System
	<input name="sender" type="radio" value="system">
	</div></td>
  <td><div align="center"><b>Take snapshot:</b>&nbsp;<input name="snap" type="checkbox" value="1">
  </div></td></tr>
	<tr><td colspan="2" align=center><input type=submit value="Send it!" class=btn>
	</td></tr></table>
	<input type=hidden name=pmees value="<?=$pmees?>">
	<input type=hidden name=n_pms value=<?=$n_pms?>>
	</form><br><br>
	<form method=post action=<?=$_SERVER['PHP_SELF']?>>
	<table border=1 cellspacing=0 cellpadding=5>
	<tr><td>
	<b>Templates:</b>
	<select name="auto">
	<?
	for ($i = 1; $i <= count($mm_template); $i++)	{
		echo "<option value=$i ".($auto == $i?"selected":"").
    		">".$mm_template[$i][0]."</option>\n";}
  ?>
	</select>
	<input type=submit value="Use" class=btn>
	</td></tr></table>
	<input type=hidden name=pmees value="<?=$pmees?>">
	<input type=hidden name=n_pms value=<?=$n_pms?>>
	</form></div></td></tr></table>
  <?
} else {                                                        ////////  PM  //
	$receiver = $_GET["receiver"];
	if (!is_valid_id($receiver))
	  die;

	$replyto = $_GET["replyto"];
	if ($replyto && !is_valid_id($replyto))
	  die;

	$auto = $_GET["auto"];
	$std = $_GET["std"];

	if (($auto || $std ) && get_user_class() < UC_MODERATOR)
	  die("Permission denied.");

	$res = mysql_query("SELECT * FROM users WHERE id=$receiver") or die(mysql_error());
	$user = mysql_fetch_assoc($res);
	if (!$user)
	  die("No user with that ID.");

  if ($auto)
 		$body = $pm_std_reply[$auto];
  if ($std)
		$body = $pm_template[$std][1];

	if ($replyto)
	{
	  $res = mysql_query("SELECT * FROM messages WHERE id=$replyto") or sqlerr();
	  $msga = mysql_fetch_assoc($res);
	  if ($msga["receiver"] != $CURUSER["id"])
	    die;
	  $res = mysql_query("SELECT username FROM users WHERE id=" . $msga["sender"]) or sqlerr();
	  $usra = mysql_fetch_assoc($res);
	  $body .= "\n\n\n-------- $usra[username] wrote: --------\n$msga[msg]\n";
	}
	stdhead("Send message", false);
	?>
	<table class=main width=750 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
	<div align=center>
	<h1>Message to <a href=userdetails.php?id=<?=$receiver?>><?=$user["username"]?></a></h1>
	<form method=post action=takemessage.php>
	<? if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"]) { ?>
	<input type=hidden name=returnto value=<?=$_GET["returnto"] ? $_GET["returnto"] : $_SERVER["HTTP_REFERER"]?>>
	<? } ?>
	<table border=1 cellspacing=0 cellpadding=5>
	<tr><td<?=$replyto?" colspan=2":""?>><textarea name=msg cols=80 rows=15><?=$body?></textarea></td></tr>
	<tr>
	<? if ($replyto) { ?>
	<td align=center><input type=checkbox name='delete' value='yes' <?=$CURUSER['deletepms'] == 'yes'?"checked":""?>>Delete message you are replying to
	<input type=hidden name=origmsg value=<?=$replyto?>></td>
	<? } ?>
	<td align=center><input type=checkbox name='save' value='yes' <?=$CURUSER['savepms'] == 'yes'?"checked":""?>>Save message to Sentbox</td></tr>
	<tr><td<?=$replyto?" colspan=2":""?> align=center><input type=submit value="Send it!" class=btn></td></tr>
	</table>
	<input type=hidden name=receiver value=<?=$receiver?>>
	</form>
<!--
  <?
  if (get_user_class() >= UC_MODERATOR)
  {
  ?>
  	<br><br>
  	<form method=get action=<?=$_SERVER['PHP_SELF']?>>
	  <table border=1 cellspacing=0 cellpadding=5>
	  <tr><td>
	  <b>PM Templates:</b>
	  <select name="std"><?
	  for ($i = 1; $i <= count($pm_template); $i++)
	  {
	    echo "<option value=$i ".($std == $i?"selected":"").
	      ">".$pm_template[$i][0]."</option>\n";
	  }?>
	  </select>
		<? if ($_SERVER["HTTP_REFERER"]) { ?>
		<input type=hidden name=returnto value=<?=$_GET["returnto"]?$_GET["returnto"]:$_SERVER["HTTP_REFERER"]?>>
    <? } ?>
  	<input type=hidden name=receiver value=<?=$receiver?>>
		<input type=hidden name=replyto value=<?=$replyto?>>
	  <input type=submit value="Use" class=btn>
	  </td></tr></table></form>
	<?
  }
	?>
-->
 	</div></td></tr></table>
	<?
}
stdfoot();
?>