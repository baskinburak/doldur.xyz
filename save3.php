<?php
require_once("db.php");
$email = null;
$username = null;
$password = null;
$info = null;
$owndata = null;

$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];
$info = $_POST['schedule'];
$owndata = $_POST['own'];


if($email == null || $username == null || $password == null) {
	die(json_encode(array("error" => "Format error")));
}

$password = (string)$password;
$username = (string)$username;
$email = (string)$email;

if(!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 80) {
	die(json_encode(array("error" => "Please enter valid email.")));
}

if(strlen($password) < 4 || strlen($password) > 20) {
	die(json_encode(array("error" => "Password must be at least length 4 at most 20.")));
}

if(strlen($username)<1 || strlen($username) > 20) {
	die(json_encode(array("error" => "Username must be at least length 1 at most 20.")));
}

if(!ctype_lower($username)) {
	die(json_encode(array("error" => "Username must contain lower-case English characters only.")));
}

if(!is_array($info)) {
	die(json_encode(array("error" => "Schedule format error.")));
}

$len = count($info);
$course_sec = array();
for($i=0; $i<$len; $i++) {
	if(!isset($info[$i])) {
		die(json_encode(array("error" => "Schedule format error.")));
	}
	$lll = $info[$i];
	if(!isset($lll['cc']) || !isset($lll['sn'])) {
		die(json_encode(array("error" => "Schedule format error.")));
	}
	if(!is_numeric($lll['cc']) || !is_numeric($lll['sn'])) {
		die(json_encode(array("error" => "Schedule format error.")));
	}
}
if($owndata != null) {
	$days = array("mon", "tue", "wed", "thu", "fri");
	$ownidxs = array();
	foreach($days as $day) {
		for($i=1; $i<=9; $i++) {
			$ownidxs[] = $day."-".$i;
		}
	}
	foreach($owndata as $key=>$value) {
		if(array_search($key,$ownidxs) === false) {
			die(json_encode(array("error" => json_encode($ownidxs))));
		}
		$ownidxs=array_diff($ownidxs, [$key]);
		foreach($value as $str) {
			if(!is_string($str)) {
				die(json_encode(array("error" => "Owndata format error.")));
			}
			if(strlen($str) == 0) {
				die(json_encode(array("error" => "Owndata format error.")));
			}
		}
	}
}

$password = md5($password);

$pdo->query("
CREATE TABLE IF NOT EXISTS users(
id INT UNIQUE PRIMARY KEY NOT NULL AUTO_INCREMENT,
username VARCHAR(25) UNIQUE NOT NULL,
password VARCHAR(40) NOT NULL,
email VARCHAR(100) UNIQUE NOT NULL,
data VARCHAR(15000) NOT NULL,
owndata VARCHAR(5000)
) CHARACTER SET utf8
");

$prepared = $pdo->prepare("SELECT count(*) as c FROM users WHERE username=:un AND email=:em AND password=:pa");
$prepared->bindValue(':un', $username);
$prepared->bindValue(':em', $email);
$prepared->bindValue(':pa', $password);
$prepared->execute();

$c = $prepared->fetch(PDO::FETCH_ASSOC)['c'];
if($c>0) {
	$prepared = $pdo->prepare("UPDATE users SET data=:d, owndata=:o WHERE username=:un AND email=:em AND password=:pa");
	$prepared->bindValue(':d', serialize($info));
	$prepared->bindValue(':o', serialize($owndata));
	$prepared->bindValue(':un', $username);
	$prepared->bindValue(':em', $email);
	$prepared->bindValue(':pa', $password);
	$prepared->execute();
	$uri = str_replace('save3.php', '', $_SERVER[REQUEST_URI]);
	die(json_encode(array("success" => "http://$_SERVER[HTTP_HOST]$uri$username")));
} else {
	$prepared = $pdo->prepare("SELECT count(*) as c FROM users WHERE username=:un AND email=:em");
	$prepared->bindValue(':un', $username);
	$prepared->bindValue(':em', $email);
	$prepared->execute();
	$c = $prepared->fetch(PDO::FETCH_ASSOC)['c'];
	if($c != 0) {
		die(json_encode(array("error" => "Wrong password.")));
	}

	$prepared = $pdo->prepare("SELECT count(*) as c FROM users WHERE username=:un");
	$prepared->bindValue(':un', $username);
	$prepared->execute();
	$c = $prepared->fetch(PDO::FETCH_ASSOC)['c'];
	if($c != 0) {
		die(json_encode(array("error" => "Username already in use.")));
	}

	$prepared = $pdo->prepare("SELECT count(*) as c FROM users WHERE email=:em");
	$prepared->bindValue(':em', $email);
	$prepared->execute();
	$c = $prepared->fetch(PDO::FETCH_ASSOC)['c'];
	if($c != 0) {
		die(json_encode(array("error" => "Email already in use.")));
	}
//	die(json_encode(array("error"=>serialize($owndata))));
	$prepared = $pdo->prepare("INSERT INTO users (username, email, password, data, owndata) VALUES(:un, :em, :pa, :d, :o)");
	$prepared->bindValue(':un', $username);
	$prepared->bindValue(':em', $email);
	$prepared->bindValue(':pa', $password);
	$prepared->bindValue(':d', serialize($info));
	$prepared->bindValue(":o", serialize($owndata));
	$prepared->execute();

	$uri = str_replace('save3.php', '', $_SERVER[REQUEST_URI]);
	die(json_encode(array("success" => "http://$_SERVER[HTTP_HOST]$uri$username")));
}
?>
