<?php
session_start();
require_once 'config.inc.php';
require_once "check_session.php";
$sess=checkSession::mustLogIn();

ob_start();
require_once 'main_function.php';
require_once 'result_box.php';
$db=newPDO();
$_SESSION=$sess->updateData($db,$_SESSION);
$result=new resultCon(false);
if(count($_POST)<=0)
	$result->result=NULL;
elseif(count($_POST['ch'])<3+$sess->type)
	echo "ยังไม่ส่งข้อมูล";
else
	try{
		$db->beginTransaction();
		$stm=$db->prepare('UPDATE team_info
		LEFT JOIN student_info ON student_info.team_id=team_info.id
		LEFT JOIN quiz_ans ON quiz_ans.team_id=team_info.id
		SET team_info.is_pass=:st, student_info.is_pass=:st, student_info.is_upload=:st, quiz_ans.state=:st
		WHERE team_info.id=:id;');
		$result->result=$stm->execute(array(':id'=>$sess->ID,':st'=>CheckSession::STATE_WAIT));
		echo "ยืนยันข้อมูลสำเร็จ กำลังรอกรรมการการแข่งขันตรวจข้อมูล";
		$_SESSION=$sess->updateData($db,$_SESSION,0);
		$db->commit();
	}catch(Exception $e){
		$db->rollBack();
		echo errMsg($e);
	}
$result->addIfisStr(nl2br(ob_get_contents()));
ob_end_clean();

?>
<!doctype html>
<html><!-- InstanceBegin template="/Templates/mahidol_login.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta charset="utf-8">

<!-- InstanceBeginEditable name="doctitle" -->
<title>ยืนยันข้อมูล: การแข่งขันตอบปัญหาวิทยาศาสตร์การแพทย์</title>
<!-- InstanceEndEditable -->
<script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
<script src="js/jquery-ui-1.10.4.custom.min.js"></script>
<script src="js/mahidol_ajax.js"></script>
<link rel="stylesheet" href="../mahidol.css" />
<link href="css/mahidol_quiz.css" rel="stylesheet" />
<link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" src="js/confirm.js"></script>
<!-- InstanceEndEditable -->
</head>

<body>
<div class="header">&nbsp;</div>
<div class="tab_menu"> </div>
<div class="content">
<div class="headline"><!-- InstanceBeginEditable name="headline" -->ยืนยันข้อมูล<!-- InstanceEndEditable --></div>
<div class="main_content"><!-- InstanceBeginEditable name="main_content" -->
<?
echo $sess->teamMessage($db,CheckSession::PAGE_CONFIRM);
echo $result->getIfNotNull();
if($sess->menuClass(CheckSession::PAGE_CONFIRM, NULL)>CheckSession::STATE_LOCKED):
?>
  <form action="confirm.php" method="post" name="form" id="form">
  <fieldset>
    <legend>ยืนยันข้อมูล</legend>
    <p>
         <input type="checkbox" name="ch[]" value="1" id="ch_0">
        ข้อมูลรายละเอียดทีมถูกต้อง
        <a href="team_info.php" title="ตรวจสอบรายละเอียดทีม" target="_blank">ตรวจสอบ</a><br>
        <input type="checkbox" name="ch[]" value="1" id="ch_1">
        ข้อมูลผู้แข่งขันแต่ละคนถูกต้อง
        <a href="team_info.php" title="ตรวจสอบข้อมูลผู้แข่งขัน" target="_blank">ตรวจสอบ</a><br>
        <input type="checkbox" name="ch[]" value="1" id="ch_2">
        อัพโหลดใบ ปพ.1 แล้ว
        <a href="upload_transcript.php" title="ตรวจสอบใบปพ.1" target="_blank">ตรวจสอบ</a><br>
<? if($sess->type): ?>        <input type="checkbox" name="ch[]" value="1" id="ch_3">
        ทำแบบทดสอบแล้ว
        <a href="quiz.php" title="ตรวจสอบ" target="_blank">ตรวจสอบ</a><? endif; ?></p>
    <p>
      <input type="submit" name="sent" id="sent" value="ส่งข้อมูลให้กรรมการ">
    </p>
  </fieldset>
  </form>
<? else: ?>
  <p>ท่านได้ยืนยันข้อมูลแล้ว ขณะนี้กรรมการการแข่งขันกำลังตรวจสอบข้อมูลของท่าน</p>
<? endif; ?>
<!-- InstanceEndEditable --></div>
</div>
<div class="sidemenu">
<div class="ui-widget-header center ui-corner-top">ข้อมูลเบื้องต้น</div>
<div class="ui-widget-content">
<div class="center bold">ทีม <?=$sess->teamName();?></div>
<div class="center">ประเภท<?=(($sess->type)? 'ทีมอิสระ':'ทีมโรงเรียน')?><br>&nbsp;</div>
<div class="center bold">ความคืบหน้า</div>
<div id="progressbox" class="ui-corner-all ui-widget-content"><div id="progressbar" class="center ui-widget-header ui-corner-all"><?=$sess->progression();?>%</div></div>
</div>
  <p class="ui-widget-header ui-corner-top center">ขั้นตอนการรับสมัคร</p>
  <div class="menubox countMenu ui-widget-content">
  <div class="<?=$sess->menuClass(CheckSession::PAGE_TEAM_INFO)?>"><a href="team_info.php" title="กรอกข้อมูลผู้แข่งขัน">กรอกข้อมูลผู้แข่งขัน</a></div>
  <div class="<?=$sess->menuClass(CheckSession::PAGE_UPLOAD_TSP)?>"><a href="upload_transcript.php" title="Upload ปพ.1">Upload ปพ.1</a></div>
<? if($sess->type): ?>  <div class="<?=$sess->menuClass(CheckSession::PAGE_QUIZ)?>"><a href="quiz.php" title="ทำแบบทดสอบ">ทำแบบทดสอบ</a></div> <? endif; ?>
  <div class="<?=$sess->menuClass(CheckSession::PAGE_CONFIRM)?>"><a href="confirm.php" title="ยืนยันข้อมูล">ยืนยันข้อมูล</a></div>
  <div class="<?=$sess->menuClass(CheckSession::PAGE_PAY)?>"><a href="payment.php" title="Upload หลักฐานการโอนเงิน">Upload หลักฐานการโอนเงิน</a></div>
  <div class="<?=$sess->menuClass(CheckSession::PAGE_RECEIVE_ID)?>"><a href="receive_id.php" title="พิมพ์บัตรประจำตัวผู้แข่งขัน">พิมพ์บัตรประจำตัวผู้แข่งขัน</a></div>
  </div>
  <p class="ui-widget-header ui-corner-top center">หน้าอื่นๆ</p>
  <div class="menubox ui-widget-content">
  <div>&nbsp;&nbsp;<a href="index.php" title="main">หน้าหลัก</a></div>
  <div>&nbsp;&nbsp;<a href="change_password.php" title="เปลี่ยน password">เปลี่ยน password</a></div>
  <div>&nbsp;&nbsp;<a href="logout.php" title="log out">log out</a></div>
  </div>
</div>
<div class="footer">&nbsp;</div>
</body>
<!-- InstanceEnd --></html>