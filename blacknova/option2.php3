<?


                if ($newpass1==$newpass2 && $password==$oldpass && $newpass1!="")
		{
			setcookie("username", $username);
			setcookie("password", $newpass1);
			setcookie("id", $id);
                } else {
                        $title="Password Problem";
                        include("header.php3");
                        echo "<center><H1>PASSWORD PROBLEM</H1><BR><BR>";
			echo "$password - $oldpass - $newpass1 - $newpass2<BR><BR>";
                        if ($password!=$oldpass) {echo "Original password incorrect!<BR><BR>";}
                        if ($newpass1!=$newpass2) {echo "New password did not match re-entered password.<BR><BR>";}
                        if ($newpass1=="" && $newpass2=="") {echo "Blank passwords are not allowed!<BR><BR>";}
                        echo "Click <a href=options.php3>here</a> to go back.";
                        include("footer.php3");
                        die();
                }

        $title="Change Password";
        include("header.php3");

	include("config.php3");


        connectdb();
	bigtitle();
        $result= mysql_query ("select * from ships where email='$username'");
        $playerinfo=mysql_fetch_array($result);
        If ($oldpass!=$playerinfo[password]) {echo "Original password incorrect!<BR><BR>"; die();}
        $result2 = mysql_query ("update ships set password='$newpass1' where ship_id=$playerinfo[ship_id]");

        if ($result2) {echo "Password has been changed.  Click <a href=main.php3>here</a> to continue.";}
        else {echo "Error changing password!";}

        include("footer.php3");

?>

