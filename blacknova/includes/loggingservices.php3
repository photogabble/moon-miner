<?
function lognews($newstype,$action,$data)
{
    echo $newstype, " - ", $action, " - ", $data;
    mysql_query("INSERT INTO news (newstypes_id, action_id, newsdata) VALUES ('$newstype','$action','$data')");
}
?>
