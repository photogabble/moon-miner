<?php

require('backends/smarty/Smarty.class.php');

class SmartyAPI
{
    private $smarty                            = NULL;
    private $parent                            = NULL;

    function __construct($parent)
    {
        $this->parent = $parent;
        $this->smarty = new Smarty();
    }

    function __destruct()
    {
    }

    public function Initialize()
    {
        // Setup all Smarty's Path locations.
        $this->smarty->setCompileDir('templates/_compile/');
        $this->smarty->setCacheDir('templates/_cache/');
        $this->smarty->setConfigDir('templates/_configs/');

        // Add a Modifier Wrapper for PHP Function: number_format.
        // Usage in the tpl file {$number|number_format:decimals:"dec_point":"thousands_sep"}
        $this->smarty->registerPlugin("modifier","number_format", "number_format");

        $this->smarty->enableSecurity();

        // Smarty Caching.
        $this->smarty->caching = false;
    }

    public function SetTheme($themeName = NULL)
    {
        $this->smarty->setTemplateDir("templates/{$themeName}");
        $this->AddVariables("template_dir", "templates/{$themeName}");
    }

    public function AddVariables($nodeName, $variables)
    {
        // We don't require the container so remove it.
        if (is_array($variables) && isset($variables['container']) == true)
        {
            unset($variables['container']);
        }

        $tmpNode = $this->smarty->getTemplateVars($nodeName);

        if (!is_null($tmpNode))
        {
            // Now we make sure we don't want dupes which causes them to become an array.
            foreach($variables as $key=>$value)
            {
                if (array_key_exists($key, $tmpNode) && $tmpNode[$key] == $value)
                {
                    unset($variables[$key]);
                }
            }

            $variables = array_merge_recursive($tmpNode, $variables);
        }
        $this->smarty->assign($nodeName, $variables);
    }

    public function GetVariables($nodeName)
    {
        return $this->smarty->getTemplateVars($nodeName);
    }

    public function Test()
    {
        $this->smarty->testInstall();
    }

    public function Display($template_file)
    {
        // Process template and return the output in a
        // varable so that we can compress it or not.
        $output = $this->smarty->fetch($template_file);

        echo $this->parent->HandleCompression($output);
        exit;
    }
}

?>
