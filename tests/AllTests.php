<?php

/**
 * Nani Framework
 *
 * @category    Nani
 * @package     Nani
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id: AllTests.php 9 2010-03-02 23:28:41Z sandiegophp $
 */

// Call AllTests::main() if this source file is executed directly.
defined('PHPUnit_MAIN_METHOD')
    || define('PHPUnit_MAIN_METHOD', 'AllTests::main');

/**
 * Test helper
 */
require_once 'TestHelper.php';

/**
 * @see Nani_AllTests
 */
require_once 'Nani/AllTests.php';

/**
 * @category   Nani
 * @package    Nani
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
class AllTests
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
	public static function main()
	{
		$parameters = array();

		if (TESTS_GENERATE_REPORT && extension_loaded('xdebug')) {
			$parameters['reportDirectory'] = TESTS_GENERATE_REPORT_TARGET;
		}

		PHPUnit_TextUI_TestRunner::run(self::suite(), $parameters);
	}

	/**
	 * Regular suite
	 * 
	 * @return PHPUnit_Framework_TestSuite
	 */
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Nani Framework');

		$suite->addTest(Nani_AllTests::suite());

		return $suite;
	}
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
	AllTests::main();
}
