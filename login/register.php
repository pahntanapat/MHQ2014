<?php
session_start();
require_once 'config.inc.php';
require_once "check_session.php";
CheckSession::whenLogIn();

ob_start();
require_once 'result_box.php';

$result=new resultCon(false);
$result->addIfisStr(delOldMail());
if(count($_POST)>0){
	if(!checkCAPTCHA())
		echo "คำตอบ (Answer) ไม่ถูกต้อง กรุณาตอบใหม่อีกครั้ง";
	elseif(time()>$_END_REGISTER)
		echo "หมดเขตรับสมัคร";
	elseif(!isset($_POST['OK']))
		echo "กรุณาอ่านทำความเข้าใจกติการการแข่งขัน";
	elseif(!isset($_POST['type']))
		echo "กรุณาเลือกประเภททีม";
	elseif(!(strlen($_POST['email'])>0 & strlen($_POST['password'])>0 && $_POST["password_confirmation"]))
		echo "กรุณากรอก email และ password";
	elseif(filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)===false)
		echo "รูปแบบ email ไม่ถูกต้อง";
	elseif(!preg_match_all('/^[[:alnum:]_:;]{6,32}$/',$_POST['password_confirmation']))
		echo "Password ต้องประกอบไปด้วย A-Z, a-z, 0-9, semicolon (;), colon (:) หรือ underscore (_) ความยาวรวม 6 - 32 ตัวอักขระเท่านั้น";
	elseif($_POST['password']!=$_POST["password_confirmation"])
		echo "Confirm password ไม่ตรงกับ password";
	else
		try{
			$db=newPDO();
			$db->beginTransaction();
			$stm=$db->prepare('SELECT (SELECT COUNT(*) FROM new_account WHERE email =:email)+(SELECT COUNT(*) FROM team_info WHERE email =:email);');
			$stm->execute(array(':email'=>$_POST['email']));
			
			if($stm->fetchColumn()>0)
				echo "Email นี้ได้ถูกลงทะเบียนแล้ว";
			else{
				$stm=$db->prepare('INSERT INTO new_account (email,password,type,confirm_code) VALUES (:email,:password,:type,:code)');
				$stm->bindParam(':email',$_POST['email']);
				$stm->bindParam(':password',$_POST['password']);
				$stm->bindParam(':type',$_POST['type'],PDO::PARAM_BOOL);
				$code=md5($_POST['email'].':'.$_POST['password'].'@'.$_POST['type'].'<'.$_POST['captcha'].'>;');
				$stm->bindParam(':code',$code);
				$stm->execute();
				
				require_once('mail.php');
				$code= 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/login.php?confirm='.$code;
				$message=<<<TXT
กรุณาคลิก link นี้และ log in ทันทีเพื่อยืนยัน email: $_POST[email] ของท่านหลังจากสมัครสมาชิกภายใน 48 ชั่วโมง<br/><br/>
<b><a href="$code" alt="ยืนยัน email" target="_blank">$code</a></b><br/><br/>
ถ้าท่านไม่สามารถคลิกที่ link ได้ ให้คัดลอก URL นี้ไปวางในโปรแกรม web browser<br/>
<b>$code</b><br/>
หากท่านยืนยัน email ช้ากว่า 48 ชั่วโมง ระบบจะลบ email ของท่านออกโดยอัตโนมัติ ท่านต้องสมัครสมาชิกใหม่โดยใช้ email เดิมหรือ email ใหม่ก็ได้
TXT;
				$warn=forceSendMail($_POST['email'],'ยืนยัน email',$message);
				if($warn!==true){
					$db->rollBack();
					$db->beginTransaction();
					echo "ไม่สามารถส่ง email ได้ไปยัง $_POST[email] ได้ เนื่องจาก ";
					echo $warn;
				}else{
					$result->result=true;
					echo "ลงทะเบียนเรียบร้อยแล้ว ระบบจะส่ง link ยืนยัน email ของท่าน\nกรุณา check email ของท่านภายใน 48 ชั่วโมง";
				}
				unset($code,$head,$message,$warn);
			}
			$db->commit();
			unset($db,$stm);
		}catch(Exception $e){
			$db->rollBack();
			echo "\nError! ไม่สามารถบันทึกข้อมูลได้เนื่องจาก\n$e";
			$result->result=false;
		}
}
$result->message.=nl2br(ob_get_contents());
ob_end_clean();

if(isset($_GET['ajax'])){
	require_once 'json_ajax.php';
	header('Content-type: application/json');
	$res=new jsonAjax();
	$res->setResult($result);
	if($result->result!==true){
		$res->addAction(jsonAjax::RELOAD_CAPTCHA);
		$res->addHtmlTextVal(jsonAjax::SET_VAL,"#captcha");
	}
	echo $res;
}else{
?>
<!doctype html>
<html><!-- InstanceBegin template="/Templates/mahidol.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta charset="utf-8">
<!-- InstanceBeginEditable name="doctitle" -->
<title>สมัครเข้าร่วมการแข่งขัน: การแข่งขันตอบปัญหาวิทยาศาสตร์การแพทย์</title>
<!-- InstanceEndEditable -->
<script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
<script src="js/jquery-ui-1.10.4.custom.min.js"></script>
<script src="js/mahidol_ajax.js"></script>
<link rel="stylesheet" href="../mahidol.css" />
<!-- InstanceBeginEditable name="head" -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.47/jquery.form-validator.min.js"></script>
<script src="js/register.js" type="application/javascript"></script>
<!-- InstanceEndEditable -->
</head>

<body>
<div class="header">&nbsp;</div>
<div class="tab_menu">&nbsp;<a href="login.php" title="log in">log in</a> <a href="register.php" title="สมัครเข้าแข่งขัน">register</a></div>
<div class="content">
<div class="headline"><!-- InstanceBeginEditable name="headline" -->สมัครเข้าร่วมการแข่งขัน<!-- InstanceEndEditable --></div>
<div class="main_content"><!-- InstanceBeginEditable name="main_content" --><? if(!$result->result){ ?>
  <form action="register.php" method="post" name="reg" id="reg"><fieldset><legend>สมัครเข้าร่วมแข่งขัน</legend>
<p><label for="email">Email</label><input name="email" type="email" required id="email" value="<?=@$_POST['email']?>" data-validation="email" /></p>
<p><label for="password">Password</label><input name="password_confirmation" type="password" id="password_confirmation" data-validation="length alphanumeric" data-validation-allowing="_:;"  data-validation-length="6-32" required></p>
<p><label for="ConfirmPW">Confirm password</label><input name="password" type="password" =id="password" data-validation="confirmation" required></p>
<div><label for="type">ประเภททีม</label><p class="radio"><input name="type" type="radio" id="type_0" value="0" required>
            ทีมโรงเรียน
          <br/>
          <input type="radio" name="type" value="1" id="type_1" required>
            ทีมอิสระ</p></div>
<? require('captcha.php');?>
<p>
    <input name="OK" type="checkbox" required id="OK" value="1">
    <b> ฉันเข้าใจแล้วยอมรับกติการการแข่งขันตอบปัญหาวิทยาศาสตร์การแพทย์</b> </p>
<p><input type="submit" name="submit" id="submit" value="ส่งข้อมูล"> <input type="reset" name="reset" id="reset" value="ล้างข้อมูล"></p></fieldset>
  </form><? } ?>
 <?=$result->getIfNotNull()?>
<!-- InstanceEndEditable --></div>
</div>
<div class="footer">&nbsp;</div>
</body>
<!-- InstanceEnd --></html><?php } ?>
