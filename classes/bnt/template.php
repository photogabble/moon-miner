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
// File: classes/bnt/template.php
namespace bnt;

if (strpos ($_SERVER['PHP_SELF'], 'template.php')) // Prevent direct access to this file
{
    die ('Please do not access this file directly');
}

define("TEMPLATE_USE_SMARTY",           0x00000000,             true);
define("TEMPLATE_USE_XML",              0x00000001,             true);

class template
{
    private $type                       = NULL;
    private $initialiszd                = NULL;
    private $api                        = NULL;

    function __construct()
    {
        $this->initialized              = (boolean) false;
        $this->api                      = array();

        // Here we check if it's an External Client, else it's a Browser Client.
        request_var("SERVER", "HTTP_ACCEPT", $accept);

        // Seperate the accepted output type.
        $accepts = explode(",", $accept);

        // Cycle through the list of accepted output types.
        foreach($accepts as $accepted)
        {
            // Seperate the q prefered order if found.
            $a_accepts = explode(";", $accepted);
            switch($a_accepts[0])
            {
                case "application/xml":
                {
                    // We have a Client that requires XML, so init the XML Template API.
                    $this->Initialize(TEMPLATE_USE_XML) or die("ERR");
                    break 2;
                }
                default:
                {
                    // We have a Client that doesn't require XML, so fallback and init the Smarty Template API.
                    $this->Initialize(TEMPLATE_USE_SMARTY);
                    break 2;
                }
            }
        }
    }

    function __destruct()
    {
        unset($this->modules);
    }

    // Needs to be updated to suit Smarty and XML Template Systems.
    public function Initialize($type = NULL, $themeName = "classic")
    {
        if ($this->initialized != true)
        {
            if($type === TEMPLATE_USE_SMARTY)
            {
                // Create the module.
                $api = new \bnt\bnt_smarty($this);
            }
            else if($type === TEMPLATE_USE_XML)
            {
                // Create the module.
                $api = new \bnt\bnt_xml($this);
            }
            else
            {
                return (boolean) false;
            }

            // Set Wrapper Class Name.
            $this->api_class = get_class($api);

            // Add Wrapper to list.
            $this->api[$this->api_class] = $api;

            // Initialize Wrapper.
            $this->api[$this->api_class]->Initialize("CRAP");

            // Flag as initialised.
            $this->initialized = (boolean) true;

            // Clear unwanted info.
            unset($api);

            // return true;
            return (boolean) true;
        }
    }

    public function AddVariables($node, $variables)
    {
        $this->api[$this->api_class]->AddVariables($node, $variables);
    }

    public function GetVariables($node)
    {
        if (method_exists($this->api[$this->api_class], 'GetVariables') === true)
        {
            return $this->api[$this->api_class]->GetVariables($node);
        }
        return (boolean) false;
    }

    public function Display($template_file = NULL)
    {
        $this->api[$this->api_class]->Display($template_file);
    }

    public function Test()
    {
        $this->api[$this->api_class]->Test();
    }

    public function getAPI()
    {
        return $this->api;
    }

    public function SetTheme($theme = NULL)
    {
        if (method_exists($this->api[$this->api_class], 'SetTheme') === true)
        {
            $this->api[$this->api_class]->SetTheme($theme);
        }
    }

    public function HandleCompression($output = NULL)
    {
        // Check to see if we have data, if not error out.
        if (is_null($output))
        {
            die("SYSTEM HALT: NOTHING TO OUTPUT.");
            exit;
        }

        // Handle the supported compressions.
        $supported_enc = array();
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
        {
            $supported_enc = explode(",", $_SERVER['HTTP_ACCEPT_ENCODING']);
        }

        if (in_array("gzip", $supported_enc) === true)
        {
            header('Vary: Accept-Encoding');
            header('Content-Encoding: gzip');
            header("DEBUG: gzip found");
            return gzencode($output, 9);
        }
        elseif(in_array("deflate", $supported_enc) === true)
        {
            header('Vary: Accept-Encoding');
            header('Content-Encoding: deflate');
            header("DEBUG: deflate found");
            return gzdeflate($output, 9);
        }
        else
        {
            header("DEBUG: None found");
            return $output;
        }
        // may need to call exit;
        # exit;
    }
}
?>