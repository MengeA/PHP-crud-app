<?php


if(!isset($_GET['term']) die ("Missing required parameter");


session_start();

if(!isset($session['user_id'])){
	die("Access denied");
}

require_once 'pdo.php';

header("Content-type: applicatin/json; charset=utf-8");

$term = $_GET['term'];
error_log("Looking up typehead term=".$term);


$stmt = $pdo->prepare('SELECT name FROM institution WHERE name LIKE :prefix');
$stmt->execute(array( ':prefix'=>$term."%"));
$retval = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	$retval[] = $row['name'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));