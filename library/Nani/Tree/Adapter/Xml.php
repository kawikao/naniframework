<?php

/**
 * @category   Nani
 * @package    Nani_Tree
 * @subpackage  Adapter
 * @copyright  Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: Xml.php 31 2010-03-13 07:46:59Z sandiegophp $
 */

/**
 * @see Nani_Tree_Adapter_Abstract
 */
require_once 'Nani/Tree/Adapter/Abstract.php';

/**
 * @category   Nani
 * @package    Nani_Tree
 * @subpackage  Adapter
 * @copyright  Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Nani_Tree_Adapter_Xml extends Nani_Tree_Adapter_Abstract
{

    /**
     * @var array
     */
    private $_rgtStack = array();
    
    /**
     * @var array
     */
    private $_lastRow = array();

    /**
     * Format rows as XML
     *
     * @see Nani_Tree_Adapter_Abstract#writeTree()
     */
    protected function writeTree($rows = null)
    {
        try {
            // initialize stacks
            $this->_rgtStack = array();
            $this->_lastRow = array();
            
            // create new Dom object
            $this->_dom = new DOMDocument('1.0', $this->_options['encoding']);

            // set options
            $this->_dom->formatOutput = $this->_options['formatOutput'];

            // create root container and append to dom
            $root = $this->_dom->createElement($this->_options['container']);
            $this->_dom->appendchild($root);

            // if $rows parameter set, use it, else get rows
            $rows = ! empty($rows) ? $rows : $this->getRows();

            // convert array result to xml
            $this->_arrayToXml($rows, $root);

            // get output
            $this->_output = $this->_dom->saveXml();

        } catch(Exception $e) {
            require_once 'Nani/Tree/Exception.php';
            throw new Nani_Tree_Exception($e->getMessage());
        }

    }

    /**
     * convert XML string into Array
     *
     * @param string|array $xml is a string on first call, array on recursive calls
     * @param bool $recursive
     * @return array $return
     */
    protected function _xmlToArray($data, $recursive = false)
    {
        // which loop
        if (!$recursive ) {

            // initial loop
            $data = @simplexml_load_string ($data);

            // returns false on any XML error
            if (! $data instanceof SimpleXMLElement) {

                require_once 'Nani/Tree/Exception.php';
                throw new Nani_Tree_Exception('XML must be a valid XML string');

            } // end: returns false on any XML error

        } // end: which loop

        // initialize this loops return array
        $return = array();

        // step through array
        foreach ($data as $key => $value) {

            // force array type
            $aryValue = (array) $value;

            // depth check
            if (isset($aryValue[0]) && ! $aryValue[0] instanceof SimpleXMLElement) {

                // level 1 node
                $return[$key] = trim($aryValue[0]);

            } else {

                // new object recursive call
                if (count($value)) {

                    // object is not another row
                    if($recursive && (is_int($key) || ! $value instanceof SimpleXmlElement)) {

                        // do not add another index level
                        $return[$key] = $this->_xmlToArray($aryValue, true);

                    } else {

                        // this is a new row, add an index level
                        $return[$key][] = $this->_xmlToArray($aryValue, true);

                    } // end: object is another row

                } else {

                    // empty value
                    $return[$key] = '';

                } // end: new object recursive call

            } // end: depth check

        } // end: step through array

        // return array
        return $return;

    }

    protected function _arrayToXml(array $mixed, DOMElement $domElement = null)
    {

        // node with children
        foreach( $mixed as $key => $mixedElement ) {

            // key is numeric and rgt is set 
            if(is_int($key) && ! empty($mixedElement['rgt'])) {

                // first node
                if(empty($this->_rgtStack)) {
                    
                    // create child of main container
                    $row = $this->_dom->createElement($this->_options['rowtag']);
                    $domElement->appendChild($row);
                    
                // nested node
                } elseif($mixedElement['rgt'] < $this->_rgtStack[0]) {

                    // create child of node in stack
                    $row = $this->_dom->createElement($this->_options['rowtag']);
                    $this->_lastRow[0]->appendChild($row);
                    
                // sibling node
                } else {
                    
                    // remove parent(s) from stacks
                    foreach($this->_rgtStack as $parentRgt) {
                        
                        // if this node is outside stack row
                        if($mixedElement['rgt'] > $parentRgt) {
                            array_shift($this->_rgtStack);
                            array_shift($this->_lastRow);
                        }
                    }

                    // create sibling of parent that made recursive call
                    $row = $this->_dom->createElement($this->_options['rowtag']);
                    $domElement->appendChild($row);

                }

                // save this nodes rgt and DOMElement instance
                array_unshift($this->_lastRow, $row);
                array_unshift($this->_rgtStack, $mixedElement['rgt']);

                // recursive call
                $this->_arrayToXml($mixedElement, $row);

            // if not attributes array
            } elseif ($key != 'attributes') {

                // create new element
                $tag = $this->_dom->createElement($key);

                // append to dom
                $domElement->appendChild($tag);
                $tag->appendChild($this->_dom->createTextNode($mixedElement));
                    
            } // end: key is numeric and rgt is set

        } // end: node with children

    }
}
