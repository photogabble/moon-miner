<? 
	$title="Login"; 
	include("header.php3");
	include("config.php3");
	echo "<CENTER>";
	bigtitle();
	echo "</CENTER>";
?>

<form action="login2.php3" method="post">
<center>
<BR><BR>If you get an error of "Can't connect to local MySQL" - it's because my hosting service's DB is down - again... sorry. :(<BR><BR>
<table  width="" border="0" cellspacing="0" cellpadding="4">
	<tr>
		<td >
			E-mail Address
		</td>
		<td >
			<input type="text" name="email" size="20" maxlength="40" value="<? echo $username; ?>">
		</td>
	</tr>

	<tr>
		<td >
			Password 
		</td>
		<td >
			<input type="password" name="pass" size="20" maxlength="20" value="<? echo $password; ?>">
		</td>
	</tr>

</table>
<br>
<input type="submit" value="Submit"><input type="reset" value="Reset"><br><br>

If you are a new player - click <a href=new.php3>here</a>.<br>
<BR>Problems?  <a href=mailto:<? echo "$admin_mail"; ?>>Email us.</a></center>



</form><CENTER><a href=http://blacknova.community.everyone.net/commun_v3/scripts/directory.pl target=_blank>Forums</a> - <a href=http://blacknova.net/ranking.php3>Rankings</a></CENTER><BR><BR>


<? 

	include("footer.php3");

?>
