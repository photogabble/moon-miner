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
// File: plugins/test_plugin/plugin_loader.php

if (strpos ($_SERVER['PHP_SELF'], 'plugin_loader.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

if (isset ($plugin_config[$pluginname]) && $plugin_config[$pluginname]['enabled'] == true)
{
    class PluginTest extends Plugin
    {
        static $AppName                         = "Plugin Test";
        static $Version                         = "0.0.0 (0000) Alpha";
        static $Author                          = "TheMightyDude";
        static $isDisabled                      = true;
        static $usesEvents                      = true;
        static $moduleSupport                   = true;
        static $pluginVer                       = "3a";
        static $description                     = "displays hello world after every 5 seconds.";

        private $switches                       = NULL;

        function __construct()
        {
            global $plugin_config;

            $this->initialised                  = (boolean) false;
            $this->switches                     = $plugin_config['PluginTest'];
            $this->modules                      = array();
        }

        function __destruct()
        {
            // Need to put all unloading here.
        }

        public function Initialize($db = null)
        {
            // Need to put all Initialization stuff here along with all the Event Hooks.
            PluginSystem::AddEventHook(EVENT_TICK, $this);
        }

        function getPluginInfo($needModuleList = false)
        {
            $info = NULL;
            $info = get_class_vars("PluginTest");

            $info['switches'] = $this->switches;

            if (isset (self::$isDisabled)) $info['isDisabled'] = self::$isDisabled;
            $info['modules'] = NULL;

            return $info;
        }

        function OnEvent()
        {
            // This is called along with arguments only on the settings page.
            if (substr(strrchr($_SERVER['PHP_SELF'], DIRECTORY_SEPARATOR), 1) === "settings.php")
            {
                if (!isset ($_SESSION['plugin_data']['PluginTest']['last_run']))
                {
                    $_SESSION['plugin_data']['PluginTest']['last_run'] = time();
                }

                if (isset ($_SESSION['plugin_data']['PluginTest']['last_run']) && time() >= ($_SESSION['plugin_data']['PluginTest']['last_run'] + 5) )
                {
                    echo "Hello World ('". date(DATE_RFC822, implode("', '", func_get_args())) ."')<br />\n";
                    $_SESSION['plugin_data']['PluginTest']['last_run'] = time();
                }
            }
        }
    }
}

?>
