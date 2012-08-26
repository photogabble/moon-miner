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
// File: includes/request_var.php

function request_var ($type = null, $name = null, &$value = null)
{
    $value = null;
    if(is_null($type) || is_null($name))
    {
        return (boolean) false;
    }

    switch(strtoupper($type))
    {
        case "GET":
        {
            if (isset($_GET[$name]) && strlen($_GET[$name]) > 0)
            {
                $value = $_GET[$name];

                return (boolean) true;
            }
            break;
        }

        case "POST":
        {
            if (isset($_POST[$name]) && strlen($_POST[$name]) > 0)
            {
                $value = $_POST[$name];

                return (boolean) true;
            }
            break;
        }

        case "REQUEST":
        {
            if (isset($_REQUEST[$name]) && strlen($_REQUEST[$name]) > 0)
            {
                $value = $_REQUEST[$name];

                return (boolean) true;
            }
            break;
        }

        case "COOKIE":
        {
            if (isset($_COOKIE[$name]) && strlen($_COOKIE[$name]) > 0)
            {
                $value = $_COOKIE[$name];

                return (boolean) true;
            }
            break;
        }

        case "SESSION":
        {
            if (isset($_SESSION[$name]) && strlen($_SESSION[$name]) > 0)
            {
                $value = $_SESSION[$name];

                return (boolean) true;
            }
            break;
        }

        case "SERVER":
        {
            if (isset($_SERVER[$name]) && strlen($_SERVER[$name]) > 0)
            {
                $value = $_SERVER[$name];

                return (boolean) true;
            }
            break;
        }

        case "CONFIG":
        {
//            if (isset($_SESSION[$name]) && strlen($_SESSION[$name]) > 0)
//            {
//                $value = $_SESSION[$name];
//                return (boolean) true;
//            }
            return (boolean) false;
            break;
        }

        default:
        {
            return (boolean) false;
            break;
        }
    }

    return (boolean) false;
}

?>
