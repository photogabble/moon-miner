<?

setcookie("username");
setcookie("password");
setcookie("id");
setcookie("res");

include("config.php3");

$title = "Logout"; 

include("header.php3");

bigtitle();

echo "$username is now logged out.  Click <A HREF=index.php3>here</A> to return to game.";

include("footer.php3");

?>
