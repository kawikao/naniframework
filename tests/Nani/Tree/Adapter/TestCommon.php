<?php

/**
 * Nani Framework
 *
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id: TestCommon.php 32 2010-03-15 15:19:27Z sandiegophp $
 */

/**
 * @see UserTree
 */
require_once dirname(__FILE__) . '/_files/treeArray.php';

/**
 * @see Nani_Tree_TestSetup
 */
require_once 'Nani/Tree/TestSetup.php';

/**
 * Exclude from code coverage report
 */
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * Tests common to all adapters
 *
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Nani_Tree_Adapter_TestCommon extends Nani_Tree_TestSetup
{
    protected function setUp()
    {
        parent::setUp();
        $this->_driverClass = 'Nani_Tree_Adapter_' . $this->getDriver();
        $this->_commonData = 'nested' . $this->getDriver();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function getCommonTestData()
    {
        $filesPath = dirname(__FILE__) . '/_files';
        return array(
            'nestedFormatted'               => $filesPath . '/nestedFormatted.xml',
            'flatFormatted'                 => $filesPath . '/flatFormatted.xml',
            'nestedXml'                     => $filesPath . '/nestedFormatted.xml',
            'siblingXml'                    => $filesPath . '/sibling.xml',
        
            'flatUnformatted'               => file_get_contents($filesPath . '/flatUnformatted.xml'),  
            'resultFlatUnformatted'         => file_get_contents($filesPath . '/resultFlatUnformatted.xml'),  
            'resultNestedUnformatted'       => file_get_contents($filesPath . '/resultNestedUnformatted.xml'),  
            'resultDeleteGrandChildXml'     => file_get_contents($filesPath . '/resultDeleteGrandChild.xml'),  
            'resultDeleteMainXml'           => file_get_contents($filesPath . '/resultDeleteMain.xml'),  
            'resultDeleteChildXml'          => file_get_contents($filesPath . '/resultDeleteChild.xml'),  
            'resultDeleteMainNodeXml'       => file_get_contents($filesPath . '/resultDeleteMainNode.xml'),  
            'resultDeleteChildNodeXml'      => file_get_contents($filesPath . '/resultDeleteChildNode.xml'),  
        
            'flatArray'                     => UserTree::$treeFlat,  
            'siblingArray'                  => UserTree::$treeNestedSiblings,
            'nestedArray'                   => UserTree::$treeNested,

        	'resultNestedArray'             => UserTree::$resultNestedArray,
            'resultDeleteMainArray'         => UserTree::$resultDeleteMain,
            'resultDeleteChildArray'        => UserTree::$resultDeleteChild,
            'resultDeleteGrandChildArray'   => UserTree::$resultDeleteGrandChild,
            'resultDeleteMainNodeArray'     => UserTree::$resultDeleteMainNode,
            'resultDeleteChildNodeArray'    => UserTree::$resultDeleteChildNode,
        
            'stdOptions'                    => array('rowtag'       => 'row',
                                                     'rowidtag'     => 'userid', 
                                                     'parentidtag'  => 'parentid',
                                                    )
        );
    }

    public function testCommonTreeShouldReturnInstance()
    {
        $tree = new Nani_Tree($this->getDriver(), 
                              $this->_data['nestedArray'], 
                              $this->_data['stdOptions']);
        $this->assertTrue($tree->_adapter instanceof $this->_driverClass);
    }
    
    public function testCommonTreeBadDriverShouldRaiseException()
    {
        try {
            $tree = new Nani_Tree('BadDriver', 
                                  $this->_data['nestedArray'], 
                                  $this->_data['stdOptions']);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
    }
    
    public function testCommonInstantiateShouldReturnInstance()
    {
        // nested
        $adapter = new $this->_driverClass($this->_data['nestedArray'], $this->_data['stdOptions']);
        $this->assertTrue($adapter instanceof $this->_driverClass);
        // flat
        $adapter = new $this->_driverClass($this->_data['flatArray'], $this->_data['stdOptions']);
        $this->assertTrue($adapter instanceof $this->_driverClass);
    }

    public function testCommonInvalidOptionIndexesShouldRaiseException()
    {
        // no options
        try {
            $adapter = new $this->_driverClass($this->_data[$this->_commonData]);
            $this->fail("exception expected");
        } catch(Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
        // empty array
        try {
            $adapter = new $this->_driverClass($this->_data[$this->_commonData], array());
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
        // container only
        try {
            $options = array('container' => 'rows');
            $adapter = new $this->_driverClass($this->_data[$this->_commonData], $options);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
        // rowtag only
        try {
            $options = array('rowtag' => 'row');
            $adapter = new $this->_driverClass($this->_data[$this->_commonData], $options);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
        // rowidtag only
        try {
            $options = array('rowidtag' => 'userid');
            $adapter = new $this->_driverClass($this->_data[$this->_commonData], $options);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
        // missing rowtag
        try {
            $options = array('rowidtag' => 'userid', 'parentidtag' => 'parentid');
            $adapter = new $this->_driverClass($this->_data[$this->_commonData], $options);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
        // missing rowidtag
        try {
            $options = array('rowtag' => 'row', 'parentidtag' => 'parentid');
            $adapter = new $this->_driverClass($this->_data[$this->_commonData], $options);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
        // parentidtag only
        try {
            $options = array('parentidtag' => 'parentid');
            $adapter = new $this->_driverClass($this->_data[$this->_commonData], $options);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
    }

    public function testCommonInvalidOptionValuesShouldRaiseException()
    {
        // invalid rowtag
        try {
            $options = array('rowtag' => 'badvalue', 'rowidtag' => 'userid');
            $adapter = new $this->_driverClass($this->_data[$this->_commonData], $options);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
        // invalid rowidtag
        try {
            $options = array('rowtag' => 'row', 'rowidtag' => 'badvalue');
            $adapter = new $this->_driverClass($this->_data[$this->_commonData], $options);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Undefined index', $e->getMessage());
        }
    }

    public function testCommonObjectAsSourceShouldRaiseException()
    {
        try {
            $adapter = new $this->_driverClass($this, array());
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Data Object must be an array,', $e->getMessage());
        }
    }

    public function testCommonDirectChildShouldReturnTrue()
    {
        $options = array('rowtag' => 'row',
                        'rowidtag' => 'userid', 
                        'parentidtag' => 'parentid',
        );
        $adapter = new $this->_driverClass($this->_data[$this->_commonData], $options);

        // is a child
        $this->assertTrue($adapter->isChild(1,2));

        // parent not found
        $this->assertFalse($adapter->isChild(0,2));
        // child is not a direct descendant
        $this->assertFalse($adapter->isChild(1,3));
        // parent and child do not exist
        $this->assertFalse($adapter->isChild(0,-1));
    }

    public function testCommonDescendantShouldReturnTrue()
    {
        $adapter = new $this->_driverClass($this->_data[$this->_commonData], $this->_data['stdOptions']);
        // 1 level deep
        $this->assertTrue($adapter->isDescendant(1,2));
        // 2 levels deep
        $this->assertTrue($adapter->isDescendant(1,3));

        // not descendant
        $this->assertFalse($adapter->isDescendant(1,4));
    }

    public function testCommonSiblingsShouldReturnTrue()
    {
        $adapter = new $this->_driverClass($this->_data['sibling' . $this->getDriver()], $this->_data['stdOptions']);

        // top-level siblings
        $this->assertTrue($adapter->isSibling(1,4));
        // 2nd level siblings
        $this->assertTrue($adapter->isSibling(2,3));
        // 3rd level siblings
        $this->assertTrue($adapter->isSibling(21,22));
        
        // descendant should return false
        $this->assertFalse($adapter->isSibling(1,3));

        // missing parameter
        try {
            $adapter->isSibling(1);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Invalid Id', $e->getMessage());
        }

        // same parameters
        try {
            $adapter->isSibling(1,1);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('Invalid Id', $e->getMessage());
        }
    }

    public function testCommonDeleteSectionShouldReturnTrue()
    {
        // main parent
        $adapter = new $this->_driverClass($this->_data['nested' . $this->getDriver()], $this->_data['stdOptions']);
        $this->assertTrue($adapter->deleteSection(1));
        $this->assertEquals($adapter->getOutput(), $this->_data['resultDeleteMain' . $this->getDriver()]);

        // 1st level nested
        $adapter = new $this->_driverClass($this->_data['nested' . $this->getDriver()], $this->_data['stdOptions']);
        $this->assertTrue($adapter->deleteSection(2));
        $this->assertEquals($adapter->getOutput(), $this->_data['resultDeleteChild' . $this->getDriver()]);

        // 2nd level nested
        $adapter = new $this->_driverClass($this->_data['nested' . $this->getDriver()], $this->_data['stdOptions']);
        $this->assertTrue($adapter->deleteSection(3));
        $this->assertEquals($adapter->getOutput(), $this->_data['resultDeleteGrandChild' . $this->getDriver()]);
    }
    
    public function testCommonDeleteNodeShouldReturnTrue()
    {
        $adapter = new $this->_driverClass($this->_data['nested' . $this->getDriver()], $this->_data['stdOptions']);
        $this->assertTrue($adapter->deleteNode(1));
        $this->assertEquals($adapter->getOutput(), $this->_data['resultDeleteMainNode' . $this->getDriver()]);

        // 
        $adapter = new $this->_driverClass($this->_data['nested' . $this->getDriver()], $this->_data['stdOptions']);
        $this->assertTrue($adapter->deleteNode(2));
        $this->assertEquals($adapter->getOutput(), $this->_data['resultDeleteChildNode' . $this->getDriver()]);
    }
    
}
