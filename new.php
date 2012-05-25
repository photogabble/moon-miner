<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: new.php

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
