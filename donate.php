<?
require "include/bittorrent.php";
dbconn();
stdhead();
?>

<b>Click the PayPal button below if you wish to make a donation!</b>

<p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="stefant@gbg.bonet.se">
<input type="hidden" name="item_name" value="Torrentbits.org">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="tax" value="0">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" border="0" name="submit"
alt="Make payments with PayPal - it's fast, free and secure!" style='margin-top: 5px'>
</form>
</p>

<p>
<? begin_main_frame(); begin_frame(); ?>
<table border=0 cellspacing=0 cellpadding=0><tr valign=top>
<td class=embedded>
<img src=pic/flag/sweden.gif style='margin-right: 10px'>
</td>
<td class=embedded>
E du svensk och inte har något kreditkort? Då kan du sätta in önskat belopp på följande konto:
<p>
Mottagare: PayPal Inc<br>
BankGiro Nummer: 5367-0527<br>
Bank: SE Banken, Stockholm
</p>
<b>Viktigt:</b> I fältet för "meddelande till betalningsmottagaren" (eller motsvarande) skriv: <b>qng9s57h9yenn</b>
</td>
</tr></table>
<? end_frame(); begin_frame("Other ways to donate"); ?>
No other ways at the moment...
<? end_frame(); end_main_frame(); ?>
</p>

<b>After you have donated -- make sure to <a href=sendmessage.php?receiver=2>send us</a> the <font color=red>transaction id</font> so we can credit your account!</b>
<?
stdfoot();
?>