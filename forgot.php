<?php

$email = null;
$password = null;

$email = $_POST['email'];
$password = $_POST['password'];

if($email == null || $password == null) {
	die(json_encode(array("error" => "You have an empty field.")));
}

$password = (string)$password;
$email = (string)$email;

if(!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 80) {
	die(json_encode(array("error" => "Please enter valid email.")));
}

if(strlen($password) < 4 || strlen($password) > 20) {
	die(json_encode(array("error" => "Password must be at least length 4 at most 20.")));
}


require_once("db.php");

$prepared=$pdo->prepare("SELECT id,username FROM users WHERE email=:em");
$prepared->bindValue(":em",$email);
$prepared->execute();

if($prepared->rowCount()===0) {
	die(json_encode(array("error" => "Email does not exists")));
}
$ff = $prepared->fetch(PDO::FETCH_ASSOC);
$user_id = (int)($ff['id']);
$user_name = (string)($ff['username']);
function generate_verification_code() {
	$pool = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$verif = '';
	$plen = strlen($pool);
	for($i=0;$i<96;$i++) {
		$verif .= $pool[rand(0,$plen-1)];
	}
	return $verif;
}

$c = 0;
$verif = null;
do {
	$verif = generate_verification_code();
	$c = $pdo->query("SELECT COUNT(*) as C FROM forgot_pw WHERE verification_code='".$verif."'")->fetch(PDO::FETCH_ASSOC)['C'];
} while($c != 0);

$prepared = $pdo->prepare("INSERT INTO forgot_pw (user_id,verification_code,new_pw) VALUES (:uid,:vc,:np) ON DUPLICATE KEY UPDATE verification_code=:kk, new_pw=:pp");
$prepared->bindValue(":uid",$user_id);
$prepared->bindValue(":vc",$verif);
$prepared->bindValue(":np",$password);
$prepared->bindValue(":kk",$verif);
$prepared->bindValue(":pp",$password);
$prepared->execute();



require "mailer/PHPMailerAutoload.php";

$mail = new PHPMailer(); // create a new object
$mail->IsSMTP(); // enable SMTP
$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
$mail->SMTPDebug = 0;
$mail->Host = "smtp.gmail.com";
$mail->Port = 587; // or 587
$mail->IsHTML(true);
$mail->Username = "<censor>";
$mail->Password = "<censor>";
$mail->SetFrom("basbursen@gmail.com");
$mail->Subject = "doldur.xyz Password Reset";
$mail->Body = 'Hello ' . $user_name . ",<br/><br/>You can confirm your password reset request by clicking this link: <a href='http://doldur.xyz/verify.php?key=".$verif."'>http://doldur.xyz/verify.php?key=".$verif."<a><br/><br/>";
$mail->AddAddress($email);

 if(!$mail->Send()) {
    die(json_encode(array("error" => "mail send error")));
 } else {
    die(json_encode(array("success" => "mail_sent")));
 }


?>
