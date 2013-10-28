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
// File: classes/BntXml.php

if (strpos ($_SERVER['PHP_SELF'], 'BntXml.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntXml
{
    private $xmldom                         = null;
    private $parent                         = null;

    // List of all names to ignore.
    private $ignore_list                    = array("language_vars");

    function __construct($parent)
    {
        $this->parent = $parent;
        $this->xmldom = new BntXmlTemplateSystem();
    }

    function __destruct()
    {
        unset($this->xmldom);
    }

    public function Initialize($comment = null)
    {
        // We can have the Template system support comments at the top of the page using the
        // <!-- COMMENT -->

        // Set the output mode to XML DOM.
        $this->xmldom->OutputMode(XMLDOM_XML);

        // Initialize.
        $this->xmldom->Initialize('utf-8', null, $comment);
    }

    public function AddVariables($nodeName = null, $attributes = null)
    {
        if (in_array($nodeName, $this->ignore_list) === true)
        {
            return (boolean) false;
        }

        $container = null;
        if ( isset( $attributes['container'] )=== true && empty( $attributes['container'] ) === false )
        {
            $container = (string) $attributes['container'];
            unset($attributes['container']);
        }

        foreach ($attributes as $key => $value)
        {
            if (is_array ($value) === true)
            {
                reset ($value);
                ksort ($value, SORT_STRING);

                if ($this->xmldom->GetNode (null, $nodeName, $node) === false)
                {
                    $node = $this->xmldom->AddNode (null, $nodeName);
                }

                // Add length attribute to parent.
                $childNode = $this->xmldom->AddNode ($node, $container);
                $this->xmldom->AddAttributes ($childNode, $value);

                if (is_numeric ($key) == false)
                {
                    $this->xmldom->AddAttributes ($childNode, array ("name"=>$key));
                }
            }
            else
            {
                // AddTextNode
                if ($this->xmldom->GetNode (null, $nodeName, $node) === false)
                {
                    $node = $this->xmldom->AddNode (null, $nodeName);
                }

                $skipNode = false;
                if ($this->xmldom->GetNode (null, $node->nodeName, $tmpnode) === true)
                {
                    for ($i = 0; $i < $tmpnode->childNodes->length; $i++)
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
                    $childNode = $this->xmldom->AddNode ($node, $container);
                    $this->xmldom->AddTextNode ($childNode, $value);

                    if (is_numeric ($key) == false)
                    {
                        $this->xmldom->AddAttributes ($childNode, array ("name"=>$key));
                    }
                }
            }
        }
    }

    public function Display ()
    {
        $output = $this->xmldom->Display ();
        echo $output;
        die ();
    }
}
?>
