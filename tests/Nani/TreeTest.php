<?php

/**
 * Nani Framework
 *
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id$
 */

/**
 * Call Nani_TreeTest::main() if this source file is executed directly.
 */
defined('PHPUnit_MAIN_METHOD')
    || define('PHPUnit_MAIN_METHOD', 'Nani_TreeTest::main');

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../TestHelper.php';

require_once dirname(__FILE__) . '/Tree/Adapter/_files/treeArray.php';

require_once 'Nani/Tree.php';

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
class Nani_TreeTest extends PHPUnit_Framework_TestCase
{

    protected $_data = null;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    protected function setUp()
    {
        $this->_data = array(
            'nestedArray'   => UserTree::$treeNested,
            'stdOptions'    => array('rowtag'       => 'row',
                                     'rowidtag'     => 'userid', 
                                     'parentidtag'  => 'parentid',
                               )
        );
    }

    protected function tearDown()
    {
    }

    public function testCreateShouldReturnInstance()
    {
        $adapter = new Nani_Tree('Array', $this->_data['nestedArray'], $this->_data['stdOptions']);
        $this->assertTrue($adapter instanceof Nani_Tree);
    }

    public function testAdapterClassThatDoesNotExtendAbstractShouldRaiseException()
    {
        // load bad class definition
        require_once dirname(__FILE__) . '/Tree/Adapter/_files/BadClass.php';
       
        try {
            $adapter = new Nani_Tree('Arrays', $this->_data['nestedArray'], $this->_data['stdOptions']);

            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('does not extend Nani_Tree_Adapter_Abstract', $e->getMessage());
        }
    }

    public function testGetAdapter()
    {
        $adapter = new Nani_Tree('Array', $this->_data['nestedArray'], $this->_data['stdOptions']);
        $this->assertTrue($adapter->getAdapter() instanceof Nani_Tree_Adapter_Array);
    }

}

if (PHPUnit_MAIN_METHOD == 'Nani_TreeTest::main') {
    Nani_TreeTest::main();
}
