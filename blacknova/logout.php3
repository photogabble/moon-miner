<?
	setcookie("username");
	setcookie("password");

	setcookie("id");
	include("config.php3");

	$title="Logout"; 

	include("header.php3");

        bigtitle();

	echo "$username is now logged out.  Click <a href=index.php3>here</a> to return to game.";



	include("footer.php3");

?>
