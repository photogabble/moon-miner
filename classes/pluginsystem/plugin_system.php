<?php

define("PLUGIN_PATH",                    "./plugins",            true);

class PluginSystem
{
    static $version                         = "0.0.0 (0000) Alpha";
    static $author                          = "Blacknova Development";

    static private $callbackfunc            = "OnEvent";
    static private $db                      = null;

    static private $pluginlist              = null;

    function __construct(){}
    function __destruct(){}

    static function Initialize($db = null)
    {
        $GLOBALS['events'] = array();
        self::$db = $db;
        self::$pluginlist = array();
    }

    static function LoadPlugins()
    {
        global $plugin_config;
        $d = dir(PLUGIN_PATH);

        while (false !== ($entry = $d->read()))
        {
            if(is_dir("{$d->path}/{$entry}") && $entry != "." && $entry != "..")
            {
                $plugin_name = $entry;
                if (file_exists("{$d->path}/{$plugin_name}/plugin_config.php"))
                {
                    require_once("{$d->path}/{$plugin_name}/plugin_config.php");
                    if(isset($plugin_config[$pluginname]['enabled']) && $plugin_config[$pluginname]['enabled'] == true)
                    {
                        if (isset($plugin_config[$pluginname]['has_loader']) && $plugin_config[$pluginname]['has_loader'] == true)
                        {
                            if ( file_exists("{$d->path}/{$plugin_name}/plugin_loader.php") )
                            {
                                global $$pluginname;
                                require_once("{$d->path}/{$plugin_name}/plugin_loader.php");

                                self::$pluginlist[$pluginname] = new $pluginname();
                                self::$pluginlist[$pluginname]->Initialize(self::$db);
                            }
                        }
                        unset($plugin_config[$pluginname]);
                    }
                    else
                    {
                        unset($plugin_config[$pluginname]);
                    }
                }
            }
        }
        $d->close();
    }

    static function GetPluginInfo($plugin = null)
    {
        if (is_null($plugin))
        {
            return (array) self::$pluginlist;
        }
        else
        {
            if(array_key_exists($plugin, self::$pluginlist))
            {
                return (array) self::$pluginlist[$plugin];
            }
        }
    }

    static function AddEventHook($event = null, Plugin $callback = null)
    {
        if(!is_numeric($event) && !is_null($event))
        {
            $event = constant($event);
        }

        if(is_null($event) || !is_numeric($event))
        {
            admin_log(self::$db, LOG_RAW, "Plugin Error on line (". __LINE__ ."): invalid event ID.");
            return (boolean) false;
        }

        if(is_null($callback) || !is_object($callback))
        {
            admin_log(self::$db, LOG_RAW, "Plugin Error on line (". __LINE__ ."): invalid callback.");
            return (boolean) false;
        }

        // Check if the callback class::function exists and is callable.
        if (!method_exists($callback, self::$callbackfunc) || !is_callable(array($callback, self::$callbackfunc), false, $callable_name))
        {
            admin_log(self::$db, LOG_RAW, "Plugin Error on line (". __LINE__ ."): ". get_class($callback) ."::". self::$callbackfunc ." function doesn't exits or isn't callable.");
            return (boolean) false;
        }

        if (!isset($GLOBALS['events'][$event]))
        {
            $GLOBALS['events'][$event] = array();
        }
        array_push($GLOBALS['events'][$event], $callback);

        return (boolean) true;
    }

    static function RemoveEventHook($event = null, Plugin $callback = null)
    {
        if (!array_key_exists('events', $GLOBALS) || !array_key_exists($event, $GLOBALS['events']) || !in_array($callback, $GLOBALS['events'][$event]))
        {
            admin_log(self::$db, LOG_RAW, "Plugin Error on line (". __LINE__ ."): Cannot find supplied Event.");
            return (boolean) false;
        }

        $index = array_search($callback, $GLOBALS['events'][$event]);
        array_splice($GLOBALS['events'][$event], $index, 1);
    }

    static function ListEventHooks($event = null)
    {
        if (is_null($event))
        {
            return (array) $GLOBALS['events'];
        }
        else
        {
            if(!is_numeric($event))
            {
                $event = constant($event);
            }
            return (array) $GLOBALS['events'][$event];
        }
    }

    static function RaiseEvent($event = null, $args = array())
    {
        if(!is_numeric($event))
        {
            $event = constant($event);
        }

        if(is_null($event) || !is_integer($event))
        {
            admin_log(self::$db, LOG_RAW, "Plugin Error on line (". __LINE__ ."): Invalid EventID.");
            return (boolean) false;
        }

        foreach ($GLOBALS['events'][$event] as $hook)
        {
            if(method_exists($hook, self::$callbackfunc))
            {
                call_user_func_array(array($hook, self::$callbackfunc), $args);
            }
            else
            {
                admin_log(self::$db, LOG_RAW, "Plugin Error on line (". __LINE__ ."): Invalid Hook.");
            }
        }
    }
}

abstract class Plugin
{
    // Force Extending class to define this method
    abstract public function __construct();
    abstract public function __destruct();
    abstract protected function OnEvent();
    abstract protected function Initialize();
}


?>
