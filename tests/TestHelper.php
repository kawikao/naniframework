<?php

/**
 * Nani Framework
 *
 * @category    Nani
 * @package     Nani
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id: TestHelper.php 34 2010-03-19 17:26:00Z sandiegophp $
 */

/*
 * Determine the root, library, and tests directories of the framework distribution
 */
$naniRoot           = dirname(__FILE__) . '/..';
$naniCoreLibrary    = "$naniRoot/library";
$naniCoreTests      = "$naniRoot/tests";

/*
 * Prepend the Nani Framework library/ and tests/ directories to the include_path.
 */
$path = array($naniCoreLibrary, 
            $naniCoreTests, 
            $naniRoot, 
            '/usr/share/php', 
            get_include_path(), 
);
set_include_path(implode(PATH_SEPARATOR, $path));
//echo get_include_path();

/**
 * Include PHPUnit dependencies
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

/*
 * Set error reporting to the level to which Zend Framework code must comply.
 */
error_reporting( E_ALL | E_STRICT );

/*
 * Omit from code coverage reports the contents of the tests directory
 */
foreach (array('php', 'phtml', 'csv') as $suffix) {
	PHPUnit_Util_Filter::addDirectoryToFilter($naniCoreTests, ".$suffix");
}

/*
 * Load the user-defined test configuration file, if it exists; otherwise, load
 * the default configuration.
 */
if (is_readable($naniCoreTests . '/TestConfiguration.php')) {
	require_once $naniCoreTests . '/TestConfiguration.php';
} else {
	require_once $naniCoreTests . '/TestConfiguration.php.dist';
}

/**
 * Start output buffering, if enabled
 */
if (defined('TESTS_NANI_OB_ENABLED') && constant('TESTS_NANI_OB_ENABLED')) {
	ob_start();
}

/*
 * Add Nani  application/ directory to the PHPUnit code coverage whitelist
 */
if (defined('TESTS_GENERATE_REPORT')
&& TESTS_GENERATE_REPORT === true
&& version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')
) {
	PHPUnit_Util_Filter::addDirectoryToWhitelist($naniCoreLibrary);
}

/*
 * Unset global variables that are no longer needed.
 */
unset($naniRoot, $naniCoreApplication, $naniCoreTests, $path);
