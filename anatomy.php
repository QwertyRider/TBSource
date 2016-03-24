<?php

require "include/bittorrent.php";

dbconn(false);
stdhead();
?>
<table class=main width=750 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<h2>Anatomy of a torrent session </h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text> 

<em>(Updated to reflect the tracker changes. 14-04-2004)</em>

<br><br>
There seems to be a lot of confusion about how the statistics updates work. The following is a capture of a full
session to see what's going on behind the scenes. The client communicates with the tracker via simple http GET commands. The very first in this case was:<br>
<br>
<code>GET /announce.php?info_hash=c%97%91%C5jG%951%BE%C7M%F9%BFa%03%F2%2C%ED%EE%0F& peer_id=S588-----gqQ8TqDeqaY&port=6882&uploaded=0&downloaded=0&left=753690875 &event=started</code><br>
<br>
Let's dissect this:<br>
<br>
• <b>info_hash</b> is just the hash identifying the torrent in question;<br>
• <b>peer_id</b>, as the name suggests, identifies the client (the s588 part identifies Shad0w's 5.8.8, the rest is random);<br>
• <b>port</b> just tells the tracker which port the client will listen to for incoming connections;<br>
• <b>uploaded</b>=0; (this and the following are the relevant ones, and are self-explanatory)<br>
• <b>downloaded</b>=0;<br>
• <b>left</b>=753690875 (how much left); <br>
• <b>event=started</b> (telling the tracker that the client has just started).<br>
<br>
Notice that the client IP doesn't show up here (although it can be sent by the client if it configured to do so). 
It's up to the tracker to see it and associate it with the user_id.<br>
(Server replies will be omitted, they're just lists of peer ips and respective ports.)<br>
At this stage the user's profile will be listing this torrent as being leeched.<br>
<br>
>From now on the client will keep send GETs to the tracker. We show only the first one as an example,
<br>
<br>
<code> GET /announce.php?info_hash=c%97%91%C5jG%951%BE%C7M%F9%BFa%03%F2%2C%ED%EE%0F& peer_id=S588-----gqQ8TqDeqaY&port=6882&uploaded=67960832&downloaded=40828928& left=715417851&numwant=0</code><br>
<br>
("numwant" is how the client tells the tracker how many new peers it wants, in this case 0.)
<br>
<br>
As you can see at this stage the user had uploaded approx. 68MB and downloaded approx. 40MB. Whenever the tracker receives
these GETs it updates both the stats relative to the 'currently leeching/seeding' boxes and the total user upload/download stats. These intermediate GETs will be sent either periodically (every 15 min
or so, depends on the client and tracker) or when you force a manual announce in the client.
<br>
<br>
Finally, when the client was closed it sent
<br>
<br>
<code> GET /announce.php?info_hash=c%97%91%C5jG%951%BE%C7M%F9%BFa%03%F2%2C%ED%EE%0F& peer_id=S588-----gqQ8TqDeqaY&port=6882&uploaded=754384896&downloaded=754215163 &left=0&numwant=0&event=completed</code><br>
<br>
Notice the all-important "event=completed". It is at this stage that the torrent will be removed from the user's profile.
If for some reason (tracker down, lost connection, bad client, crash, ...) this last GET doesn't reach
the tracker this torrent will still be seen in the user profile until some tracker timeout occurs. It should be stressed that this message will be sent only when
closing the client properly, not when the download is finished. (The tracker will start listing
a torrent as 'currently seeding' after it receives a GET with left=0). <br>
<br>
There's a further message that causes the torrent to be removed from the user's profile, 
namely"event=stopped". This is usually sent 
when stopping in the middle of a download, e.g. by pressing 'Cancel' in Shad0w's. <br>
<br>
One last note: some clients have a pause/resume option. This will <b>not</b> send any message to the server. 
Do not use it as a way of updating stats more often, it just doesn't work. (Checked for Shad0w's 5.8.11 and ABC 2.6.5.)
<br>
</td></tr></table>
</td></tr></table>
<br>
<?
stdfoot();
?>