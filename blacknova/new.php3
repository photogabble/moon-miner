<?

include("extension.inc");
	$title="Create New Player"; 

	include("header.php3");
	include("config.php3");
        bigtitle();

?>

<form action="new2.php3" method="post">

<center>

<table  width="" border="0" cellspacing="0" cellpadding="4">

	<tr>

		<td >

			E-mail Address

		</td>

		<td >

			<input type="text" name="username" size="20" maxlength="40" value="">

		</td>

	</tr>

	<tr>

		<td >

			Ship Name

		</td>

		<td >

			<input type="text" name="shipname" size="20" maxlength="20" value="">

		</td>

	</tr>

	<tr>

		<td >

			Player Character Name

		</td>

		<td >

			<input type="text" name="character" size="20" maxlength="20" value="">

		</td>

	</tr>

</table>

<BR>

<input type="submit" value="submit"><input type="reset" value="reset">
<BR><BR>We promise not to give out (or sell) your e-mail address to anyone.  It is required though, to send you your server generated password..<BR>
</center>

</form>

<? 



	include("footer.php3");



?>
