<?php
require_once '../../../vendor/autoload.php';           // Load the auto-loader
ob_start (array('BntCompress', 'compress'));

$etag = md5_file (__FILE__); // Generate an md5sum and use it as the etag for the file, ensuring that caches will revalidate if the code itself changes
//header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 604800));
header ("Vary: Accept-Encoding");
header ("Content-type: text/css");
header ("Connection: Keep-Alive");
header ("Cache-Control: public");
header ('ETag: "' . $etag . '"');
?>
.faderlines { margin-left:auto; margin-right:auto; border:#fff solid 1px; text-align:center; background-color:#400040; color:#fff; padding:0px; border-spacing:0px; width:600px}
.footer, .push { clear: both}
.footer, .push { height: 4em}
.headlines { color:white; font-size:8pt; font-weight:bold; text-decoration:none}
.headlines:hover { color:#36f; text-decoration:none}
.map { background-color:#0000ff;  border:#555555 1px solid; color:#fff; float:left; height:20px; padding:0px; position:relative; width:20px}
.map:hover { border:#fff 1px solid}
.none { background-image:url('../../../images/space.png')}
.portcosts1 { background-color:#300030; border-style:none; color:#c0c0c0; font-size:1em; width:7em}
.portcosts2 { background-color:#400040; border-style:none; color:#c0c0c0; font-size:1em; width:7em}
.rank_dev_text { color:#f00; font-size:0.8em; text-decoration:none; vertical-align:middle}
.un { background-image:url('../../../images/uspace.png'); opacity:0.5}
.wrapper { min-height: 100%; height: auto !important; height: 100%; margin: 0 auto -4em}
a:active { color: #f00}
a.dis { color:silver; font-size: 8pt; font-weight:bold; text-decoration:none}
a.dis:hover { color:#36f; font-size: 8pt; font-weight:bold; text-decoration:none}
a.index { border:0; display:block; margin-left:auto; margin-right:auto; text-align:center}
a:link { color: #0f0}
a.new_link { color:#0f0; font-size: 8pt; font-weight:bold}
a.new_link:hover { color:#36f; font-size: 8pt; font-weight:bold}
a:visited { color: #0f0}
body { background-color:#000; background-image: url('../../../images/bgoutspace1.png'); color:#c0c0c0; font-family: Verdana, "DejaVu Sans", sans-serif; font-size: 85%; line-height:1.125em; height: 100%}
body.device table { border:0; border-spacing:0px}
body.device td { color:white; font-size:1.1em; padding:3px}
body.device th { background-color:#500050; text-align:left}
body.device tr:nth-child(even) { background-color:#300030}
body.device tr:nth-child(odd) { background-color:#400040}
body.error { background: url(../images/error.jpg) no-repeat center center fixed; -webkit-background-size: cover; -o-background-size: cover; background-size: cover}
body.faq a { color:#ffffff; text-decoration: none}
body.faq { color:#c0c0c0; font-size:14px; height:14px}
body.faq table { border:0px; width:100%; border-spacing:0px}
body.faq table.navbar { border-spacing:0px}
body.faq td.firstbar { background-color:#500050; color:#eeeeee; font-size:36px; height:36px; text-align:center}
body.faq td.header { background-color:#400040; color:#eeeeee; font-size:18px; font-weight:bold; height:18px; width:25%; text-align:center}
body.faq td.lists { text-align:center; width:20%}
body.faq td.secondbar { background-color:#400040; color:#eeeeee; font-size:14px; height:14px; text-align:center}
body.faq td.spacer { background-color:#300030; width:5%}
body.faq td.subheader { background-color:#400040; color:#eeeeee; font-size:16px; font-weight:bold; height:16px; width:90%}
body.igb { background-color:#929292; background-image:none; color:#f0f0f0; font-family: Courier New, Courier, monospace; font-size:0.8em}
body.index a { outline:none; text-decoration:none}
body.index { background-color:#929292; background-image:none; color:#000; font-family: 'Ubuntu', Verdana, "DejaVu Sans", sans-serif}
body.index { font-size: 75%; text-align: center}
body.index img { border:0}
body.log.a:active { color: #040658}
body.log.a:link { color: #040658}
body.log.a:visited { color: #040658}
body.log { background-color:#000; background-image: url('../../../images/bgoutspace1.png'); color:#c0c0c0}
body.options table { border:0; border-spacing:0px; color:#fff; padding:2px}
body.options th { background-color:#500050; text-align:left}
body.options tr:nth-child(even) { background-color:#300030}
body.options tr:nth-child(odd) { background-color:#400040}
body.port table { border:0; border-spacing:0px; color:#fff; width:100%}
body.port td { font-size:1.1em; padding:0px}
body.port th { background-color:#500050; text-align:left}
body.port tr:nth-child(even) { background-color:#300030}
body.port tr:nth-child(odd) { background-color:#400040}
body.zoneinfo table { border:1px solid white; border-spacing:0px; color: #fff; margin-left:20%; margin-right: 20%; padding:0px; width:60%}
body.zoneinfo td { font-size:1.1em}
body.zoneinfo td.name { width: 50%}
body.zoneinfo td.value { width: 50%}
body.zoneinfo td.zonename { text-align:center}
body.zoneinfo th { background-color:#500050; text-align:left}
body.zoneinfo tr:nth-child(even) { background-color:#300030}
body.zoneinfo tr:nth-child(odd) { background-color:#400040}
center.term { background-color: #000; border-color:#0f0; color: #0f0; font-size:0.8em}
div.error_content { float:right; text-align:left; width: 80%}
div.error_location { float:left; width: 20%}
div.error_text { background: rgb(0, 0, 0); background: rgba(0, 0, 0, 0.7); width:60%; margin: 0px auto; padding-left:1em}
div.igb { color:#0f0}
div.index-flags { height:auto; left:80%; position:absolute; top:3%; width:auto}
div.index-flags img { height:16px}
div.index-header { border:2px solid white; box-shadow: 3px 3px 6px #000; height: 150px; left:0px; margin:2px; top: 0px; width:99%}
div.index-header-text { color:white; font-size:4em; height:auto; left:30%; line-height:4em; position:absolute; text-shadow: black 2px 2px 0.1em; top:1%; width:auto}
div.index-welcome { font-size:1.2em; text-align:center}
div.mnu { color:white; font-size: 8pt; font-weight:bold; text-decoration:none}
div.navigation { display:table; margin: 0 auto}
dl.twocolumn-form dd { float:left; height:2em; text-align:left; width:45%; padding:3px}
dl.twocolumn-form dt { float:left; height:2em; text-align:right; width:45%; padding:3px}
dl.twocolumn-form input {width:200px}
h1.index-h1 { font-size:1em; font-weight: normal; margin: 0; padding: 0}
html.error { height: 100%}
html { height: 85%}
img.index { border:0; display:block; height:150px; margin-left:auto; margin-right:auto; width:100%}
img.mnu { border:transparent 2px dashed; padding:4px}
img.mnu:hover { border:#f00 2px dashed; padding:4px}
input.term { background-color: #000; border-color:#0f0; color: #0f0; font-size:0.8em}
p.cookie-warning { font-size:0.7em}
p.error_footer { clear:both}
p.error_return { }
p.error_text { }
pre.term { background-color: #000; border-color:#0f0; color: #0f0; font-size:0.8em}
select.term { background-color: #000; border-color:#0f0; color: #0f0; font-size:0.8em}
span.mnu { color:white; font-size: 8pt; font-weight:bold; text-decoration:none}
table.dis { color:silver; font-size: 8pt; font-weight:bold; text-decoration:none}
table.dis:hover { color:#36f; font-size: 8pt; font-weight:bold; text-decoration:none}
ul.navigation li { display:inline}
ul.navigation { list-style:none}
a.mnu { color:white; font-size: 8pt; font-weight:bold; text-decoration:none}
a.mnu:hover { color: #36f; font-size: 8pt; font-weight:bold; text-decoration:none}
