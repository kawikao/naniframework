<?php

/**
 * Nani Framework
 *
 * @category    Nani
 * @package     Nani_Tree
 * @subpackage  UnitTests
 * @copyright   Copyright (c) 2010 Kawika Ohumukini (http://code.google.com/p/naniframework/)
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id: treeArray.php 32 2010-03-15 15:19:27Z sandiegophp $
 */

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
class UserTree {
    public static $treeNested = array(
        'row' => array(
            array('userid' => 1, 
                'parentid' => 0, 
                'data'  => null, 
                'row'  => array(
                    array('userid' => 2, 
                        'parentid' => 1, 
                        'data'  => 'data2', 
                        'row'  => array(
                            array('userid' => 3, 
                                'parentid' => 2, 
                                'data'  => 'data3', 
                            ),
                        ),
                    ),
                ),
           ),
	       array('userid' => 4, 
                'parentid' => 0, 
                'data'  => 'data4', 
	       ),
	       array(
                'userid' => 5, 
                'parentid' => 0, 
                'data'  => 'data5', 
	       )
	   )
	);

	public static $treeFlat = array(
        'row' => array(
            array('userid' => 1, 
                'parentid' => 0, 
                'data'  => null, 
	       ),
	       array('userid' => 2, 
                'parentid' => 1, 
                'data'  => 'data2', 
	       ),
	       array('userid' => 3, 
                'parentid' => 2, 
                'data'  => 'data3', 
	       ),
	       array('userid' => 4, 
                'parentid' => 0, 
                'data'  => 'data4', 
	       ),
	       array('userid' => 5, 
                'parentid' => 0, 
                'data'  => 'data5', 
	       )
	   )
	);

    public static $treeNestedSiblings = array(
        'row' => array(
            array('userid' => 1, 
                'parentid' => 0, 
                'data'  => null, 
                'row'  => array(
                    array('userid' => 2, 
                        'parentid' => 1, 
                        'data'  => 'data2', 
                    ), 
                    array('userid' => 3, 
                        'parentid' => 1, 
                        'data'  => 'data3', 
                    ),
                ),
           ),
	       array('userid' => 4, 
                'parentid' => 0, 
                'data'  => 'data4', 
	       ),
	       array(
                'userid' => 5, 
                'parentid' => 0, 
                'data'  => 'data5', 
	       )
	   )
	);

    public static $resultNestedArray = array(
        1 => array(
            'userid' => 1,
            'parentid' => 0,
            'data' => null,
            'lft' => 1,
            'rgt' => 6,
        ),
        2 => array(
            'userid' => 2,
            'parentid' =>1,
            'data' => 'data2',
            'lft' => 2,
            'rgt' => 5,
        ),
        3 => array(
            'userid' => 3,
            'parentid' =>2,
            'data' => 'data3',
            'lft' => 3,
            'rgt' => 4,
        ),
        4 => array(
            'userid' => 4,
            'parentid' =>0,
            'data' => 'data4',
            'lft' => 7,
            'rgt' => 8,
        ),
        5 => array(
            'userid' => 5,
            'parentid' =>0,
            'data' => 'data5',
            'lft' => 9,
            'rgt' => 10,
        ),
    );

    public static $resultDeleteMain = array(
        4 => array(
            'userid' => 4,
            'parentid' =>0,
            'data' => 'data4',
            'lft' => 1,
            'rgt' => 2,
        ),
        5 => array(
            'userid' => 5,
            'parentid' =>0,
            'data' => 'data5',
            'lft' => 3,
            'rgt' => 4,
        ),
    );
    
    public static $resultDeleteChild = array(
        1 => array(
            'userid' => 1,
            'parentid' => 0,
            'data' => null,
            'lft' => 1,
            'rgt' => 2,
        ),
        4 => array(
            'userid' => 4,
            'parentid' =>0,
            'data' => 'data4',
            'lft' => 3,
            'rgt' => 4,
        ),
        5 => array(
            'userid' => 5,
            'parentid' =>0,
            'data' => 'data5',
            'lft' => 5,
            'rgt' => 6,
        ),
    );

    public static $resultDeleteGrandChild = array(
        1 => array(
            'userid' => 1,
            'parentid' => 0,
            'data' => null,
            'lft' => 1,
            'rgt' => 4,
        ),
        2 => array(
            'userid' => 2,
            'parentid' =>1,
            'data' => 'data2',
            'lft' => 2,
            'rgt' => 3,
        ),
        4 => array(
            'userid' => 4,
            'parentid' =>0,
            'data' => 'data4',
            'lft' => 5,
            'rgt' => 6,
        ),
        5 => array(
            'userid' => 5,
            'parentid' =>0,
            'data' => 'data5',
            'lft' => 7,
            'rgt' => 8,
        ),
    );

    public static $resultDeleteMainNode = array(
        2 => array(
            'userid' => 2,
            'parentid' => 0,
            'data' => 'data2',
            'lft' => 1,
            'rgt' => 4,
        ),
        3 => array(
            'userid' => 3,
            'parentid' =>2,
            'data' => 'data3',
            'lft' => 2,
            'rgt' => 3,
        ),
        4 => array(
            'userid' => 4,
            'parentid' =>0,
            'data' => 'data4',
            'lft' => 5,
            'rgt' => 6,
        ),
        5 => array(
            'userid' => 5,
            'parentid' =>0,
            'data' => 'data5',
            'lft' => 7,
            'rgt' => 8,
        ),
    );

    public static $resultDeleteChildNode = array(
        1 => array(
            'userid' => 1,
            'parentid' => 0,
            'data' => null,
            'lft' => 1,
            'rgt' => 4,
        ),
        3 => array(
            'userid' => 3,
            'parentid' =>1,
            'data' => 'data3',
            'lft' => 2,
            'rgt' => 3,
        ),
        4 => array(
            'userid' => 4,
            'parentid' =>0,
            'data' => 'data4',
            'lft' => 5,
            'rgt' => 6,
        ),
        5 => array(
            'userid' => 5,
            'parentid' =>0,
            'data' => 'data5',
            'lft' => 7,
            'rgt' => 8,
        ),
    );
    
}
