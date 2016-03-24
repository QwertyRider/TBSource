<?
require "include/bittorrent.php";
dbconn();

function insert_tag($name, $description, $syntax, $example, $remarks)
{
	$result = format_comment($example);
	print("<p class=sub><b>$name</b></p>\n");
	print("<table class=main width=100% border=1 cellspacing=0 cellpadding=5>\n");
	print("<tr valign=top><td width=25%>Description:</td><td>$description\n");
	print("<tr valign=top><td>Syntax:</td><td><tt>$syntax</tt>\n");
	print("<tr valign=top><td>Example:</td><td><tt>$example</tt>\n");
	print("<tr valign=top><td>Result:</td><td>$result\n");
	if ($remarks != "")
		print("<tr><td>Remarks:</td><td>$remarks\n");
	print("</table>\n");
}

stdhead("Tags");
begin_main_frame();
begin_frame("Tags");
$test = $_POST["test"];
?>
<p>The TorrentBits forums supports a number of <i>BB tags</i> which you can embed to modify how your posts are displayed.</p>

<form method=post action=?>
<textarea name=test cols=60 rows=3><? print($test ? htmlspecialchars($test) : "")?></textarea>
<input type=submit value="Test this code!" style='height: 23px; margin-left: 5px'>
</form>
<?

if ($test != "")
  print("<p><hr>" . format_comment($test) . "<hr></p>\n");

insert_tag(
	"Bold",
	"Makes the enclosed text bold.",
	"[b]<i>Text</i>[/b]",
	"[b]This is bold text.[/b]",
	""
);

insert_tag(
	"Italic",
	"Makes the enclosed text italic.",
	"[i]<i>Text</i>[/i]",
	"[i]This is italic text.[/i]",
	""
);

insert_tag(
	"Underline",
	"Makes the enclosed text underlined.",
	"[u]<i>Text</i>[/u]",
	"[u]This is underlined text.[/u]",
	""
);

insert_tag(
	"Color (alt. 1)",
	"Changes the color of the enclosed text.",
	"[color=<i>Color</i>]<i>Text</i>[/color]",
	"[color=blue]This is blue text.[/color]",
	"What colors are valid depends on the browser. If you use the basic colors (red, green, blue, yellow, pink etc) you should be safe."
);

insert_tag(
	"Color (alt. 2)",
	"Changes the color of the enclosed text.",
	"[color=#<i>RGB</i>]<i>Text</i>[/color]",
	"[color=#0000ff]This is blue text.[/color]",
	"<i>RGB</i> must be a six digit hexadecimal number."
);

insert_tag(
	"Size",
	"Sets the size of the enclosed text.",
	"[size=<i>n</i>]<i>text</i>[/size]",
	"[size=4]This is size 4.[/size]",
	"<i>n</i> must be an integer in the range 1 (smallest) to 7 (biggest). The default size is 2."
);

insert_tag(
	"Font",
	"Sets the type-face (font) for the enclosed text.",
	"[font=<i>Font</i>]<i>Text</i>[/font]",
	"[font=Impact]Hello world![/font]",
	"You specify alternative fonts by separating them with a comma."
);

insert_tag(
	"Hyperlink (alt. 1)",
	"Inserts a hyperlink.",
	"[url]<i>URL</i>[/url]",
	"[url]http://torrentbits.org/[/url]",
	"This tag is superfluous; all URLs are automatically hyperlinked."
);

insert_tag(
	"Hyperlink (alt. 2)",
	"Inserts a hyperlink.",
	"[url=<i>URL</i>]<i>Link text</i>[/url]",
	"[url=http://torrentbits.org/]TorrentBits[/url]",
	"You do not have to use this tag unless you want to set the link text; all URLs are automatically hyperlinked."
);

insert_tag(
	"Image (alt. 1)",
	"Inserts a picture.",
	"[img=<i>URL</i>]",
	"[img=http://torrentbits.org/pic/logo.gif]",
	"The URL must end with <b>.gif</b>, <b>.jpg</b> or <b>.png</b>."
);

insert_tag(
	"Image (alt. 2)",
	"Inserts a picture.",
	"[img]<i>URL</i>[/img]",
	"[img]http://torrentbits.org/pic/logo.gif[/img]",
	"The URL must end with <b>.gif</b>, <b>.jpg</b> or <b>.png</b>."
);

insert_tag(
	"Quote (alt. 1)",
	"Inserts a quote.",
	"[quote]<i>Quoted text</i>[/quote]",
	"[quote]The quick brown fox jumps over the lazy dog.[/quote]",
	""
);

insert_tag(
	"Quote (alt. 2)",
	"Inserts a quote.",
	"[quote=<i>Author</i>]<i>Quoted text</i>[/quote]",
	"[quote=John Doe]The quick brown fox jumps over the lazy dog.[/quote]",
	""
);

insert_tag(
	"List",
	"Inserts a list item.",
	"[*]<i>Text</i>",
	"[*] This is item 1\n[*] This is item 2",
	""
);

insert_tag(
	"Preformat",
	"Preformatted (monospace) text. Does not wrap automatically.",
	"[pre]<i>Text</i>[/pre]",
	"[pre]This is preformatted text.[/pre]",
	""
);

end_frame();
end_main_frame();
stdfoot();
?>