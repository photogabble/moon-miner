<? header("Cache-Control: no-cache, must-revalidate");
// Comment out the line below if you are running php 4.0.6 or earlier
ob_start("ob_gzhandler");

?>
<!doctype html public "-//w3c//dtd html 3.2//en">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<HTML>

<HEAD>
<TITLE><? echo $title; ?></TITLE>
<STYLE TYPE="text/css">
<!--
<?
if($interface == "")
{
  $interface = "main.php";
}

if($interface == "main.php")
{
	echo "
	a.mnu {text-decoration:none; font-size: $stylefontsize; font-family: verdana; color:white; font-weight:bold;}
	a.mnu:hover {text-decoration:none; font-size: $stylefontsize; font-family: verdana; color:#3366ff; font-weight:bold;}
	div.mnu {text-decoration:none; font-size: $stylefontsize; font-family: verdana; color:white; font-weight:bold;}
	a.dis {text-decoration:none; font-size: $stylefontsize; font-family: verdana; color:silver; font-weight:bold;}
	a.dis:hover {text-decoration:none; font-size: $stylefontsize; font-family: verdana; color:#3366ff; font-weight:bold;}
	";
}
echo "body {font-family: Arial, Tahoma, Helvetica, sans-serif; font-size: x-small}";
?>

-->
</STYLE>
</HEAD>

<?

if(empty($no_body))

{

  if($interface=="main.php")
  {
  	echo "<BODY BACKGROUND=\"images/bgoutspace1.gif\" bgcolor=#000000 text=\"#c0c0c0\" link=\"#00ff00\" vlink=\"#00ff00\" alink=\"#ff0000\">";
  }
  else
  {
  	echo "<BODY BACKGROUND=\"\" BGCOLOR=\"#000000\" TEXT=\"#c0c0c0\" LINK=\"#00ff00\" VLINK=\"#808080\" ALINK=\"#ff0000\">";
  }

}
echo "\n";

?>
