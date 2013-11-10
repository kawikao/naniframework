<?php

/**
 * Nani Framework
 *
 * @category    Nani
 * @package     Nani
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id: AllTests.php 22 2010-03-07 22:18:46Z sandiegophp $
 */

// Call Nani_AllTests::main() if this source file is executed directly.
defined('PHPUnit_MAIN_METHOD')
    || define('PHPUnit_MAIN_METHOD', 'Nani_AllTests::main');

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * @see Nani_Application_AllTests
 */
require_once 'Nani/Tree/AllTests.php';

require_once 'TreeTest.php';

/**
 * @category   Nani
 * @package    Nani
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Nani_AllTests
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
		$suite = new PHPUnit_Framework_TestSuite('Nani Framework - Nani');

		// Start tests...
		$suite->addTestSuite('Nani_TreeTest');
		$suite->addTest(Nani_Tree_AllTests::suite());
		
		return $suite;
	}
}

if (PHPUnit_MAIN_METHOD == 'Nani_AllTests::main') {
	Nani_AllTests::main();
}
