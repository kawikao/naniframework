<?php

/**
 * Nani Framework
 *
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id: XmlTest.php 30 2010-03-13 07:46:34Z sandiegophp $
 */

/**
 * Call Nani_Tree_Adapter_XmlTest::main() if this source file is executed directly.
 * @ignore
 */
defined('PHPUnit_MAIN_METHOD')
    || define('PHPUnit_MAIN_METHOD', 'Nani_Tree_Adapter_XmlTest::main');

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Nani_Tree_Adapter_TestCommon
 */
require_once 'Nani/Tree/Adapter/TestCommon.php';

/**
 * @see Nani_Tree_Adapter_Xml
 */
require_once 'Nani/Tree/Adapter/Xml.php';

/**
 * Exclude from code coverage report
 */
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Nani_Tree_Adapter_XmlTest extends Nani_Tree_Adapter_TestCommon
{

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->_data = parent::getCommonTestData();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testFilenameShouldReturnInstance()
    {
        // nested
        $adapter = new Nani_Tree_Adapter_Xml($this->_data['nestedFormatted'], $this->_data['stdOptions']);
        $this->assertTrue($adapter instanceof Nani_Tree_Adapter_Xml);

        // flat
        $adapter = new Nani_Tree_Adapter_Xml($this->_data['flatFormatted'], $this->_data['stdOptions']);
        $this->assertTrue($adapter instanceof Nani_Tree_Adapter_Xml);
    }

    public function testXmlStringShouldReturnInstance()
    {
        // nested xml
        $xml = file_get_contents($this->_data['nestedFormatted']);
        $adapter = new Nani_Tree_Adapter_Xml($xml, $this->_data['stdOptions']);
        $this->assertTrue($adapter instanceof Nani_Tree_Adapter_Xml);

        // flat xml
        $xml = file_get_contents($this->_data['flatFormatted']);
        $adapter = new Nani_Tree_Adapter_Xml($xml, $this->_data['stdOptions']);
        $this->assertTrue($adapter instanceof Nani_Tree_Adapter_Xml);

    }

    public function testFlatUnformattedXmlShouldReturnResult()
    {
        $options = array('rowtag' => 'row',
                        'rowidtag' => 'userid', 
                        'formatOutput' => false, 
        );
        $adapter = new Nani_Tree_Adapter_Xml($this->_data['flatUnformatted'], $options);
        $xml = explode("\n", $adapter->getOutput());
        $this->assertEquals($xml[1], $this->_data['resultFlatUnformatted']);
    }

    public function testNestedXmlWithoutParentIdShouldReturnNestedResult()
    {
        $options = array('rowtag' => 'row',
                        'rowidtag' => 'userid', 
        				'formatOutput' => false, 
        );
        $adapter = new Nani_Tree_Adapter_Xml($this->_data['nestedFormatted'], $options);
        $xml = explode("\n", $adapter->getOutput());
        $this->assertEquals($xml[1], $this->_data['resultNestedUnformatted']);
    }

    public function testFlatXmlWithParentIdShouldReturnNestedResult()
    {
        // flat w/parentidtag
        $options = array('rowtag' => 'row',
	                    'rowidtag' => 'userid', 
						'parentidtag' => 'parentid', 
        				'formatOutput' => false, 
        );
        $adapter = new Nani_Tree_Adapter_Xml($this->_data['flatFormatted'], $options);
        $xml = explode("\n", $adapter->getOutput());
        $this->assertEquals($xml[1], $this->_data['resultNestedUnformatted']);
    }

    public function testFlatXmlWithoutParentIdShouldReturnFlatResult()
    {
        // flat w/out parentidtag
        $options = array('rowtag' => 'row',
                        'rowidtag' => 'userid', 
                        'formatOutput' => false, 
        );
        $adapter = new Nani_Tree_Adapter_Xml($this->_data['flatFormatted'], $options);
        $xml = explode("\n", $adapter->getOutput());
        $this->assertEquals($xml[0], '<?xml version="1.0" encoding="utf-8"?>');
        $this->assertEquals($xml[1], $this->_data['resultFlatUnformatted']);
    }

    public function testFormatOutputOptionSetToTrueShouldReturnOneTagPerLine()
    {
        $options = array('rowtag'      => 'row',
                        'rowidtag'     => 'userid',
                        'formatOutput' => true,
        );
        $adapter = new Nani_Tree_Adapter_Xml($this->_data['flatFormatted'], $options);
        $xml = explode("\n", $adapter->getOutput());
        $this->assertEquals($xml[0], '<?xml version="1.0" encoding="utf-8"?>');
        $this->assertEquals($xml[1], '<rows>');
        $this->assertEquals($xml[2], '  <row>');
    }

    public function testInvalidXmlContainerValueShouldRaiseException()
    {
        // invalid container
        try {
            $options = array('container' => 'bad name', 'rowtag' => 'row', 'rowidtag' => 'userid');
            $adapter = new Nani_Tree_Adapter_Xml($this->_data['nestedFormatted'], $options);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertEquals('Invalid Character Error', $e->getMessage());
        }
    }

    public function testInvalidFileShouldRaiseException()
    {
        // no filename
        try {
            $adapter = new Nani_Tree_Adapter_Xml();
            $this->fail("exception expected");
        } catch(Exception $e) {
            $this->assertContains('Missing argument 1', $e->getMessage());
        }
        // bad filename
        try {
            $adapter = new Nani_Tree_Adapter_Xml('badfile.xml', $this->_data['stdOptions']);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('must be an array,', $e->getMessage());
        }
        // empty file
        try {
            $adapter = new Nani_Tree_Adapter_Xml(dirname(__FILE__) . '/_files/empty.xml', $this->_data['stdOptions']);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('must be an array,', $e->getMessage());
        }
        // xml declaration only
        try {
            $adapter = new Nani_Tree_Adapter_Xml(dirname(__FILE__) . '/_files/declarationOnly.xml'
                                               , $this->_data['stdOptions']);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('must be an array,', $e->getMessage());
        }
    }

    public function testInvalidXmlStringShouldRaiseException()
    {
        // empty string
        try {
            $adapter = new Nani_Tree_Adapter_Xml('', $this->_data['stdOptions']);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('must be an array, XML string', $e->getMessage());
        }
        // invalid xml
        try {
            $adapter = new Nani_Tree_Adapter_Xml('invalidString', $this->_data['stdOptions']);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('must be an array,', $e->getMessage());
        }
        // xml declaration only
        try {
            $adapter = new Nani_Tree_Adapter_Xml('<?xml version="1.0" encoding="UTF-8" ?>'
                                               , $this->_data['stdOptions']);
            $this->fail("exception expected");
        } catch(Nani_Tree_Exception $e) {
            $this->assertContains('must be an array,', $e->getMessage());
        }
    }

    public function getDriver()
    {
        return 'Xml';
    }

}

if (PHPUnit_MAIN_METHOD == 'Nani_Tree_Adapter_XmlTest::main') {
    Nani_Tree_Adapter_XmlTest::main();
}
