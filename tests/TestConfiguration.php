<?php

/**
 * Nani Framework
 *
 * @category    Nani
 * @package     Nani
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id: TestConfiguration.php 34 2010-03-19 17:26:00Z sandiegophp $
 */

/**
 * GENERAL SETTINGS
 *
 * OB_ENABLED should be set to true for tests that require headers not be sent
 * to check if all functionality works as expected.
 */
define('TESTS_NANI_OB_ENABLED', false);

/**
 * PHPUnit Code Coverage / Test Report
 */
define('TESTS_GENERATE_REPORT', true);
define('TESTS_GENERATE_REPORT_TARGET', dirname(__FILE__) . '/../documentation/coveragereport');

//define('TESTS_ZEND_AUTH_ADAPTER_DBTABLE_PDO_SQLITE_ENABLED', true);
//define('TESTS_ZEND_AUTH_ADAPTER_DBTABLE_PDO_SQLITE_DATABASE', ':memory:');
define('TESTS_ZEND_ENABLED', false);