<?php

/**
 * Nani Framework
 *
 * @category   Nani
 * @package    Nani_Tree
 * @subpackage Adapter
 * @copyright  Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: Array.php 31 2010-03-13 07:46:59Z sandiegophp $
 */

/**
 * abstract
 */
require_once 'Nani/Tree/Adapter/Abstract.php';

/**
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  Adapter
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Nani_Tree_Adapter_Array extends Nani_Tree_Adapter_Abstract
{

    /**
     * Return rows as Array
     * 
     * @see Nani_Tree_Adapter_Abstract#writeTree()
     */
	protected function writeTree($rows = null){

	   if(!empty($rows)) {
	       $this->_output = $rows;
	   } else {
	       $this->_output = $this->getRows(); 
	   }
	}
}
