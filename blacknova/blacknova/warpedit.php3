<?
	include("config.php3");
	updatecookie();

	$title="Use Warp Editor";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}


	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
        bigtitle();
	if ($playerinfo[turns]<1)
	{
		echo "You need at least one turn to use a warp editor.<BR><BR>";
		echo "Click <a href=main.php3>here</a> to return to Main Menu.";
		include("footer.php3");		
		die();
	}

	$result2 = mysql_query ("SELECT * FROM links WHERE link_start=$playerinfo[sector]");
	if ($result2 < 1)
	{
		echo "There are no links out of this sector.<BR><BR>";
	} else {
		echo "Links lead from this sector to ";
		while ($row = mysql_fetch_array($result2))
		{
			echo "$row[link_dest] ";
		}
		echo "<BR><BR>";
	}
?>
<form action="warpedit2.php3" method="post">
<table>
	<tr><td>What sector would you like to create a link to?</td><td><input type="text" name="target_sector" size="6" maxlength="6" value=""></td></tr>
	<tr><td>One-way?</td><td><input type="checkbox" name="oneway" value="oneway"></td></tr>
</table>

<input type="submit" value="Submit"><input type="reset" value="Reset">
</form>
<BR><BR>Alternately, you may destroy a link to sector.<BR><BR>
<form action="warpedit3.php3" method="post">
<table>
	<tr><td>What sector would you like to remove a link to?</td><td><input type="text" name="target_sector" size="6" maxlength="6" value=""></td></tr>
	<tr><td>Both-ways?</td><td><input type="checkbox" name="bothway" value="bothway"></td></tr>
</table>

<input type="submit" value="Submit"><input type="reset" value="Reset">
</form>

<?
	echo "Click <a href=main.php3>here</a> to return to the main menu.";

	include("footer.php3");

?> 
