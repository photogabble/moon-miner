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
// File: classes/bnt/spl_class_loader.php
//
// SplClassLoader implementation that implements the technical interoperability
// standards for PHP 5.3 namespaces and class names.
//
// http://groups.google.com/group/php-standards/web/final-proposal
//
// @author Jonathan H. Wage <jonwage@gmail.com>
// @author Roman S. Borschel <roman@code-factory.org>
// @author Matthew Weier O'Phinney <matthew@zend.com>
// @author Kris Wallsmith <kris.wallsmith@gmail.com>
// @author Fabien Potencier <fabien.potencier@symfony-project.org>
//
// We modified it for BNT, removing the remapping of "_" to a directory
// separator, because we use those in our file names.

class SplClassLoader
{
    private $_fileExtension = '.php';
    private $_namespace;
    private $_includePath;
    private $_namespaceSeparator = '\\';

    // Creates a new <tt>SplClassLoader</tt> that loads classes of the specified namespace.
    // @param string $ns The namespace to use.

    public function __construct ($ns = null, $includePath = null)
    {
        $this->_namespace = $ns;
        $this->_includePath = $includePath;
    }

    // Sets the namespace separator used by classes in the namespace of this class loader.
    // @param string $sep The separator to use.
    public function setNamespaceSeparator ($sep)
    {
        $this->_namespaceSeparator = $sep;
    }

    // Gets the namespace seperator used by classes in the namespace of this class loader.
    // @return void
    public function getNamespaceSeparator ()
    {
        return $this->_namespaceSeparator;
    }

    // Sets the base include path for all class files in the namespace of this class loader.
    // @param string $includePath
    public function setIncludePath ($includePath)
    {
        $this->_includePath = $includePath;
    }

    // Gets the base include path for all class files in the namespace of this class loader.
    // @return string $includePath
    public function getIncludePath ()
    {
        return $this->_includePath;
    }

    // Sets the file extension of class files in the namespace of this class loader.
    // @param string $fileExtension
    public function setFileExtension ($fileExtension)
    {
        $this->_fileExtension = $fileExtension;
    }

    // Gets the file extension of class files in the namespace of this class loader.
    // @return string $fileExtension
    public function getFileExtension ()
    {
        return $this->_fileExtension;
    }

    // Installs this class loader on the SPL autoload stack.
    public function register ()
    {
        spl_autoload_register (array ($this, 'loadClass'));
    }

    // Uninstalls this class loader from the SPL autoloader stack.
    public function unregister ()
    {
        spl_autoload_unregister (array ($this, 'loadClass'));
    }

    // Loads the given class or interface.
    // @param string $className The name of the class to load.
    // @return void
    public function loadClass ($className)
    {
        if (null === $this->_namespace || $this->_namespace.$this->_namespaceSeparator === substr ($className, 0, strlen ($this->_namespace.$this->_namespaceSeparator)))
        {
            $fileName = '';
            $namespace = '';
            if (false !== ($lastNsPos = strripos ($className, $this->_namespaceSeparator)))
            {
                $namespace = substr ($className, 0, $lastNsPos);
                $className = substr ($className, $lastNsPos + 1);
                $fileName = str_replace ($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }

            $fileName .= strtolower ($className) . $this->_fileExtension;
            require ($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '') . $fileName;
        }
    }
}
?>
