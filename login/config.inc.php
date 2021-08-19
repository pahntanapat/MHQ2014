<?php
//Root of This site
$_ROOT=$_SERVER['DOCUMENT_ROOT'];
// Schedule last day of ...
$_END_REGISTER=strtotime("2015-04-15 23:59:59",time());
$_END_EDIT_INFO=strtotime("2015-04-17 23:59:59",time());
$_START_PAY=strtotime("2014-04-15 00:00:00",time());
$_END_PAY=strtotime("+1 week -1 second",$_START_PAY);
$_START_PRINT=strtotime("2014-05-14 00:00:00",time());
// PDO config at main_function.php
function newPDO(){
	$dbh=new PDO(
		"mysql:host=localhost;dbname=mahidol_quiz;", // DSN
		"root", //Username
		"053721872" //Password
	);
	$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	return $dbh;
}
// Mail config at mail.php
const
	SMTP_HOST= '127.0.0.1',
	SMTP_PORT= 25,
	SMTP_USER= "info@test.net",
	SMTP_PASS= "053721872",
	
	MAIL_FROM="no-reply@test.net",
	MAIL_REPLY_TO="info@test.net";

//Normal Error Message
function errMsg($e,$sql=NULL,$report="กรุณาแจ้งกรรมการการแข่งขันด่วน"){
	if($sql)
		return "Error! ระบบเกิดความผิดพลาดเนื่องจาก\n$e\nSQL = $sql\n$report";
	else
		return "Error! ระบบเกิดความผิดพลาดเนื่องจาก\n$e\n$report";
}

?>