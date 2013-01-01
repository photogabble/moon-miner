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
// File: classes/eventsystem/event_system.php

require_once "event_list.php";

class EventSystem
{

    function __construct($parent = NULL){}
    function __destruct(){}

    static function Initialize()
    {
        $GLOBALS['events'] = array();
    }

    static function HookEvent($event = NULL, $callback_function = NULL)
    {
        if(!is_numeric($event))
        {
            $event = constant($event);
        }
        if(is_null($event))
        {
            return (boolean) false;
        }

        if (is_array($callback_function))
        {
            $callback_function[1] .= "_OnEvent";
            if(!method_exists($callback_function[0],$callback_function[1]))
            {
//              SYS::ErrorMessage(__FILE__, __FUNCTION__, __LINE__,"<br />\nFailed to Add Event Hook, Cannot find callback function '<span style='color:#00FF00;'>". get_class($callback_function[0]) ."::". $callback_function[1] ."(...)</span>'");
                echo "<pre>[".__FILE__.":".__FUNCTION__.":".__LINE__."]\n". print_r($GLOBALS['events'], true) ."</pre>\n";
                return (boolean) false;
            }
        }
        else
        {
            $callback_function .= "_OnEvent";
            // Need to add the hook to the event.
            if(!function_exists($callback_function))
            {
//              SYS::ErrorMessage(__FILE__, __FUNCTION__, __LINE__,"Failed to Add Event Hook function '{$callback_function}'.\n");
                echo "<pre>[".__FILE__.":".__FUNCTION__.":".__LINE__."]\n". print_r($GLOBALS['events'], true) ."</pre>\n";

                return (boolean) false;
            }
        }
        if (!isset($GLOBALS['events'][$event]))
        {
            $GLOBALS['events'][$event] = array();
        }
        array_push($GLOBALS['events'][$event], $callback_function);
        return (boolean) true;
    }

    static function ListEvent()
    {
//      SYS::debugMessage(__FILE__, __FUNCTION__, __LINE__,"<pre>[EventList]\n". print_r($GLOBALS['events'], true) ."</pre>\n");
        echo "<pre>[EventList]\n". print_r($GLOBALS['events'], true) ."</pre>\n";

    }

    static function GetEventHooks()
    {
        $hook_list = array();
//      list($hook) = func_get_args();

        foreach ($GLOBALS['events'] as $event_key => $hook_array)
        {
            $hooks = count($hook_array);
            for ($i=0; $i<$hooks; $i++)
            {
                $plugin_class = $hook_array[$i][0];

                while (method_exists($plugin_class, 'GetParent') === true)
                {
                    $plugin_class = call_user_func(array($plugin_class, 'GetParent'), true);
                }
                $plugin_class_name = get_class($plugin_class);

                if (array_key_exists($plugin_class_name, $hook_list) === false)
                {
                    $hook_list[$plugin_class_name] = array();
                }

                if (in_array($event_key , $hook_list[$plugin_class_name]) === false)
                {
                    array_push($hook_list[$plugin_class_name], $event_key);
                }
            }
        }

        if (func_num_args() >0)
        {
            list($hook) = func_get_args();
            if (array_key_exists($hook, $hook_list) === true)
            {
                return $hook_list[$hook];
            }
            else
            {
                return (boolean) false;
            }
        }
        else
        {
            return $hook_list;
        }
    }

    static function UnHookEvent()
    {
    }

    // This may be removed.
    static function GetEventName()
    {
        list($event) = func_get_args();

        switch($event)
        {
            case EVENT_RANKING_PLAYERINFO:
            {
                return "Ranking Player Info";
                break;
            }

            case EVENT_PLAYER_JOIN:
            {
                return "Player Login";
                break;
            }

            case EVENT_TICK:
            {
                return "Pseudo Update";
                break;
            }

            case SCHEDULER_RUN:
            {
                return "Scheduler";
                break;
            }

            case EVENT_CREATE_UNIVERSE:
            {
                return "Create Universe";
                break;
            }

            default:
            {
                return "UNKNOWN EVENT";
                break;
            }

        }
    }

    static function RaiseEvent()
    {
        // in most cases the following is true, but depends on the information required for the event.
        // $args['arg1'] is Sub Event is used else is set to NULL.
        // $args['arg2'] is the name of the container to be used if needed to update data.
        // $args['arg3'] is the actual data that need to be accessed.
        list($event, $args) = func_get_args();
        if(!is_numeric($event))
        {
            $event = constant($event);
        }
        if(is_null($event) || !is_integer($event))
        {
            return (boolean) false;
        }

        $max_hooks = 0;

        if (array_key_exists($event, $GLOBALS['events']))
        {
            $max_hooks = (int) count($GLOBALS['events'][$event]);
        }

        for($index=0; $index<$max_hooks; $index++)
        {
            $callback_function = $GLOBALS['events'][$event][$index];
            if(is_array($callback_function))
            {
                if(method_exists($callback_function[0],$callback_function[1]))
                {
                    call_user_func_array($callback_function, $args);
                }
                else
                {
//                  SYS::ErrorMessage(__FILE__, __FUNCTION__, __LINE__,"Invalid Event Callback function '{$callback_function[0]}:{$callback_function[1]}'.\n");
                    echo "<pre>[".__FILE__.":".__FUNCTION__.":".__LINE__."]\n". print_r($GLOBALS['events'], true) ."</pre>\n";
                }
            }
            else
            {
                if(function_exists($callback_function))
                {
                    call_user_func_array($callback_function, $args);
                }
                else
                {
//                  SYS::ErrorMessage(__FILE__, __FUNCTION__, __LINE__,"Invalid Event Callback function '{$callback_function}'.\n");
                    echo "<pre>[".__FILE__.":".__FUNCTION__.":".__LINE__."]\n". print_r($GLOBALS['events'], true) ."</pre>\n";
                }
            }
        }
    }
}

?>
