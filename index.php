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

include "config.php";

if (empty($lang))
{
    $lang = $default_lang;
}

include "languages/$lang";
$title = $l_login_title;
$no_body = 1;
include "header.php";
?>

<body style="background-color:#929292; background-image:none; color:#c0c0c0;">
<img style="display:block; border:0; margin-left:auto; margin-right:auto;" src="images/bnt-header.jpg" width="517" height="189" alt="Blacknova Traders">
<img style="display:block; border:0; margin-left:auto; margin-right:auto;" class="div" src="images/div2.png" width="600" height="21" alt="">
<a class="index-imgswap-login" href="login.php"><div class="index-imgswap-login"><img src="images/login.png" width="146" height="58" alt="Login"></div></a>
<a class="index-imgswap-mail" href="mailto:<?php echo $admin_mail; ?>"><div class="index-imgswap-mail"><img id="mail" src="images/mail.png" width="146" height="58" alt="Mail"></div></a>
<a style="display:block; border:0; margin-left:auto; margin-right:auto; text-align:center" class="new_link" href="docs/faq.html"><?php echo "$l_faq"; ?></a>
<?php include "footer.php"; ?>
