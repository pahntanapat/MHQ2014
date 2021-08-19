<?php
require_once 'config.inc.php';
function delOldMail(PDO $dbh=NULL){
	try{
		if($dbh==NULL) $dbh=newPDO();
		$dbh->beginTransaction();
		
		$sql='DELETE FROM new_account WHERE create_time < (NOW() - INTERVAL 2 DAY)';
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		
		$row=$stmt->rowCount();
		$dbh->commit();
		unset($sql,$dbh,$stmt);
		return $row;
	} catch(Exception $e){
		$dbh->rollBack();
		return errMsg($e,$sql,'');
	}
}

function checkCAPTCHA(){
	if(!isset($_POST['captcha'])) return false;
	require_once("securimage/securimage.php");
	$cp=new Securimage();
	return ($cp->check($_POST['captcha']));
}
?>