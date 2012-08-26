<?php

define("TEMPLATE_USE_SMARTY",           0x00000000,             true);
define("TEMPLATE_USE_XML",              0x00000001,             true);

class Template
{
    private $type                       = NULL;
    private $initialiszd                = NULL;
    private $api                        = NULL;

    function __construct()
    {
        $this->initialized              = (boolean) false;
        $this->api                      = array();

        // Here we check if it's an External Client, else it's a Browser Client.
        request_var("SERVER", "HTTP_CLIENT", $type);
        request_var("SERVER", "HTTP_USER_AGENT", $agent);
        if ( (!is_null($type) && strtolower(trim($type)) === "externalxml" && substr($agent, 0, 6) === "Warden"))
        {
            // We have an Extrenal Client, so init the XML Template API.
            $this->Initialize(TEMPLATE_USE_XML) or die("ERR");
        }
        else
        {
            // We have an Browser Client, so init the Smarty Template API.
            $this->Initialize(TEMPLATE_USE_SMARTY, "classic");
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
                // Include the templat wrapper file.
                require_once './classes/template_api/template_smarty.php';

                // Create the module.
                $api = new SmartyAPI($this);
            }
            else if($type === TEMPLATE_USE_XML)
            {
                // Include the templat wrapper file.
                require_once './classes/template_api/template_xml.php';

                // Create the module.
                $api = new XMLAPI($this);
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
