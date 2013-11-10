<?php

/**
 * Nani Framework
 *
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id: TestSetup.php 33 2010-03-16 14:54:06Z sandiegophp $
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Nani_Tree
 */
require_once 'Nani/Tree.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Exclude from code coverage report
 */
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * Sets up database
 * 
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright  	Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license    	http://opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Nani_Tree_TestSetup extends PHPUnit_Framework_TestCase
{
    protected $_db = null;

    public abstract function getDriver();

    protected function setUp()
    {
        if(TESTS_ZEND_ENABLED == true) {
            $this->_setupDbAdapter();
        }   
    }

    protected function tearDown()
    {
        if(! empty($this->_db)) {
            $this->_adapter = null;
            $this->_db->query('DROP TABLE [treetest]');
            $this->_db = null;
        }
    }

    private function _setupDbAdapter($optionalParams = array())
    {
        require_once 'Zend/Db.php';
        require_once 'Zend/Db/Adapter/Pdo/Sqlite.php';

        $params = array('dbname' => ':memory');

        if (!empty($optionalParams)) {
            $params['options'] = $optionalParams;
        }

        $this->_db = new Zend_Db_Adapter_Pdo_Sqlite($params);

        $sqlCreate = 'CREATE TABLE [treetest] ( '
                   . '[id] INTEGER  NOT NULL PRIMARY KEY, '
                   . '[parentid] INTEGER NOT NULL, '
                   . '[settype] VARCHAR NOT NULL, '
                   . '[lft] INTEGER NOT NULL, '
                   . '[rgt] INTEGER NOT NULL)';
        $obj = $this->_db->query($sqlCreate);

        // nested : good
        $this->_db->query("INSERT INTO treetest (id, parentid, settype, lft, rgt) VALUES (1,0,'nested',1,6)");
        $this->_db->query("INSERT INTO treetest (id, parentid, settype, lft, rgt) VALUES (2,1,'nested',2,5)");
        $this->_db->query("INSERT INTO treetest (id, parentid, settype, lft, rgt) VALUES (3,2,'nested',3,4)");
        $this->_db->query("INSERT INTO treetest (id, parentid, settype, lft, rgt) VALUES (4,0,'nested',7,8)");
        $this->_db->query("INSERT INTO treetest (id, parentid, settype, lft, rgt) VALUES (5,0,'nested',9,10)");
        // flat : good
        $this->_db->query("INSERT INTO treetest (id, parentid, settype, lft, rgt) VALUES (11,0,'flat',1,2)");
        $this->_db->query("INSERT INTO treetest (id, parentid, settype, lft, rgt) VALUES (12,1,'flat',3,4)");
        $this->_db->query("INSERT INTO treetest (id, parentid, settype, lft, rgt) VALUES (13,2,'flat',5,6)");
        $this->_db->query("INSERT INTO treetest (id, parentid, settype, lft, rgt) VALUES (14,0,'flat',7,8)");
        $this->_db->query("INSERT INTO treetest (id, parentid, settype, lft, rgt) VALUES (15,0,'flat',9,10)");
    }
}
