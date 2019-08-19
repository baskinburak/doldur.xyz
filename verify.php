<?php
if(!isset($_GET['key'])) {
	die("verification code is not set.");
}
$key = (string)$_GET['key'];
if(strlen($key)!=96) {
	die("verification code is not valid.");
}

require_once("db.php");
$prepared = $pdo->prepare("select * from forgot_pw where verification_code = :code");
$prepared->bindValue(":code",$key);
$prepared->execute();
if($prepared->rowCount()==0) {
	die("verification code is not valid.");
}

$fetched = $prepared->fetch(PDO::FETCH_ASSOC);
$user_id = (int)($fetched['user_id']);
$new_pw = md5((string)($fetched['new_pw']));


$prepared = $pdo->prepare("UPDATE users SET password=:pw WHERE id=:ii");
$prepared->bindValue(":pw",$new_pw);
$prepared->bindValue(":ii",$user_id);
$prepared->execute();

$prepared = $pdo->prepare("DELETE FROM forgot_pw WHERE user_id = :ii");
$prepared->bindValue(":ii",$user_id);
$prepared->execute();

die("Password updated successfully.<br/><a href='http://doldur.xyz'>Return to site.</a>");
?>
