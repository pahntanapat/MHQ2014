<?php
ob_start();
session_start();
require_once "main_function.php";
require_once 'result_box.php';

$result=new resultCon(false);
$result->addIfisStr(delOldMail());
if(isset($_POST)){
	if(count($_POST)<=0) $result->result=NULL;
	elseif (!checkCAPTCHA())
		echo "คำตอบ (Answer) ไม่ถูกต้อง กรุณาตอบใหม่อีกครั้ง";
	elseif(strlen($_POST['email'])<=0)
		echo "กรุณากรอก email";
	elseif(filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)===false)
		echo "รูปแบบ email ไม่ถูกต้อง";
	elseif(!isset($_POST['type']))
		echo "กรุณาเลือกประเภททีม";
	else try{
		$db=newPDO();
		$stm=$db->prepare('SELECT *,NOW() FROM '.(isset($_POST['isConfirm'])? 'team_info':'new_account').' WHERE email=:email AND type=:type;');
		$stm->bindParam(':email',$_POST['email']);
		$stm->bindParam(":type",$_POST['type'],PDO::PARAM_BOOL);
		$stm->execute();
		
		if($stm->rowCount()>0){
			$res=$stm->fetch(PDO::FETCH_ASSOC);
			$msg=<<<MAIL
ท่านได้ส่งคำร้องขอรหัสผ่านจากระบบ log in ของการแข่งขันตอบปัญหามหิดลเมื่อเวลา <b>{$res['NOW()']}</b><br>
<p><b>รายละเอียด</b></p>
<b>Email: </b>{$res['email']}<br>
<b>Password: </b>{$res['password']}<br>
<b>ประเภททีม: </b> ทีม
MAIL;
			$msg.=($res['type']==0)? 'โรงเรียน':'อิสระ';
			if(isset($res['confirm_code'])){
				$code= 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/login.php?confirm='.$res['confirm_code'];
				$msg.=<<<MAIL
<br><br><b>ท่านต้องยืนยัน email โดยคลิก link นี้แล้ว log in ทันที: <a href="$code" alt="ยืนยัน email" target="_blank">$code</a></b>
MAIL;
			}
			$msg.=<<<MAIL
<br><br>
คำแนะนำเพื่อความปลอดภัย: กรุณาเก็บ password เป็นความลับและควรเปลี่ยน password หลังจาก log in แล้ว<br>
MAIL;
			require 'mail.php';
			$warn=forceSendMail($_POST['email'],'ลืมรหัสผ่าน',$msg);
			if($warn===true){
				$result->result=true;
				echo "ส่งระบบได้ email ให้ท่านแล้ว";
			}else{
				$result->result=false;
				echo "Error: ไม่สามารถส่ง email ได้ กรุณาติดต่อทีมงานการแข่งขันฯ\n$warn";
			}
			unset($msg,$warn);
		}else
			echo "ไม่พบ email ที่ระบุนี้";
		unset($res,$db,$stm);
	}catch(Exception $e){
		echo "\nError: $e\n{$e->getMessage()}\n";
		$result->result=false;
	}
}
$result->message.=nl2br(ob_get_contents());
ob_end_clean();
if(isset($_GET['ajax'])):
	require_once 'json_ajax.php';
	header('Content-type: application/json');
	$res=new jsonAjax();
	$res->setResult($result);
	if($result->result===false){
		$res->addAction(jsonAjax::RELOAD_CAPTCHA);
		$res->addHtmlTextVal(jsonAjax::SET_VAL,"#captcha");
	}
	echo $res;
else:
?>
<!doctype html>
<html><!-- InstanceBegin template="/Templates/mahidol.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta charset="utf-8">
<!-- InstanceBeginEditable name="doctitle" -->
<title>ลืมรหัสผ่าน: การแข่งขันตอบปัญหาวิทยาศาสตร์การแพทย์</title>
<!-- InstanceEndEditable -->
<script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
<script src="js/jquery-ui-1.10.4.custom.min.js"></script>
<script src="js/mahidol_ajax.js"></script>
<link rel="stylesheet" href="../mahidol.css" />
<!-- InstanceBeginEditable name="head" -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.47/jquery.form-validator.min.js"></script>
<script src="js/forget.js"></script>
<!-- InstanceEndEditable -->
</head>

<body>
<div class="header">&nbsp;</div>
<div class="tab_menu">&nbsp;<a href="login.php" title="log in">log in</a> <a href="register.php" title="สมัครเข้าแข่งขัน">register</a></div>
<div class="content">
<div class="headline"><!-- InstanceBeginEditable name="headline" -->ลืมรหัสผ่าน<!-- InstanceEndEditable --></div>
<div class="main_content"><!-- InstanceBeginEditable name="main_content" --><? if(!$result->result): ?><form action="forget.php" method="post" name="forget" id="forget"><fieldset><legend>ลืมรหัสผ่าน</legend><p><label for="email">Email</label><input name="email" type="email" required id="email" value="<?=@$_POST['email'];?>" size="35" data-validation="email"></p><div><label for="type">ประเภททีม</label><p class="radio"><input name="type" type="radio" id="type_0" value="0" required>
            ทีมโรงเรียน <br>
          <input type="radio" name="type" value="1" id="type_1" required>
            ทีมอิสระ<br><input name="isConfirm" type="checkbox" id="isConfirm" value="1"> ได้ยืนยัน email แล้ว
</p></div>
<? require('captcha.php');?><p><input type="submit" name="submit" id="submit" value="ส่งข้อมูล"> <input type="reset" name="reset" id="reset" value="ล้างข้อมูล"></p></fieldset></form><? endif;
echo $result->getIfNotNull();?>
<!-- InstanceEndEditable --></div>
</div>
<div class="footer">&nbsp;</div>
</body>
<!-- InstanceEnd --></html>
<? endif; ?>
