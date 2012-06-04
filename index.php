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
<img style="display:block; border:0; margin-left:auto; margin-right:auto;" src="images/BNT-header.jpg" width="517" height="189" alt="Blacknova Traders">
<img style="display:block; border:0; margin-left:auto; margin-right:auto;" class="div" src="images/div2.png" width="600" height="21" alt="">
<a href="login.php" onMouseOut="MM_swapImgRestore()"  onMouseOver="MM_swapImage('login','','images/login_.gif',1);" ><img style="display:block; border:0; margin-left:auto; margin-right:auto;" id="login" src="images/login.gif" width="146" height="58" alt="Login"></a>
<a href="mailto:<?php echo $admin_mail; ?>" onMouseOut="MM_swapImgRestore()"  onMouseOver="MM_swapImage('mail','','images/mail_.gif',1);" ><img style="display:block; border:0; margin-left:auto; margin-right:auto;" id="mail" src="images/mail.gif" width="146" height="58" alt="Mail"></a>
<a style="display:block; border:0; margin-left:auto; margin-right:auto; text-align:center" class="new_link" href="docs/faq.html"><?php echo "$l_faq"; ?></a>
<?php include("footer.php"); ?>
