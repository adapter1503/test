<?php
require_once('class/MyLogger.php');
require_once('class/PDOAdapter.php');

$dsn = 'mysql:host=127.0.0.1;dbname=test';
$username = 'root';
$password = '';

$log = new MyLogger(log_file);
$connection = new PDOAdapter($dsn, $username, $password, $log);

$req_1 = file_get_contents('SQLrequests/sql1.sql');
$req_2 = file_get_contents('SQLrequests/sql2.sql');
$req_3 = file_get_contents('SQLrequests/sql3.sql');
$req_4 = file_get_contents('SQLrequests/sql4.sql');

$maxAge = $connection->execute('selectOne', $req_1);
$ageLessThanMax = $connection->execute('selectOne', $req_2);
//$update = $connection->execute('selectOne', $req_3);
$maxAgePersons = $connection->execute('selectAll', $req_4);

?>