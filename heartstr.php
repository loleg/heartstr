<?php
/*

Installation:
-------------

1. Run this in MySQL to prepare the table:

CREATE TABLE  `favster` (
`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`ip` INT UNSIGNED NOT NULL ,
`url` VARCHAR( 255 ) NOT NULL ,
INDEX (  `url` )
) ENGINE = INNODB COMMENT =  'Stores page favorites';

2. Set up the HOST, USERNAME, PASSWORD below

3. Put the JavaScript in your theme and update the URL if not /favster.php

*/

$MYSQL_HOST = 'localhost';
$MYSQL_USER = 'utou';
$MYSQL_PW   = 'utou';
$MYSQL_DB   = 'utou';
$MYSQL_TBL  = 'favster';

if (!isset($_GET['url'])) {
  die ('Error: no URL provided');
}
$url = urldecode($_GET['url']);
$ip = getenv('REMOTE_ADDR');

// Connect to DB
$link = mysql_connect($MYSQL_HOST, $MYSQL_USER, $MYSQL_PW);
if (!$link) {
    die('Error: could not connect: ' . mysql_error());
}
mysql_select_db($MYSQL_DB) or die('Error: could not select database');

// Check this ip/address combination faves
$query = 'SELECT * FROM ' . $MYSQL_TBL . ' WHERE `ip` = INET_ATON(\'' . $ip . '\')'
       . ' AND `url` = \''. mysql_real_escape_string($url) . '\'';
$result = mysql_query($query) or die('Error: select failed: ' . mysql_error());

if (mysql_num_rows($result) == 0) {

  // Push the fave
  $query = 'INSERT INTO ' . $MYSQL_TBL . ' (`date`,`ip`,`url`) VALUES ('
         . 'CURRENT_TIMESTAMP, INET_ATON(\'' . $ip . '\'),\'' 
         . mysql_real_escape_string($url) . '\');';
  $result = mysql_query($query) or die('Error: update failed: ' . mysql_error());

}

// Count up the faves
$query = 'SELECT COUNT(`ip`) FROM ' . $MYSQL_TBL
       . ' WHERE `url` = \''. mysql_real_escape_string($url) . '\'';
$result = mysql_query($query) or die('Error: select failed: ' . mysql_error());

// Printing count of faves
echo mysql_result($result, 0);

// Free resultset
mysql_free_result($result);

mysql_close($link);

?>
