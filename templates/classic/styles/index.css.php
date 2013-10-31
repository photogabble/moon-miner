<?php
require_once '../../../vendor/autoload.php';           // Load the auto-loader
ob_start (array('BntCompress', 'compress'));

$etag = md5_file (__FILE__); // Generate an md5sum and use it as the etag for the file, ensuring that caches will revalidate if the code itself changes
// header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 604800));
header ("Vary: Accept-Encoding");
header ("Content-type: text/css");
header ("Connection: Keep-Alive");
header ("Cache-Control: public");
header ('ETag: "' . $etag . '"');
?>
.button:active .shine { opacity: 0}
.button.blue { background: #3a617e}
.button.brown { background: #663300}
.button.gray { background: #555}
.button.green { background: #477343}
.button.orange { background: #624529}
.button.purple { background: #4b3f5e}
.button.red { background: #723131}
.button:hover .shine { left: 24px}
.cookie-warning { font-size:0.7em}
.footer { height: 4em; clear:both}
.index-flags { height:auto; left:80%; position:absolute; top:3%; width:auto}
.index-flags img { height:16px}
.index-h1 { font-size:1em; font-weight: normal; margin: 0; padding: 0}
.index-header { border:2px solid white; box-shadow: 3px 3px 6px #000; height: 150px; left:0; margin:2px; top: 0; width:99%}
.index-header-text { color:white; font-size:4em; height:auto; left:30%; line-height:4em; position:absolute; text-shadow: black 2px 2px 0.1em; top:1%; width:auto}
.index-welcome { font-size:1.2em; text-align:center}
a:active { color: #f00}
a:link { color: #0f0}
a.new_link { color:#0f0; font-size: 8pt; font-weight:bold}
a.new_link:hover { color:#36f; font-size: 8pt; font-weight:bold}
a { outline:none; text-decoration:none}
a:visited { color: #0f0}
body { background-color:#000; background-image: url('../images/bgoutspace1.png'); color:#c0c0c0; font-family: Verdana, "DejaVu Sans", sans-serif; font-size: 85%; line-height:1.125em; height: 100%}
body.index { background-color:#929292; background-image:none; color:#000; font-family: 'Ubuntu', Verdana, "DejaVu Sans", sans-serif; font-size:75%; text-align:center}
dd { float:left; height:2em; text-align:left; width:45%; padding:3px}
div.navigation { display:table; margin: 0 auto}
dt { float:left; height:2em; text-align:right; width:45%; padding:3px}
html { height: 85%}
img { border:0}
img.index { border:0; display:block; height:150px; margin-left:auto; margin-right:auto; width:100%}
li { display:inline}
ul.navigation { list-style:none}
.button {
    background: #434343;
    border: 1px solid #242424;
    color: #FFF;
    cursor: pointer;
    display: inline-block;
    font-size: 1em;
    letter-spacing: 1px;
    margin: 0 5px 5px 0;
    min-height: 1em;
    padding: 12px 24px;
    opacity: 0.9;
    text-shadow: 0 1px 2px rgba(0,0,0,0.9);
    text-transform: uppercase;

    -webkit-border-radius: 4px;
     -khtml-border-radius: 4px;
         -o-border-radius: 4px;
            border-radius: 4px;
    -webkit-box-shadow: rgba(255,255,255,0.25) 0 1px 0, inset rgba(255,255,255,0.25) 0 1px 0, inset rgba(0,0,0,0.25) 0 0 0, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
     -khtml-box-shadow: rgba(255,255,255,0.25) 0 1px 0, inset rgba(255,255,255,0.25) 0 1px 0, inset rgba(0,0,0,0.25) 0 0 0, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
         -o-box-shadow: rgba(255,255,255,0.25) 0 1px 0, inset rgba(255,255,255,0.25) 0 1px 0, inset rgba(0,0,0,0.25) 0 0 0, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
            box-shadow: rgba(255,255,255,0.25) 0 1px 0, inset rgba(255,255,255,0.25) 0 1px 0, inset rgba(0,0,0,0.25) 0 0 0, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
    -webkit-transition: all 0.1s linear;
     -khtml-transition: all 0.1s linear;
       -moz-transition: all 0.1s linear;
         -o-transition: all 0.1s linear;
            transition: all 0.1s linear;
}
.button:hover {
    -webkit-box-shadow: rgba(0,0,0,0.5) 0 2px 5px, inset rgba(255,255,255,0.25) 0 1px 0, inset rgba(0,0,0,0.25) 0 0 0, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
     -khtml-box-shadow: rgba(0,0,0,0.5) 0 2px 5px, inset rgba(255,255,255,0.25) 0 1px 0, inset rgba(0,0,0,0.25) 0 0 0, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
         -o-box-shadow: rgba(0,0,0,0.5) 0 2px 5px, inset rgba(255,255,255,0.25) 0 1px 0, inset rgba(0,0,0,0.25) 0 0 0, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
            box-shadow: rgba(0,0,0,0.5) 0 2px 5px, inset rgba(255,255,255,0.25) 0 1px 0, inset rgba(0,0,0,0.25) 0 0 0, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
}
.button:active {
    -webkit-box-shadow: rgba(255,255,255,0.25) 0 1px 0,inset rgba(255,255,255,0) 0 1px 0, inset rgba(0,0,0,0.5) 0 0 5px, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
     -khtml-box-shadow: rgba(255,255,255,0.25) 0 1px 0,inset rgba(255,255,255,0) 0 1px 0, inset rgba(0,0,0,0.5) 0 0 5px, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
         -o-box-shadow: rgba(255,255,255,0.25) 0 1px 0, inset rgba(255,255,255,0) 0 1px 0, inset rgba(0,0,0,0.5) 0 0 5px, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
            box-shadow: rgba(255,255,255,0.25) 0 1px 0, inset rgba(255,255,255,0) 0 1px 0, inset rgba(0,0,0,0.5) 0 0 5px, inset rgba(255,255,255,0.03) 0 20px 0, inset rgba(0,0,0,0.15) 0 -20px 20px, inset rgba(255,255,255,0.05) 0 20px 20px;
}
.shine {
    display: block;
    height: 1px;
    left: -24px;
    padding: 0 12px;
    position: relative;
    top: -12px;
    -webkit-box-shadow: rgba(255,255,255,0.2) 0 1px 5px;
     -khtml-box-shadow: rgba(255,255,255,0.2) 0 1px 5px;
         -o-box-shadow: rgba(255,255,255,0.2) 0 1px 5px;
            box-shadow: rgba(255,255,255,0.2) 0 1px 5px;
    -webkit-transition: all 0.3s ease-in-out;
     -khtml-transition: all 0.3s ease-in-out;
       -moz-transition: all 0.3s ease-in-out;
         -o-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
    background: -webkit-linear-gradient(to left, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 50%,rgba(255,255,255,0) 100%);
    background: -ms-linear-gradient(to left, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 50%,rgba(255,255,255,0) 100%);
    background: linear-gradient(to left, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 50%,rgba(255,255,255,0) 100%);
    background: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(255,255,255,0)), color-stop(50%,rgba(255,255,255,1)), color-stop(100%,rgba(255,255,255,0)));
}
