<?php

/**
 * @category    Nani
 * @package     Nani_Tree
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id: Tree.php 32 2010-03-15 15:19:27Z sandiegophp $
 */

/**
 * @category    Nani
 * @package     Nani_Tree
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Nani_Tree {

    /**
     * 
     * @param string $adapter
     * @param array $config
     * @return object $treeAdapter
     * 
     * @todo unit test for good file, bad class name
     */
    public function __construct($adapter, $data, $options = array())
    {
        $this->setAdapter($adapter, $data, $options);
    }

    /**
     * 
     * @param string    $adapter        Adapter to use
     * @param mixed     $data           Data for the adapter
     * @param array     $options        REQUIRED options for the adapter
     * @throws Nani_Tree_Exception
     */
    public function setAdapter($adapter, $data, array $options = array())
    {

        // class name
        $adapterName = 'Nani_Tree_Adapter_' . ucfirst($adapter);

        if(! class_exists($adapterName)) {
            $filepath = $this->_getFilePath($adapterName);

            if(empty($filepath)) {
                require_once 'Nani/Tree/Exception.php';
                throw new Nani_Tree_Exception("Adapter " . $adapter . " not found");
            }
        
            require_once $filepath;
        }

        // instantiate new adapter
        $this->_adapter = new $adapterName($data, $options);

        // adapter not instantiated
        if (! $this->_adapter instanceof Nani_Tree_Adapter_Abstract) {
            require_once 'Nani/Tree/Exception.php';
            throw new Nani_Tree_Exception("Adapter '$adapter' does not extend Nani_Tree_Adapter_Abstract");
        }

    }
    
    /**
     * @return object   $adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }
    
    /**
     * get filepath using PHP functions
     * 
     * @param string $adapter
     * @return mixed $fullpath
     */
    private function _getFilePath($adapter)
    {
        // convert class name to file path
        $filename = preg_replace('/_/', '/', ucfirst($adapter) . '.php');

        // get include_path parts
        $paths = explode(':', get_include_path());

        // step through paths
        foreach($paths as $path) {
            // append filename
            $fullpath = $path . '/' . $filename;
            
            if(file_exists($fullpath)) {
                return $fullpath;
            }
        }
        
        return '';
    }
    
    // @codeCoverageIgnoreStart
    
    /**
     * Calls all methods from the adapter
     */
    public function __call($method, array $options)
    {
        if (method_exists($this->_adapter, $method)) {
            return call_user_func_array(array($this->_adapter, $method), $options);
        }
        require_once 'Nani/Tree/Exception.php';
        throw new Nani_Tree_Exception("Unknown method '" . $method . "' called!");
    }
    
    // =====================================
    // INPUT / OUTPUT
    public function serializeTree($tree) {
        return serialize($tree);
    }
    public function unserializeTree($serializedTree) {
        return unserialize($serializedTree);
    }
    // @codeCoverageIgnoreEnd
    
}
