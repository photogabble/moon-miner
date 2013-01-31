<?php
//
// Name: XML DOM Template System
// Author: TheMightyDude (Paul Kirby)
// Version: 0.0.9 (0032) Development version.
// Created: March 15 2012
// Updated: August 12 2012
// Copyright: © 2012 Paul Kirby
//
// This file handles all the XML formatting and allows to validate the
// XML Format by using a DTD file, we also use a style sheet that uses
// the XML Data and then outputs as XHTML using a XSLT style sheet file.
//
// File: vendor/bnt/TemplateSystem.php
namespace bnt;

if (strpos ($_SERVER['PHP_SELF'], 'templateSystem.php')) // Prevent direct access to this file
{
    die ('Please do not access this file directly');
}

    define ("XMLDOM_PROCESSED",             0x0001,            true);
    define ("XMLDOM_UNPROCESSED",           0x0002,            true);
    define ("XMLDOM_XML",                   0x0003,            true);

    class TemplateSystem
    {
        static $version                     = "0.0.9 (0032) [DEV]";
        static $author                      = "TheMightyDude";
        private $qualifiedName              = "root";
        private $document                   = NULL;
        private $root                       = NULL;
        private $validation                 = false;
        private $initialized                = false;
        private $mode                       = XMLDOM_XML;

        private $stylesheet                 = NULL;

        function __construct($publicId = NULL, $systemId  = NULL)
        {
            $this->chkLibraries(array("xsl", "dom"));

            $imp = new \DOMImplementation;
            if ( !is_null($publicId) && !is_null($systemId) )
            {
                $this->validation = (boolean) true;
                $dtd = $imp->createDocumentType($this->qualifiedName, $publicId, $systemId);
                $this->document = $imp->createDocument("", "", $dtd);
            }
            else
            {
                $this->validation = (boolean) false;
                $this->document = $imp->createDocument("", "");
            }
            $this->initialized = (boolean) false;
        }

        function __destruct()
        {
            $this->RleaseTemplate();
        }

        private function chkLibraries($libraries = NULL)
        {
            if( is_null($libraries) || !is_array($libraries) )
			{
				return (boolean) false;
			}

            foreach($libraries as $library)
            {
                $library = strtolower($library);
                extension_loaded($library) or trigger_error("Class '{$library}' not found", E_USER_ERROR);
            }
        }

        public function Initialize($encoding = "iso-8859-1", $stylesheet = NULL, $comment = NULL)
        {
            $this->Encoding($encoding);
            $this->StyleSheet($stylesheet);

            // Add a comment to the document
            if (!is_null($comment))
            {
                $this->document->appendChild(new \DOMComment($comment));
            }

            $this->root = $this->document->appendChild(new \DOMElement($this->qualifiedName));
            $this->StandAlone(true);

            // Add Template class info.
            $this->AddAttributes($this->root, array("version"=>self::$version, "author"=>self::$author));
            $this->initialized = (boolean) true;
        }

        public function RleaseTemplate()
        {
            unset($this->document);
            unset($this->error);
        }

        public function Encoding($encoding = "iso-8859-1")
        {
            $this->document->encoding = $encoding;
        }

        public function StandAlone($standalone = false)
        {
            $this->document->standalone = $standalone;
        }

        public function OutputMode($mode = XMLDOM_XML)
        {
            switch($mode)
            {
                case XMLDOM_XML:
                {
                    $this->mode = XMLDOM_XML;
                    break;
                }
                case XMLDOM_UNPROCESSED:
                {
                    $this->mode = XMLDOM_UNPROCESSED;
                    break;
                }
                case XMLDOM_PROCESSED:
                {
                    $this->mode = XMLDOM_PROCESSED;
                    break;
                }
                default:
                {
                    $this->mode = XMLDOM_PROCESSED;
                    break;
                }
            }
        }

        public function StyleSheet($stylesheet = NULL)
        {
            if (!is_null($stylesheet))
            {
                if (file_exists($stylesheet) === false)
                {
                    trigger_error("Style-Sheet: '{$stylesheet}' does not exist.", E_USER_ERROR);
                }

				if($this->mode == XMLDOM_UNPROCESSED)
				{
					$this->document->appendChild($this->document->createProcessingInstruction("xml-stylesheet", "type=\"text/xsl\" href=\"$stylesheet\"")); 

				}
				else
				{
					$this->stylesheet = new \DOMDocument();
					$this->stylesheet->preserveWhiteSpace = true;
					$this->stylesheet->formatOutput = true;
					$ret = $this->stylesheet->load($stylesheet);
					if (!$ret)
					{
						trigger_error("Unable to load style-sheet: '{$stylesheet}'", E_USER_ERROR);
					}
				}
            }
        }

        public function AddNode($parent = NULL, $nodeName = NULL)
        {
            if (is_null($nodeName))
            {
                return (boolean) false;
            }

            if ($this->initialized === false)
            {
                trigger_error("Template System NOT Initialized", E_USER_ERROR);
            }

            if(is_null($parent))
            {
                $parent = $this->root;
            }
            return $parent->appendChild(new \DOMElement($nodeName));
        }

		public function GetNode($parent = NULL, $nodeName = NULL, &$node = NULL)
		{
			$node = NULL;
            if (is_null($nodeName))
            {
                return (boolean) false;
            }

            if ($this->initialized === false)
            {
                trigger_error("Template System NOT Initialized", E_USER_ERROR);
            }

            if(is_null($parent))
            {
                $parent = $this->root;
            }

			$nodeList = $parent->childNodes;
			for ($i=0; $i<$nodeList->length; $i++)
			{
				if ($nodeList->item($i)->nodeName === $nodeName)
				{
					$node = $nodeList->item($i);
					break;
				}
			}

			if (is_null($node) === true)
			{
				return (boolean)false;
			}
			else
			{
				return (boolean) true;
			}
		}

        public function AddNodeArray($parent = NULL, $nodeName = NULL, $nodeData = NULL)
        {
            if (is_null($nodeName))
            {
                return (boolean) false;
            }

            if ($this->initialized === false)
            {
                trigger_error("Template System NOT Initialized", E_USER_ERROR);
            }

            if(is_null($parent))
            {
                $parent = $this->root;
            }

            $count = (integer) count($nodeData);
            for($i=0; $i<$count; $i++)
            {
                $node = $parent->appendChild(new \DOMElement($nodeName));
                foreach ($nodeData[$i] as $name => $value)
                {
                    $node->setAttributeNode(new \DOMAttr($name, $value));
                }
            }
            return (boolean) true;
        }

        public function AddAttribute($node = NULL, $name = NULL, $value = NULL)
        {
            $return = (boolean) false;
            if ( !is_null($node) && !is_null($name) )
            {
                $node->setAttributeNode(new \DOMAttr($name, $value));
                $return = (boolean) true;
            }
            return $return;
        }

        public function AddAttributes($node = NULL, $array = NULL)
        {
            $return = (boolean) false;
            if ( !is_null($node) && !is_null($array) && is_array($array) )
            {
                foreach ($array as $name => $value)
                {
                    $node->setAttributeNode(new \DOMAttr($name, $value));
                }
                $return = (boolean) true;
            }
            return $return;
        }

        public function AddComment($node = NULL, $comment = NULL)
        {
            if (is_null($comment))
            {
                return (boolean) false;
            }

            if ($this->initialized === false)
            {
                trigger_error("Template System NOT Initialized", E_USER_ERROR);
            }

            if(is_null($node))
            {
                $node = $this->root;
            }
            $node->appendChild(new \DOMComment($comment));
            return (boolean) true;
        }

        public function AddCDATASection($node = NULL, $cdata = NULL)
        {
            if (is_null($cdata))
            {
                return (boolean) false;
            }

            if ($this->initialized === false)
            {
                trigger_error("Template System NOT Initialized", E_USER_ERROR);
            }

            if(is_null($node))
            {
                $node = $this->root;
            }
            $node->appendChild(new \DOMCdataSection($cdata));
            return (boolean) true;
        }

        public function AddTextNode($node = NULL, $text = NULL)
        {
            if (is_null($text))
            {
                return (boolean) false;
            }

            if ($this->initialized === false)
            {
                trigger_error("Template System NOT Initialized", E_USER_ERROR);
            }

            if(is_null($node))
            {
                $node = $this->root;
            }
            $node->appendChild(new \DOMText($text));
            return (boolean) true;
        }

        public function Display()
        {
            if ($this->initialized === false)
            {
                trigger_error("Template System NOT Initialized", E_USER_ERROR);
            }

            if(@$this->document->validate() || $this->validation == false)
            {
                header('Cache-Control: no-cache, must-revalidate');

                if ($this->mode == XMLDOM_XML)
                {
					header('Content-Type: application/xml');
					$this->document->preserveWhiteSpace = false;
					$this->document->formatOutput = true;

					// Hash the content, so that we can validate the client end.
					header("Content-Hash: ". sha1($this->document->saveXML()));

					return $this->document->saveXML();
                }
                elseif($this->mode == XMLDOM_PROCESSED)
                {
                    if (is_null($this->stylesheet)) trigger_error("StyleSheet Not Set.", E_USER_ERROR);
//                    header("Content-Type: text/html; charset={$this->document->encoding}");
                    header("Content-Type: text/html");
                    $proc = new \XSLTProcessor;
                    $proc->setProfiling('logs/profiling.txt');
                    @$proc->importStyleSheet($this->stylesheet); // attach the xsl rules
                    echo $proc->transformToXML($this->document);
                }
                elseif($this->mode == XMLDOM_UNPROCESSED)
				{
//                    header('Content-Type: application/xml; charset={$this->document->encoding}');
                    header('Content-Type: application/xml');
                    echo $this->document->saveXML();
				}
                else
                {
                    trigger_error("Output Mode NOT SUPPORTED", E_USER_ERROR);
                }
            }
            else
            {
                trigger_error("Failed to Validate", E_USER_ERROR);
            }
        }
    }
?>
