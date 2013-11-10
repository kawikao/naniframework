Introduction
------------------
Create, Update and Delete nested set trees.

Source Data Format
------------------
* XML or Array data.
* Structure may be nested or flat.
* Each item may contain a parentid or not.

Options
------------------
Required:

* rowtag - wraps each item
* rowidtag - primary key for each item

Optional:

* startleft - (default:1) Begins tree (lft value) with this integer.
* parentidtag - (default:null) If missing, output will be flat. If present, output will be nested.
* formatOutput - (default:true) If true, getOutput() returns one tag per line. false, getOutput() returns data all on one line.
* encoding - (default:utf-8) XML encoding.

Adapters (returned data format)
------------------

Current Adapters: Xml and Array.

Proposed Adapters: Database and File.

Details
------------------
Call Adapter in the format you want the return output.

To use:

* Include NaniFramework/library into your PHP include path.
* Use require_once to load adapter.
* Instantiate object passing it the data to create the nested set for and required options.
* Use getRows() to retrieve tree array.
* Use getOutput() for XML adapter to retrieve XML string.

Example : Array
------------------
    <?php

    set_include_path("/path/to/NaniFramework/library:".get_include_path());
    require_once 'Nani/Tree/Adapter/Array.php';
    $options = array('rowtag' => 'row',
                     'rowidtag' => 'userid',
                     'parentidtag' => 'parentid',
    );
    $data = array(
        'row' => array(
            array('userid' => 1,
                'parentid' => 0,
                'username'  => 'USERNAME1',
                'password' => 'PASSWORD',
                'firstname' => 'FIRSTNAME',
                'lastname' => '',
                'row'  => array(
                    array('userid' => 2,
                        'parentid' => 1,
                        'username'  => 'USERNAME2',
                        'password' => 'PASSWORD',
                        'firstname' => 'FIRSTNAME',
                        'lastname' => 'LASTNAME',
                    ),
               ),
           ),
        )
    );

    $adapter = new Nani_Tree_Adapter_Array($data, $options);
    $result = $adapter->getRows();
    print_r($result);

Output:

    Array
    (
        [0] => Array
            (
                [userid] => 1
                [parentid] => 0
                [username] => USERNAME1
                [password] => PASSWORD
                [firstname] => FIRSTNAME
                [lastname] => 
                [lft] => 1
                [rgt] => 4
            )
        [1] => Array
            (
                [userid] => 2
                [parentid] => 1
                [username] => USERNAME2
                [password] => PASSWORD
                [firstname] => FIRSTNAME
                [lastname] => LASTNAME
                [lft] => 2
                [rgt] => 3
            )
    )

Example : XML
------------------
    <?php

    set_include_path("/path/to/NaniFramework/library:".get_include_path());
    require_once 'Nani/Tree/Adapter/Xml.php';
    $options = array('rowtag' => 'row',
                     'rowidtag' => 'userid',
                     'parentidtag' => 'parentid',
    );

    $data = '<rows>
        <row>
            <userid>1</userid>
            <parentid>0</parentid>
            <username>USERNAME1</username>
            <password>PASSWORD</password>
            <firstname>FIRSTNAME</firstname>
            <lastname/>
        </row>
        <row>
            <userid>2</userid>
            <parentid>1</parentid>
            <username>USERNAME2</username>
            <password>PASSWORD</password>
            <firstname>FIRSTNAME</firstname>
            <lastname>LASTNAME</lastname>
        </row>
    </rows>';

    $adapter = new Nani_Tree_Adapter_Xml($data, $options);
    echo $adapter->getOutput();


Output:

    <?xml version="1.0" encoding="utf-8"?>
    <rows>
      <row>
        <userid>1</userid>
        <parentid>0</parentid>
        <username>USERNAME1</username>
        <password>PASSWORD</password>
        <firstname>FIRSTNAME</firstname>
        <lastname></lastname>
        <lft>1</lft>
        <rgt>4</rgt>
        <row>
          <userid>2</userid>
          <parentid>1</parentid>
          <username>USERNAME2</username>
          <password>PASSWORD</password>
          <firstname>FIRSTNAME</firstname>
          <lastname>LASTNAME</lastname>
          <lft>2</lft>
          <rgt>3</rgt>
        </row>
      </row>
    </rows>

