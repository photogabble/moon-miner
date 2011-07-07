<?php
include("config.php");
include("languages/$lang");

$title=$l_new_title;
include("header.php");

  bigtitle();
?>
<form action="new2.php" method="post">
 <center>
  <table  width="" border="0" cellspacing="0" cellpadding="4">
   <tr>
    <td><?php echo $l_login_email;?></td>
    <td><input type="text" name="username" size="20" maxlength="40" value=""></td>
   </tr>
   <tr>
    <td><?php echo $l_new_shipname; ?></td>
    <td><input type="text" name="shipname" size="20" maxlength="20" value=""></td>
   </tr>
   <tr>
    <td><?php echo $l_new_pname;?></td>
    <td><input type="text" name="character" size="20" maxlength="20" value=""></td>
   </tr>
  </table>
  <br>
  <input type="submit" value="<?php echo $l_submit;?>">
  <input type="reset" value="<?php echo $l_reset;?>">
  <br><br><?php echo $l_new_info;?><br>
 </center>
</form>

<?php include("footer.php"); ?>
