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
// File: index.php

include("config.php");

if(empty($lang))
{
    $lang = $default_lang;
}

include("languages/$lang");
$title=$l_login_title;
$interface="index.php";
$no_body=1;
include("header.php");
?>

<body style="background-color:#929292; color:#c0c0c0;" onLoad="MM_preloadImages('images/login_.gif','images/mail_.gif');">
<!--<div align="center">-->
<img style="display:block; border:0; margin-left:auto; margin-right:auto;" src="images/BNT-header.jpg" width="517" height="189" alt="Blacknova Traders">
<!-- <td colspan="3">-->

<table style="width:600px; border:0; border-spacing:0; margin-left:auto; margin-right:auto">
  <tr>
    <td><img style="border:0" id="div1" src="images/div1.gif" width="600" height="21" alt=""></td>
    <td><img style="border:0" src="images/spacer.gif" width="1" height="21"  alt=""></td>
  </tr>
  <tr>
    <td><img style="border:0" id="bnthed" src="images/bnthed.gif" width="600" height="61" alt="Blacknova Traders"></td>
    <td><img style="border:0" src="images/spacer.gif" width="1" height="61"  alt=""></td>
  </tr>
  <tr>
    <td><img style="border:0" id="div2" src="images/div2.gif" width="600" height="21" alt=""></td>
    <td><img style="border:0" src="images/spacer.gif" width="1" height="21"  alt=""></td>
  </tr>
  <tr>
    <td style="text-align:center"><a href="login.php" onMouseOut="MM_swapImgRestore()"  onMouseOver="MM_swapImage('login','','images/login_.gif',1);" ><img style="border:0" id="login" src="images/login.gif" width="146" height="58" alt="Login"></a></td>
    <td><img style="border:0" src="images/spacer.gif" width="1" height="58"  alt=""></td>
  </tr>
  <tr>
    <td style="text-align:center"><a href="mailto:<?php echo $admin_mail; ?>" onMouseOut="MM_swapImgRestore()"  onMouseOver="MM_swapImage('mail','','images/mail_.gif',1);" ><img style="border:0" id="mail" src="images/mail.gif" width="146" height="58" alt="Mail"></a></td>
    <td><img style="border:0" src="images/spacer.gif" width="1" height="58"  alt=""></td>
  </tr>
  <tr>
    <td style="text-align:center"><a class="new_link" href="docs/faq.html"><?php echo "$l_faq"; ?></a></td>
    <td></td>
  </tr>
  </table>
<!--</div>-->
<?php
    include("footer.php");
?>
