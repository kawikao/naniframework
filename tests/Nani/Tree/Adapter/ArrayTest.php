<?php

/**
 * Nani Framework
 *
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id: ArrayTest.php 35 2010-03-19 22:07:24Z sandiegophp $
 */

/**
 * Call Nani_Tree_Adapter_ArrayTest::main() if this source file is executed directly.
 */
defined('PHPUnit_MAIN_METHOD')
    || define('PHPUnit_MAIN_METHOD', 'Nani_Tree_Adapter_ArrayTest::main');

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Nani_Tree_Adapter_TestCommon
 */
require_once 'Nani/Tree/Adapter/TestCommon.php';

/**
 * Exclude from code coverage report
 */
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright	Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Nani_Tree_Adapter_ArrayTest extends Nani_Tree_Adapter_TestCommon
{

    protected $_data = null;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->_data = parent::getCommonTestData();

        require_once 'Nani/Tree/Adapter/Array.php';
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testArrayShouldReturnInstance()
    {
        // nested
        $adapter = new Nani_Tree_Adapter_Array($this->_data['nestedArray'], $this->_data['stdOptions']);
        $this->assertTrue($adapter instanceof Nani_Tree_Adapter_Array);
        // flat
        $adapter = new Nani_Tree_Adapter_Array($this->_data['flatArray'], $this->_data['stdOptions']);
        $this->assertTrue($adapter instanceof Nani_Tree_Adapter_Array);
    }

    public function testNestedArrayWithoutParentIdShouldReturnNestedResult()
    {
        $options = array('rowtag' => 'row',
                        'rowidtag' => 'userid', 
        );
        $adapter = new Nani_Tree_Adapter_Array($this->_data['nestedArray'], $options);
        $rows = $adapter->getRows();
        $this->assertEquals($rows, $this->_data['resultNestedArray']);
    }

    public function testFlatArrayWithParentIdShouldReturnNestedResult()
    {
        // flat w/parentidtag
        $adapter = new Nani_Tree_Adapter_Array($this->_data['nestedArray'], $this->_data['stdOptions']);
        $rows = $adapter->getRows();
        $this->assertEquals($rows, $this->_data['resultNestedArray']);
    }

    public function testFlatArrayWithoutParentIdShouldReturnFlatResult()
    {
        // flat w/out parentidtag
        $options = array('rowtag' => 'row',
                        'rowidtag' => 'userid', 
        );
        $adapter = new Nani_Tree_Adapter_Array($this->_data['flatArray'], $options);
        $rows = $adapter->getRows();
        $this->assertEquals($rows[1]['lft'], 1);
        $this->assertEquals($rows[1]['rgt'], 2);
        $this->assertEquals($rows[2]['lft'], 3);
        $this->assertEquals($rows[2]['rgt'], 4);
        $this->assertEquals($rows[3]['lft'], 5);
        $this->assertEquals($rows[3]['rgt'], 6);
    }

    public function testInvalidArrayShouldRaiseException()
    {
        // non-array
        try {
            $adapter = new Nani_Tree_Adapter_Array('');
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('must be an array', $e->getMessage());
        }
        // empty array
        try {
            $adapter = new Nani_Tree_Adapter_Array(array(), $this->_data['stdOptions']);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
        // invalid array
        try {
            $adapter = new Nani_Tree_Adapter_Array(array('badindex'=>'badvalue'), $this->_data['stdOptions']);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
    }
    
    public function getDriver()
    {
        return 'Array';
    }

}

if (PHPUnit_MAIN_METHOD == 'Nani_Tree_Adapter_ArrayTest::main') {
    Nani_Tree_Adapter_ArrayTest::main();
}
