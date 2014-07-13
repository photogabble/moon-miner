<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
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

$index_page = true;
require_once './common.php';

if (!isset($_GET['lang']))
{
    $_GET['lang'] = null;
    $lang = $bntreg->default_lang;
    $link = '';
}
else
{
    $lang = $_GET['lang'];
    $link = '?lang=' . $lang;
}

if (!Bnt\Db::isActive($pdo_db))
{
    // If DB is not active, redirect to CU to run install
    header('Location: create_universe.php');
    die();
}

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array ('main', 'login', 'logout', 'index', 'common','regional', 'footer','global_includes'));

$variables = null;
$variables['lang'] = $lang;
$variables['link'] = $link;
$variables['title'] = $langvars['l_welcome_bnt'];
$variables['link_forums'] = $bntreg->link_forums;
$variables['admin_mail'] = $bntreg->admin_mail;
$variables['body_class'] = 'index';

// Now Get a list of supported languages etc.
$sql = "SELECT section, name, value FROM {$pdo_db->prefix}languages WHERE category = :category AND (name = :name1 OR name = :name2) ORDER BY section, name;";
$stmt = $pdo_db->prepare($sql);
$stmt->bindValue(':category', 'regional');
$stmt->bindValue(':name1', 'local_lang_name');
$stmt->bindValue(':name2', 'local_lang_flag');
$stmt->execute();
$lang_rs = $stmt->fetchAll();

$list_of_langs = array();

if (is_array($lang_rs) === true && count($lang_rs) >= 2)
{
    foreach ($lang_rs as $id => $langinfo)
    {
        if (array_key_exists($langinfo['section'], $list_of_langs) === false)
        {
            $list_of_langs[$langinfo['section']] = array();
        }

        switch($langinfo['name'])
        {
            case 'local_lang_flag':
                $list_of_langs[$langinfo['section']] = array_merge($list_of_langs[$langinfo['section']], array('flag' => $langinfo['value']));
                break;

            case 'local_lang_name':
                $list_of_langs[$langinfo['section']] = array_merge($list_of_langs[$langinfo['section']], array('lang_name' => $langinfo['value']));
                break;
        }
    }

    // Extract our default language, and remove it from the list of supported languages.
    $our_lang = $list_of_langs[$lang];
    unset($list_of_langs[$lang]);

    // Add our default language back in, this should be put at the end of the list.
    $list_of_langs[$lang] = $our_lang;
    unset($our_lang);
}

// Add the games supported languages and unset no longer required variables.
$variables['list_of_langs'] = $list_of_langs;
unset($list_of_langs, $lang_rs);

$variables['template'] = $bntreg->default_template; // Temporarily set the template to the default template until we have a user option
// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = 'variable';
$langvars['container'] = 'langvars';

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->addVariables('langvars', $langvars);
$template->addVariables('variables', $variables);
$template->display('index.tpl');
?>
