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
.footer { clear: both; height: 4em}
.portcosts1 { background-color:#300030; border-style:none; color:#c0c0c0; font-size:1em; width:7em}
.portcosts2 { background-color:#400040; border-style:none; color:#c0c0c0; font-size:1em; width:7em}
a:active { color: #f00}
a:link { color: #0f0}
a.new_link { color:#0f0; font-size: 8pt; font-weight:bold}
a.new_link:hover { color:#36f; font-size: 8pt; font-weight:bold}
a:visited { color: #0f0}
body { background-color:#000; background-image: url('../images/bgoutspace1.png'); color:#c0c0c0; font-family: Verdana, "DejaVu Sans", sans-serif; font-size: 85%; line-height:1.125em; height: 100%}
body.port table { border:0; border-spacing:0px; color:#fff; width:100%}
body.port td { font-size:1.1em; padding:0px}
body.port th { background-color:#500050; text-align:left}
body.port tr:nth-child(even) { background-color:#300030}
body.port tr:nth-child(odd) { background-color:#400040}
html { height: 85%}
