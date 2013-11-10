<?php

/**
 * Nani Framework
 *
 * @category   Nani
 * @package    Nani_Tree
 * @subpackage Adapter
 * @copyright  Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: Abstract.php 34 2010-03-19 17:26:00Z sandiegophp $
 */

/**
 * Class for common operations
 *
 * @category   Nani
 * @package    Nani_Tree
 * @subpackage Adapter
 * @copyright  Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Nani_Tree_Adapter_Abstract
{
    /**
     * Incoming data can be XML string, XML filename, array or database object
     *
     * @var mixed
     */
    protected $_data;

    /**
     * Nested set tree
     *
     * @var array
     */
    protected $_rows = array();

    /**
     * Options for building rows and connecting to database
     *
     * @var array
     */
    protected $_options = array(
        'startleft'    => 1, 
        'container'    => 'rows',
        'rowtag'       => null, 
        'rowidtag'     => null, 
        'parentidtag'  => null, 
        'formatOutput' => false, 
        'encoding'     => 'utf-8', 
    //'columnPrefix => null,
    );

    /**
     * receives mixed data from adapter, converts to array, builds tree
     * then calls output adapter's writeTree() function to handle the results
     *
     * @param mixed $sourceData
     * @param array $options
     */
    public function __construct($sourceData, array $options = array())
    {
        // reject empty data
        if(is_string($sourceData) && empty($sourceData)) {
            require_once 'Nani/Tree/Exception.php';
            $errorString = "Data " . $sourceData . " must be an array, XML string, filename or a Database Handle";
            throw new Nani_Tree_Exception($errorString);
        }

        // merge options with any existing options
        $this->_setOptions($options);

        // determine input data type and convert to an array
        $arrayData = $this->_setDataType($sourceData);

        // build nested set
        $this->_buildTree($arrayData);

        // format output based on adapter called
        $this->writeTree();
    }

    /**
     * Build array based on input data datatype
     *
     * @param mixed $sourceData
     * @return array $arrayData
     * @throws Nani_Tree_Exception if sourceData is not valid
     */
    private function _setDatatype($sourceData)
    {
        try {
            if(is_array($sourceData)) {

                $arrayData = $sourceData;

            } elseif (is_string($sourceData)) {

                if(is_readable($sourceData)) {
                    $sourceData = file_get_contents($sourceData);
                }

                $arrayData = $this->_xmlToArray($sourceData);

            } elseif (is_object($sourceData) && ! empty($this->_options['db'])) {

//                require_once 'Nani/Tree/Select.php';
                require_once 'Zend/Db/Table.php';

                $select = $sourceData->select();
                $select->from($this->_options['db']['tablename'])
                        ->order('lft')
                        ->where($this->_options['db']['where']);
                $stmt = $select->query();
                $arrayData = array('row'=>$stmt->fetchAll());

            } else {

                throw new Exception();

            }

            return $arrayData;

        } catch(Exception $e) {

            if(is_object($sourceData)) {
                $sourceData = 'Object';
            }

            require_once 'Nani/Tree/Exception.php';
            $errorString = "Data " . $sourceData . " must be an array, XML string, filename or Database Handle";
            throw new Nani_Tree_Exception($errorString);
        }
    }

    /**
     * Sets new adapter options
     *
     * @param  array $options Adapter options
     * @return void
     */
    private function _setOptions(array $options = array()) {
        $this->_options = array_merge($this->_options, $options);
    }

    /**
     *    Nested Set Building Functions
     */

    /**
     * Walk top-level array elements and add lft and rgt rows for nested set.
     * Nested child paths will be handled in _parseRows function
     *
     * @param array $arrayData
     * @throws Nani_Tree_Exception if array parsing fails
     * @return void
     */
    private function _buildTree(array $arrayData)
    {
        // create temporary root_node which will be removed later
        $this->_rows[0] = array('id'  =>0,
                                'lft' => $this->_options['startleft'], 
                                'rgt' => $this->_options['startleft'] + 1, 
        );
        try {
            // loop through top-level nodes
            foreach($arrayData[$this->_options['rowtag']] as $row) {
                $this->_parseRows($row);
            }

            // unset temporary root_node
            // cannot array_shift as that will change the keys
            unset($this->_rows[0]);
            
            // compress tree
            array_walk($this->_rows, array(&$this,'_moveSetLeft'));

        } catch(Exception $e) {
            // xml error
            require_once 'Nani/Tree/Exception.php';
            throw new Nani_Tree_Exception($e->getMessage());
        }
    }

    /**
     * Get rows from current node and recursively calls itself for all child nodes
     *
     * @param array $row current row
     * @param integer $parent parent index
     * @return void
     */
    private function _parseRows(array $row, $parent=0)
    {
        // use parentidtag as result row index
        if(! empty($this->_options['parentidtag'])) {
            $parent = $row[$this->_options['parentidtag']];
        }

        // initialize temporary row array
        $tmpRow = array();

        // step through each tag of current row
        foreach($row as $tag => $value) {
            // skip over child paths
            if($tag != $this->_options['rowtag']) {
                $tmpRow[$tag] = $value;
            }
        }
         
        // set temporary variable to parents rgt value
        $rgtParent = $this->_rows[$parent]['rgt'];

        // expand tree to make room for new node by bumping rgt values by 2 where (rgt > rgtParent)
        array_walk($this->_rows, array(&$this, '_moveSetRight'), $rgtParent);

        // set this nodes lft and rgt values
        $lft = $rgtParent;
        $rgt = $rgtParent + 1;

        // append to rows object using this nodes rowidtag as the index
        $this->_rows[$tmpRow[$this->_options['rowidtag']]] = array_merge($tmpRow, array('lft'=>$lft, 'rgt'=>$rgt));

        // child paths were skipped above so, if this node contains children
        if (isset($row[$this->_options['rowtag']])) {
            // for each child path
            foreach($row[$this->_options['rowtag']] as $child) {
                // recursively parse data
                $this->_parseRows($child, $row[$this->_options['rowidtag']]);
            }
        }
    }

    /**
     * Expands node by 2
     * updates tree by reference
     *
     * @param array $tree reference
     * @param integer $id parentid
     * @param integer $rgt reference rgt position
     * @return void
     */
    private function _moveSetRight(array &$tree, $id, $rgt) {
        if ($tree['rgt'] >= $rgt) {
            $tree['rgt'] += 2;
        }
    }

    /**
     * Compress node by 2
     * updates tree by reference
     *
     * @param array $tree reference
     * @return void
     */
    private function _moveSetLeft(array &$tree)
    {
        $tree['lft'] -= 1;
        $tree['rgt'] -= 1;
    }

    /**
     * @return result array
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * @return result defined by adapter
     */
    public function getOutput()
    {
        return $this->_output;
    }

    // @codeCoverageIgnoreStart

    // =====================================
    // INPUT / OUTPUT
    // xml, array, object
    public function setInputAdapter() {}
    public function setReturnAdapter() {}

    // =====================================
    // SEARCH
    public function findItem($sql, $bind = array(), $fetchMode = null) {}
    public function findRoot($childid) {}

    // =====================================
    // LIST

    public function listTree($sql, $bind = array(), $fetchMode = null) {}
    public function listDirectChildren($parentId) {}
    public function listAllChildren($parentId) {}
    public function listParents($childid) {}
    public function listSiblings($childid) {}
    public function enumerateNodes($tree) {}

    // =====================================
    // INSERT
    public function addSection($section, $parentId) {}
    public function addSibling($parentId) {}
    public function addChild($parentId) {}

    // =====================================
    // MOVE
    public function moveNodeUp($nodeid, $levelsup) {}
    public function moveNode($nodeid, $newparentid) {}
    public function moveNodeLeft($nodeid) {}
    public function moveNodeRight($nodeid) {}
    
    /**
     * make parentid indexed array
     *
     * @return array
     */
    private function _parentArray()
    {
        $parentArray = array();
        foreach($this->getRows() as $rows) {
            $parentArray[$rows[$this->_options['parentidtag']]][] = $rows;
        }
        return $parentArray;
    }

    /**
     * sanity checks for functions
     *
     * @throws Nani_Tree_Exception if checks fail
     */
    private function _preCheck()
    {
        // parentidtag and rowidtag must be set
        if(empty($this->_options['parentidtag']))
        {
            require_once 'Nani/Tree/Exception.php';
            throw new Nani_Tree_Exception('No parent id tag specified.');
        }
    }
    
    // @codeCoverageIgnoreEnd
    
    // =====================================
    // DELETE
    
    /**
     * delete one node and move children up to parent
     * 
     * @param $nodeid
     * @return boolean true if successful
     */
    public function deleteNode($nodeid)
    {
        // get rows
        $rows = $this->getRows();
        $newRows = array();

        // get nodeid boundaries
        $lft = $rows[$nodeid]['lft'];
        $rgt = $rows[$nodeid]['rgt'];
        
        // get parent row
        $parent = $this->getParent($rows[$nodeid]);
        
        // step through array
        foreach($rows as $key => $row) {
            
            // skip over target
            if($row[$this->_options['rowidtag']] == $nodeid) continue;

            // append row
            $newRows[$key] = $row;
            
            // remove nodes within boundaries
            if($row['lft'] > $lft && $row['rgt'] < $rgt) {

                // parentidtag set and current row is child of target
                if(! empty($this->_options['parentidtag']) 
                    && $this->isChild($nodeid, $row[$this->_options['rowidtag']])) {

                    // parent id
                    if(! empty($parent)) {
                        $parentid = $parent[$this->_options['rowidtag']];
                    } else {
                        $parentid = 0;
                    }

                    // assign target parent to target children
                    $newRows[$key][$this->_options['parentidtag']] = $parentid;
                }
                
                // shift child left
                $newRows[$key]['lft'] -= 1;
                $newRows[$key]['rgt'] -= 1;
                
            // move rows on right to the left
            } elseif($row['lft'] > $rgt) {
                $newRows[$key]['lft'] -= 2;
                $newRows[$key]['rgt'] -= 2;
                
            // compress parents
            } elseif($row['rgt'] > $rgt) {
                $newRows[$key]['rgt'] -= 2;
            }
        }
        
        // replace rows
        $this->_rows = $newRows;
        
        $this->writeTree();
        
        return true;
    }

    /**
     * delete target and all descendants
     * 
     * @param integer $nodeid
     * @return boolean true if successful
     */
    public function deleteSection($nodeid)
    {
        // get rows
        $rows = $this->getRows();

        // get nodeid boundaries
        $lft = $rows[$nodeid]['lft'];
        $rgt = $rows[$nodeid]['rgt'];
        $width = $rgt - $lft + 1;
        
        // step through array
        foreach($rows as $key => $row) {
            // remove nodes within boundaries
            if($key == $nodeid || ($row['lft'] > $lft && $row['rgt'] < $rgt)) {
                unset($rows[$key]);
                
            // move rows on right to the left
            } elseif($row['lft'] > $rgt) {
                $rows[$key]['lft'] -= $width;
                $rows[$key]['rgt'] -= $width;
                
            // compress parents
            } elseif($row['rgt'] > $rgt) {
                $rows[$key]['rgt'] -= $width;
            }
        }

        $this->writeTree($rows);
        
        return true;
    }
    
    // =====================================
    // VALIDATION

    /**
    * check if childid is a direct descendant of parentid
    *
    * @param integer $parentId
    * @param integer $childid
    * @return boolean true if child is a direct descendant of parentid
    */
    public function isChild($parentId, $childid)
    {
        // step through each row object until the child is found
        foreach($this->getRows() as $row) {

            // found parent
            if($row[$this->_options['rowidtag']] == $parentId) {
                $parent = $row;
                
            // found expected child
            } elseif($row[$this->_options['rowidtag']] == $childid) {

                // check if parent found and child is within parents boundaries
                if(! empty($parent) && $row['lft'] > $parent['lft'] && $row['rgt'] < $parent['rgt'] ) {
                    
                    $childFound = true;
                    
                    // other children of parentid exist, check for nested parent
                    if(! empty($aryChildren)) {

                        // loop through children of parentid
                        foreach($aryChildren as $children) {

                            // if other parent found, exit
                            if($children['lft'] < $row['lft'] && $children['rgt'] > $row['rgt']) {
                                $childFound = false;
                            }
                        }
                    }

                    return $childFound;

                // parent not found or undefined relationship
                } else {
                    return false;
                }

            // found any child, save for later testing
            } elseif(! empty($parent) && $row['lft'] > $parent['lft'] && $row['rgt'] < $parent['rgt']) {
                $aryChildren[] = $row;
            }
        }
        
        // parent and/or child was not found
        return false;
    }

    /**
     * check if childid is a descendant of parentid
     * child can be any number of levels deep
     *
     * @param integer $parentId
     * @param integer $childid
     * @return boolean true if childid is a descendant of parentid
     */
    public function isDescendant($parentId, $descendantId)
    {
        unset($this->_parent);
        
        // step through each row object until the child is found
        foreach($this->getRows() as $row) {
            // found parent
            if($row[$this->_options['rowidtag']] == $parentId) {
                // found parent
                $this->_parent = $row;
                
            // found expected child
            } elseif($row[$this->_options['rowidtag']] == $descendantId) {
                // check if parent found and child is within parents boundaries
                if(! empty($this->_parent) && $row['lft'] > $this->_parent['lft'] 
                        && $row['rgt'] < $this->_parent['rgt'] ) {
                    
                    // child is valid
                    return true;

                }
            }
        }
        
        // parent and/or child was not found
        return false;
    }

    /**
     * check if siblingid and childid have same parent
     *
     * @param integer $childid
     * @param integer $siblingid
     * @return boolean true if childid is a descendant of parentid
     */
    public function isSibling($childid=0, $siblingid=0)
    {
        // if either parameter not provided or they are equal
        if(($childid == 0 || $siblingid == 0) || ($childid == $siblingid)) {
            require_once 'Nani/Tree/Exception.php';
            throw new Nani_Tree_Exception("Invalid Id");
        }
        
        $child = $this->getOneRow($childid);
        $childParent = $this->getParent($child);

        $sibling = $this->getOneRow($siblingid);
        $siblingParent = $this->getParent($sibling);
        
        $result = false;
        
        /*
         * tests:
         * - both are top-level siblings
         * - both parents exist AND parentId is the same 
         */
        if((empty($childParent) && empty($siblingParent)) 
            || ((! empty($childParent) && ! empty($siblingParent)) 
                && $childParent[$this->_options['rowidtag']] == $siblingParent[$this->_options['rowidtag']])) {

            // siblings
            $result = true;
            
        }
        
        return $result;

    }

    /**
     * get row by id
     * 
     * @param integer $id
     * @return array
     */
    public function getOneRow($id)
    {
        $row = array();
        // step through each row object until the child is found
        foreach($this->getRows() as $key => $row) {
            // found row
            if($row[$this->_options['rowidtag']] == $id) {
                break;
            }
        }

        return $row;
    }

    /**
     * gets parent object of child
     * 
     * @param array $child
     * @return array $parent
     */
    public function getParent(array $child=array())
    {
        $parent = array();
        
        foreach($this->getRows() as $row) {
            // if $row is outside child boundaries
            if($row['lft'] < $child['lft'] && $row['rgt'] > $child['rgt']) {
                // save row
                $parent = $row;
            }
        }
        
        // last parent is direct parent
        return $parent;
    }
    
    /**
     * Abstract Methods
     */

    /**
     * Return tree via called adapter
     *
     * If adapter is xml, returned datatype is valid XML string
     * If adapter is array, returned datatype is array
     * If adapter is db, tree is written to database table
     *
     * @return mixed
     */
    abstract protected function writeTree();

}
