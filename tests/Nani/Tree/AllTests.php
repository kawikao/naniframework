<?php

/**
 * Nani Framework
 *
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id: AllTests.php 6 2010-03-02 16:58:20Z sandiegophp $
 */

// Call Nani_Tree_AllTests::main() if this source file is executed directly.
defined('PHPUnit_MAIN_METHOD')
    || define('PHPUnit_MAIN_METHOD', 'Nani_Tree_AllTests::main');

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @group       Nani_Tree
 */
class Nani_Tree_AllTests
{

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
	public static function main()
	{
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

    /**
     * Regular suite
     * 
     * @return PHPUnit_Framework_TestSuite
     */
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Nani Framework - Nani - Tree');

		self::_addTreeTestSuites($suite, 'Xml');
		self::_addTreeTestSuites($suite, 'Array');

		return $suite;
	}

    /**
     * load class and add test suite
     */
	protected static function _addTreeTestSuites($suite, $driver)
	{
		require_once dirname(__FILE__) . "/Adapter/{$driver}Test.php";
		$suite->addTestSuite("Nani_Tree_Adapter_{$driver}Test");
	}

}

if (PHPUnit_MAIN_METHOD == 'Nani_Tree_AllTests::main') {
	Nani_Tree_AllTests::main();
}
