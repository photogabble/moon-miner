<?php
if(extension_loaded('zlib'))
{
    ob_start('ob_gzhandler');
}

header("Content-type: text/css");
header("Vary: Accept-Encoding");
header("Connection: Keep-Alive");
header("Cache-Control: public");
?>
<!--
    var seconds = 0;
    var nextInterval = new Date().getTime();
    var maxTicks = 0;

    function NextUpdate()
    {
        var date = new Date();

        if (nextInterval <= 0)
        {
            nextInterval = date.getTime()+1000;
            Display();
        }
        else if (nextInterval <= date.getTime())
        {
            if (seconds <= 0)
            {
                seconds = maxTicks-1;
            }
            else
            {
                seconds -= 1;
            }

            Display();

            nextInterval = date.getTime() + 1000;
        }
        setTimeout("NextUpdate();", 100);
    }

    function Display()
    {
        if (seconds == 0)
        {
            document.getElementById('update_ticker').innerHTML = l_running_update;
        }
        else
        {
            document.getElementById('update_ticker').innerHTML = '~ '+ seconds +' '+ l_footer_until_update;
        }
    }
-->
<?php if(extension_loaded('zlib')){ob_end_flush();}?>