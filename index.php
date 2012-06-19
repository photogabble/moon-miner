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
$body_class = 'index';
include "header.php";
?>

<img class="index" src="images/bnt-header.jpg" width="517" height="189" alt="Blacknova Traders">
<img class="index" src="images/div2.png" width="600" height="21" alt="">
<div class="index-imgswap-login"><a class="index-imgswap-login" href="login.php"><img src="images/login.png" width="146" height="58" alt="Login"></a></div>
<div class="index-imgswap-mail"><a class="index-imgswap-mail" href="mailto:<?php echo $admin_mail; ?>"><img id="mail" src="images/mail.png" width="146" height="58" alt="Mail"></a></div>
<a class="index new_link" href="docs/faq.html"><?php echo "$l_faq"; ?></a>
<?php include "footer.php"; ?>
