<?php
session_start();
require_once "check_session.php";
$sess=checkSession::mustLogIn();

require_once 'result_box.php';
$db=newPDO();
$_SESSION=$sess->updateData($db,$_SESSION);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<!-- TemplateInfo codeOutsideHTMLIsLocked="false" -->
<!-- TemplateBeginEditable name="doctitle" -->
<title>การแข่งขันตอบปัญหาวิทยาศาสตร์การแพทย์</title>
<!-- TemplateEndEditable -->
<script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
<script src="../login/js/jquery-ui-1.10.4.custom.min.js"></script>
<script src="../login/js/mahidol_ajax.js"></script>
<link rel="stylesheet" href="../mahidol.css" />
<link href="../login/css/mahidol_quiz.css" rel="stylesheet" />
<link href="../login/css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" />
<!-- TemplateBeginEditable name="head" -->
<!-- TemplateEndEditable -->
</head>

<body>
<div class="header">&nbsp;</div>
<div class="tab_menu"> </div>
<div class="content">
<div class="headline"><!-- TemplateBeginEditable name="headline" -->headline<!-- TemplateEndEditable --></div>
<div class="main_content"><!-- TemplateBeginEditable name="main_content" -->main_content<!-- TemplateEndEditable --></div>
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
  <div class="<?=$sess->menuClass(CheckSession::PAGE_TEAM_INFO)?>"><a href="../login/team_info.php" title="กรอกข้อมูลผู้แข่งขัน">กรอกข้อมูลผู้แข่งขัน</a></div>
  <div class="<?=$sess->menuClass(CheckSession::PAGE_UPLOAD_TSP)?>"><a href="../login/upload_transcript.php" title="Upload ปพ.1">Upload ปพ.1</a></div>
<? if($sess->type): ?>  <div class="<?=$sess->menuClass(CheckSession::PAGE_QUIZ)?>"><a href="../login/quiz.php" title="ทำแบบทดสอบ">ทำแบบทดสอบ</a></div> <? endif; ?>
  <div class="<?=$sess->menuClass(CheckSession::PAGE_CONFIRM)?>"><a href="../login/confirm.php" title="ยืนยันข้อมูล">ยืนยันข้อมูล</a></div>
  <div class="<?=$sess->menuClass(CheckSession::PAGE_PAY)?>"><a href="../login/payment.php" title="Upload หลักฐานการโอนเงิน">Upload หลักฐานการโอนเงิน</a></div>
  <div class="<?=$sess->menuClass(CheckSession::PAGE_RECEIVE_ID)?>"><a href="../login/receive_id.php" title="พิมพ์บัตรประจำตัวผู้แข่งขัน">พิมพ์บัตรประจำตัวผู้แข่งขัน</a></div>
  </div>
  <p class="ui-widget-header ui-corner-top center">หน้าอื่นๆ</p>
  <div class="menubox ui-widget-content">
  <div>&nbsp;&nbsp;<a href="../login/index.php" title="main">หน้าหลัก</a></div>
  <div>&nbsp;&nbsp;<a href="../login/change_password.php" title="เปลี่ยน password">เปลี่ยน password</a></div>
  <div>&nbsp;&nbsp;<a href="../login/logout.php" title="log out">log out</a></div>
  </div>
</div>
<div class="footer">&nbsp;</div>
</body>
</html>
