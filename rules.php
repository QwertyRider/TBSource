<?

ob_start("ob_gzhandler");

require "include/bittorrent.php";

dbconn();

//loggedinorreturn();

stdhead("Rules");

begin_main_frame();

?>

<? begin_frame("General rules - <font color=#004E98>Breaking these rules can and will get you banned!</font>"); ?>
<ul>
<li>Do not defy the moderators expressed wishes!</li>
<li>Do not upload our torrents to other trackers! (See the <a href=faq.php#up3 class=altlink><b>FAQ</b></a> for details.)</li>
<li><a name="warning"></a>Disruptive behaviour in the forums or on the game server will result in a warning (<img src="pic/warned.gif"> ).<br>
You will only get <b>one</b> warning! After that it's bye bye Kansas!</li>

<? end_frame(); ?>
<? begin_frame("Downloading rules - <font color=#004E98>By not following these rules you will lose download privileges!</font>"); ?>
<ul>
<li>Access to the newest torrents is conditional on a good ratio! (See the <a href=faq.php#dl8 class=altlink><b>FAQ</b></a> for details.)</li>
<li>Low ratios may result in severe consequences, including banning in extreme cases.</li>
<? end_frame(); ?>
<? begin_frame("General Forum Guidelines - <font color=#004E98>Please follow these guidelines or else you might end up with a warning!</font>"); ?>
<ul>
<li>No aggressive behaviour or flaming in the forums.</li>
<li>No trashing of other peoples topics (i.e. SPAM).</li>
<li>No language other than English in the forums.</li>
<li>No systematic foul language (and none at all on  titles).</li>
<li>No links to warez or crack sites in the forums.</li>
<li>No requesting or posting of serials, CD keys, passwords or cracks in the forums.</li>
<li>No requesting if there has been no '<a href="http://www.nforce.nl/">scene</a>' release in the last 7 days.</li>
<li>No bumping... (All bumped threads will be deleted.)</li>
<li>No images larger than 800x600, and preferably web-optimised.</li>
<li>No double posting. If you wish to post again, and yours is the last post
in the thread please use the EDIT function, instead of posting a double.</li>
<li>Please ensure all questions are posted in the correct section!<br>
(Game questions in the Games section, Apps questions in the Apps section, etc.)</li>
<li>Last, please read the <a href=faq.php class=altlink><b>FAQ</b></a> before asking any questions!</li>
<? end_frame(); ?>
<? begin_frame("Avatar Guidelines - <font color=#004E98>Please try to follow these guidelines</font>"); ?>
<ul>
<li>The allowed formats are .gif, .jpg and .png.
<li>Be considerate. Resize your images to a width of 150 px and a size of no more than 150 KB.
(Browsers will rescale them anyway: smaller images will be expanded and will not look good;
larger images will just waste bandwidth and CPU cycles.) For now this is just a guideline but
it will be automatically enforced in the near future.
<li>Do not use potentially offensive material involving porn, religious material, animal / human
cruelty or ideologically charged images. Mods have wide discretion on what is acceptable.
If in doubt PM one.
<? end_frame(); ?>

<? if (get_user_class() >= UC_UPLOADER) { ?>

<? begin_frame("Uploading rules - <font color=#004E98>Torrents violating these rules may be deleted without notice</font>"); ?>
<ul>
<li>All uploads must include a proper NFO.</li>
<li>Only scene releases. If it's not on <a href=redir.php?url=http://www.nforce.nl class=altlink>NFOrce</a> or <a href=http://www.grokmusiq.com/ class=altlink>grokMusiQ</a> then forget it!</li>
<li>The stuff must not be older than seven (7) days.</li>
<li>All files must be in original format (usually 14.3 MB RARs).</li>
<li>Pre-release stuff should be labeled with an *ALPHA* or *BETA* tag.</li>
<li>Make sure not to include any serial numbers, CD keys or similar in the description (you do <b>not</b> need to edit the NFO!).</li>
<li>Make sure your torrents are well-seeded for at least 24 hours.</li>
<li>Do not include the release date in the torrent name.</li>
<li>Stay active! You risk being demoted if you have no active torrents.</li>
</ul>
<br>
<ul>
If you have something interesting that somehow violate these rules (e.g. not ISO format), ask a mod and we might make an exception.

<? end_frame(); ?>

<? } if (get_user_class() >= UC_MODERATOR) { ?>

<? begin_frame("Moderating rules - <font color=#004E98>Whom to promote and why</font>"); ?>
<br>
<table border=0 cellspacing=3 cellpadding=0>
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top width=80>&nbsp; <b>Power User</b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Automatically given to (and revoked from) users who have been members for at least 4 weeks, have uploaded at least
 	25 GB and have a share ratio above 1.05. Moderator changes of status last only until the next execution of the script.</td>
</tr>
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b><img src="pic/star.gif"></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>This status is given ONLY by Redbeard since he is the only one who can verify that they actually donated something.</td>
</tr>
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b>VIP</b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Conferred to users you feel contribute something special to the site. (Anyone begging for VIP status will be automatically disqualified)</td>
</tr>
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b>Other</b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Customised title given to special users only (Not available to Users or Power Users).</td>
</tr>
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b><font color="#4040c0">Uploader</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Appointed by Admins/SysOp. Send a PM to <a class=altlink href=sendmessage.php?receiver=2>RB<a> / <a class=altlink href=sendmessage.php?receiver=277>HS</a> / <a class=altlink href=sendmessage.php?receiver=2253>H</a> if you think you've got a good candidate.</td>
</tr>
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b><font color="#A83838">Moderator</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Appointed by Redbeard only. If you think you've got a good candidate,
	send him a <a class=altlink href=sendmessage.php?receiver=2>PM</a>.</td>
</tr>
</table>
<br>
<?
	end_frame();
	begin_frame("Moderating Rules - <font color=#004E98>Use your better judgement!</font>");
?>
<ul>
<li>The most important rule: Use your better judgment!</li>
<li>Don't be afraid to say <b>NO</b>! (a.k.a. "Helshad's rule".)
<li>Don't defy another mod in public, instead send a PM or through IM.</li>
<li>Be tolerant! Give the user(s) a chance to reform.</li>
<li>Don't act prematurely, let the users make their mistakes and THEN correct them.</li>
<li>Try correcting any "off topics" rather then closing a thread.</li>
<li>Move topics rather than locking them.</li>
<li>Be tolerant when moderating the Chit-chat section (give them some slack).</li>
<li>If you lock a topic, give a brief explanation as to why you're locking it.</li>
<li>Before you disable a user account, send him/her a PM and if they reply, put them on a 2 week trial.</li>
<li>Don't disable a user account until he or she has been a member for at least 4 weeks.</li>
<li><b>Always</b> state a reason (in the user comment box) as to why the user is being banned / warned.</li>
<br>

<?
	end_frame();
	begin_frame("Moderating options - <font color=#004E98>What are my privileges as a mod?</font>");
?>
<ul>
<li>You can delete and edit forum posts.</li>
<li>You can delete and edit torrents.</li>
<li>You can delete and change users avatars.</li>
<li>You can disable user accounts.</li>
<li>You can edit the title of VIP's.</li>
<li>You can see the complete info of all users.</li>
<li>You can add comments to users (for other mods and admins to read).</li>
<li>You can stop reading now 'cuz you already knew about these options. ;)</li>
<li>Lastly, check out the <a href=http://www.torrentbits.org/staff.php class=altlink>Staff</a> page (top right corner).</li>

<? end_frame(); ?>

<p align=right><font size=1 color=#004E98><b>Rules edited 2004-05-24 (03:41 GMT)</b></font></p>

<? }
end_main_frame();
stdfoot(); ?>