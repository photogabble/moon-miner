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
$title="Login";
$interface="index.php";
$no_body=1;
include("header.php");
?>

<body bgcolor="#666666" text="#c0c0c0" link="#000000" vlink="#990033" alink="#FF3333" onLoad="MM_preloadImages('images/login_.gif','images/mail_.gif');">
<center>
<img src="images/BNT-header.jpg" width="517" height="189" border="0" alt="BlackNova Traders">

<table border="0" cellpadding="0" cellspacing="0" width="600">
  <tr>
    <td colspan="3"><img name="div1" src="images/div1.gif" width="600" height="21" border="0" alt=""></td>
    <td><img src="images/spacer.gif" width="1" height="21" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="3"><img name="bnthed" src="images/bnthed.gif" width="600" height="61" border="0" alt="BlackNova Traders"></td>
    <td><img src="images/spacer.gif" width="1" height="61" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="3"><img name="div2" src="images/div2.gif" width="600" height="21" border="0" alt=""></td>
    <td><img src="images/spacer.gif" width="1" height="21" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan=3 align=center><a href=<?php echo "login.php"; ?> onMouseOut="MM_swapImgRestore()"  onMouseOver="MM_swapImage('login','','images/login_.gif',1);" ><img name="login" src="images/login.gif" width="146" height="58" border="0" alt="Login"></a></td>
    <td><img src="images/spacer.gif" width="1" height="58" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan=3 align=center><a href=<?php echo "\"mailto:$admin_mail\""; ?> onMouseOut="MM_swapImgRestore()"  onMouseOver="MM_swapImage('mail','','images/mail_.gif',1);" ><img name="mail" src="images/mail.gif" width="146" height="58" border="0" alt="Mail"></a></td>
    <td><img src="images/spacer.gif" width="1" height="58" border="0" alt=""></td>
  </tr>
  <tr>
  <td colspan=3 align=center><a href="docs/faq.html"><?php echo "$l_faq"; ?></a></td>
  </tr>
  </table></center>
<?php
    include("footer.php");
?>
