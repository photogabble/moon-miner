<?

include("config.php3");

SetCookie("userpass","",0,$gamepath,$gamedomain);
SetCookie("userpass","",0); // Delete from default path as well.
setcookie("username","",0); // Legacy support, delete the old login cookies.
setcookie("password","",0); // Legacy support, delete the old login cookies.
setcookie("id","",0);
setcookie("res","",0);


$title = "Logout"; 

include("header.php3");

bigtitle();

echo "$username is now logged out.  Click <A HREF=index.php3>here</A> to return to game.";

include("footer.php3");

?>
