<?php
session_start();
require_once "check_session.php";
CheckSession::whenLogIn();

ob_start();
require_once 'result_box.php';
$result=new resultCon(false);
$result->addIfisStr(delOldMail());
if(count($_POST)>0){
	if (!checkCAPTCHA())
		echo "คำตอบ (Answer) ไม่ถูกต้อง กรุณาตอบใหม่อีกครั้ง";
	elseif(!(strlen($_POST['email'])>0 && strlen($_POST['password'])>0))
		echo "กรุณากรอก email และ password";
	elseif(filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)===false)
		echo "รูปแบบ email ไม่ถูกต้อง";
	else try{
		$db=newPDO();
		$db->beginTransaction();
		
		$sql=isset($_REQUEST['confirm']) ? 'SELECT email,password,type FROM new_account WHERE email=:email AND password=:pw AND confirm_code=:cc':(CheckSession::SQL).' WHERE team_info.email=:email AND team_info.password=:pw;';
		$stm=$db->prepare($sql);
		$stm->bindParam(':email',$_POST['email']);
		$stm->bindParam(':pw',$_POST['password']);
		if(isset($_REQUEST['confirm']))
			$stm->bindParam(':cc',$_REQUEST['confirm']);
		$stm->execute();
		
		if($stm->rowCount()>0){
			$sess=new CheckSession();
			if(isset($_REQUEST['confirm'])){
				$row=$stm->fetch(PDO::FETCH_ASSOC);
				$sql='INSERT INTO team_info (email,password,type) VALUES (:email,:pw,:type)';
				$stm=$db->prepare($sql);
				$stm->bindParam(":email",$row['email']);
				$stm->bindParam(":pw",$row['password']);
				$stm->bindParam(":type",$row['type']);
				$stm->execute();
				
				$sess->newUser($db->lastInsertId(),$row['type']);
				
				$sql='DELETE FROM new_account WHERE email=:email AND password=:pw AND type=:type AND confirm_code=:cc';
				$stm=$db->prepare($sql);
				$stm->bindParam(":email",$row['email']);
				$stm->bindParam(":pw",$row['password']);
				$stm->bindParam(":type",$row['type']);
				$stm->bindParam(':cc',$_REQUEST['confirm']);
				$stm->execute();
			}else{
				$sess->fromDB($stm);
			}
			$_SESSION=$sess->toSession($_SESSION);
			$result->result=true;
			unset($row);
		}else{
			echo "ไม่สามารถ log in ได้ เนื่องจาก <ol><li>Email หรือ Password ไม่ถูกต้อง</li><li>ท่านยังไม่ได้ยืนยัน email สำหรับ account นี้</li><li>Confirm code ไม่ถูกต้อง</li><li>ท่านยืนยัน email นี้ ช้ากว่า 48 ชั่วโมง</li></ol>";
			$result->result=false;
			unset($_SESSION);
		}
		$db->commit();
		unset($db,$stm,$sql);
	} catch(Exception $e){
		$db->rollBack();
		$result->result=false;
		unset($_SESSION);
		echo "\nไม่สามารถ log in ได้ เนื่องจากข้อผิดพลาดของระบบ\n$e\n\n{$e->getMessage()}\nSQL = $sql";
	}
}
$result->message.=nl2br(ob_get_contents());
ob_end_clean();

if(isset($_GET['ajax'])){
	require_once 'json_ajax.php';
	header('Content-type: application/json');
	$res=new jsonAjax();
	$res->setResult($result);
	if($result->result===true){
		$res->addAction(jsonAjax::REDIRECT,"./");
	}else{
		$res->addAction(jsonAjax::RELOAD_CAPTCHA);
		$res->addHtmlTextVal(jsonAjax::SET_VAL,"#captcha");
	}
	echo $res;
}elseif($result->result===true){
	header("Location: ./");
}else{
?>
<!doctype html>
<html><!-- InstanceBegin template="/Templates/mahidol.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta charset="utf-8">
<!-- InstanceBeginEditable name="doctitle" -->
<title>เข้าสู่ระบบ: การแข่งขันตอบปัญหาวิทยาศาสตร์การแพทย์</title>
<!-- InstanceEndEditable -->
<script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
<script src="js/jquery-ui-1.10.4.custom.min.js"></script>
<script src="js/mahidol_ajax.js"></script>
<link rel="stylesheet" href="../mahidol.css" />
<!-- InstanceBeginEditable name="head" -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.47/jquery.form-validator.min.js"></script>
<script type="text/javascript" src="js/login.js"></script>
<!-- InstanceEndEditable -->
</head>

<body>
<div class="header">&nbsp;</div>
<div class="tab_menu">&nbsp;<a href="login.php" title="log in">log in</a> <a href="register.php" title="สมัครเข้าแข่งขัน">register</a></div>
<div class="content">
<div class="headline"><!-- InstanceBeginEditable name="headline" -->เข้าสู่ระบบ<!-- InstanceEndEditable --></div>
<div class="main_content"><!-- InstanceBeginEditable name="main_content" -->main_content
 <form action="login.php" method="post" name="login" class="left" id="login">
  <fieldset>
  <legend>Log in</legend>
  <p><label for="email">Email</label><input name="email" type="email" required id="email" value="<?=@$_POST['email'];?>" size="35" data-validation="email"></p>
  <p><label for="password">Password</label><input name="password" type="password" id="password" size="35" data-validation="length alphanumeric" data-validation-allowing="_:;"  data-validation-length="6-32" required></p>
  <? require("captcha.php");?>
  <p><? if(isset($_REQUEST['confirm'])): ?><input name="confirm" type="hidden" id="confirm" value="<?=$_REQUEST['confirm']?>"><? endif ?>
    <input type="submit" name="submit" id="submit" value="Log in">
    <input type="reset" name="Reset" id="button" value="Cancel">
    <a href="forget.php" title="ลืมรหัสผ่าน">forget password?</a></p>
</fieldset>
  </form>
<!-- InstanceEndEditable --></div>
</div>
<div class="footer">&nbsp;</div>
</body>
<!-- InstanceEnd --></html>
<? } ?>