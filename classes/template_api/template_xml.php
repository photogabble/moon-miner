<?php

require_once("classes/xml_dom/xml_dom.php");

class XMLAPI
{
    private $xmldom                         = NULL;
    private $parent                         = NULL;

    // List of all names to ignore.
    private $ignore_list                    = array("language_vars");

    function __construct($parent)
    {
        $this->parent = $parent;
        $this->xmldom = new TemplateSystem();
    }

    function __destruct()
    {
        unset($this->xmldom);
    }

    public function Initialize($comment = NULL)
    {
        // We can have the Template system support comments at the top of the page using the
        // <!-- COMMENT -->

        // Set the output mode to XML DOM.
        $this->xmldom->OutputMode(XMLDOM_XML);

        // Initialize.
        $this->xmldom->Initialize('utf-8', NULL, $comment);
    }

    public function AddVariables($nodeName = NULL, $attributes = NULL)
    {
        if (in_array($nodeName, $this->ignore_list) === true)
        {
            return (boolean) false;
        }

        $container = NULL;
        if ( isset( $attributes['container'] )=== true && empty( $attributes['container'] ) === false )
        {
            $container = (string) $attributes['container'];
            unset($attributes['container']);
        }

        foreach($attributes as $key => $value)
        {
            if (is_array($value) === true)
            {
                reset($value);
                ksort($value, SORT_STRING);

                if ($this->xmldom->GetNode(NULL, $nodeName, $node) === false)
                {
                    $node = $this->xmldom->AddNode(NULL, $nodeName);
                }

                // Add length attribute to parent.
                $childNode = $this->xmldom->AddNode($node, $container);
                $this->xmldom->AddAttributes($childNode, $value);

                if (is_numeric($key) == false)
                {
                    $this->xmldom->AddAttributes($childNode, array("name"=>$key));
                }
            }
            else
            {
                // AddTextNode
                if ($this->xmldom->GetNode(NULL, $nodeName, $node) === false)
                {
                    $node = $this->xmldom->AddNode(NULL, $nodeName);
                }

                $skipNode = false;
                if ($this->xmldom->GetNode(NULL, $node->nodeName, $tmpnode) === true)
                {
                    for($i=0; $i<$tmpnode->childNodes->length; $i++)
                    {
                        if ($tmpnode->childNodes->item($i)->getAttribute("name") == $key && $tmpnode->childNodes->item($i)->nodeValue == $value)
                        {
                            $skipNode = true;
                            break;
                        }
                    }
                }

                // We do not want dupe arrays.
                if($skipNode == false)
                {
                    $childNode = $this->xmldom->AddNode($node, $container);
                    $this->xmldom->AddTextNode($childNode, $value);

                    if (is_numeric($key) == false)
                    {
                        $this->xmldom->AddAttributes($childNode, array("name"=>$key));
                    }
                }
            }
        }
    }

    public function Display()
    {
        $output = $this->xmldom->Display();
        echo $this->parent->HandleCompression($output);
        exit;
    }
}
?>
