<?php
session_start();
require_once 'config.inc.php';
require_once "check_session.php";
$sess=checkSession::mustLogIn();

$db=newPDO();
$_SESSION=$sess->updateData($db,$_SESSION);
?>
<!doctype html>
<html><!-- InstanceBegin template="/Templates/mahidol_login.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta charset="utf-8">

<!-- InstanceBeginEditable name="doctitle" -->
<title>พิมพ์บัตรประจำตัวผู้แข่งขัน: การแข่งขันตอบปัญหาวิทยาศาสตร์การแพทย์</title>
<!-- InstanceEndEditable -->
<script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
<script src="js/jquery-ui-1.10.4.custom.min.js"></script>
<script src="js/mahidol_ajax.js"></script>
<link rel="stylesheet" href="../mahidol.css" />
<link href="css/mahidol_quiz.css" rel="stylesheet" />
<link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
<div class="header">&nbsp;</div>
<div class="tab_menu"> </div>
<div class="content">
<div class="headline"><!-- InstanceBeginEditable name="headline" -->พิมพ์บัตรประจำตัวผู้แข่งขัน<!-- InstanceEndEditable --></div>
<div class="main_content"><!-- InstanceBeginEditable name="main_content" -->
<?=$sess->showState(CheckSession::PAGE_RECEIVE_ID)?>
<?=$sess->teamMessage($db,CheckSession::PAGE_RECEIVE_ID,true)?>
  <h3>รายละเอียดทีมและผู้แข่งขัน</h3>
<?php
try{
	$stm=$db->prepare('SELECT sorted_id, t_firstname, t_lastname, t_phone FROM team_info WHERE id=:id;');
	$stm->execute(array(':id'=>$sess->ID));
	$row=$stm->fetch(PDO::FETCH_ASSOC);
	$i=0;
?>
<h4>รายละเอียดทีม</h4>
  <table border="0" cellspacing="2">
    <tr>
      <td rowspan="5" align="right" valign="middle"><img src="barcode.php?order=<?=$i?>&amp;sorted_id=<?=$row['sorted_id']?>" alt="รหัสประจำทีม"></td>
      <th scope="row">ชื่อทีม</th>
      <td><?=$sess->teamName()?></td>
    </tr>
    <tr>
      <th scope="row">ประเภท</th>
      <td>ทีม<?=($sess->type)?'อิสระ':'โรงเรียน'?></td>
      </tr>
    <tr>
      <th scope="row">ครูที่ปรึกษา</th>
      <td><?=$row['t_firstname'].' '.$row['t_lastname']?></td>
      </tr>
    <tr>
      <th scope="row">โทร</th>
      <td><?=$row['t_phone']?></td>
      </tr>
    <tr>
      <th scope="row">รหัสประจำทีม</th>
      <td><?=$row['sorted_id']?></td>
    </tr>
    </table>
    <p><a href="print.php" title="พิมพ์บัตรประจำตัวผู้แข่งขัน" target="_blank">พิมพ์บัตรประจำตัวผู้แข่งขัน</a> หรือ <a href="print.php?i=<?=$i?>" title="capture หน้าจอ" target="_blank">capture หน้าจอ</a></p>
<?php
	$stm=$db->prepare('SELECT title, firstname, lastname, school, sorted_id, exam_room FROM student_info WHERE team_id=:id');
	$stm->execute(array(':id'=>$sess->ID));
	while($row=$stm->fetch(PDO::FETCH_ASSOC)){
?>
<h4>ผู้แข่งขันคนที่ <?=++$i?></h4>
<table border="0" cellspacing="2">
  <tr>
    <td rowspan="4" align="right" valign="middle"><img src="barcode.php?order=<?=$i?>&amp;sorted_id=<?=$row['sorted_id']?>" alt="รหัสประจำทีม"></td>
    <th scope="row">ชื่อ - นามสกุล</th>
    <td><?=$row['title']?> <?=$row['firstname']?> <?=$row['lastname']?></td>
  </tr>
  <tr>
    <th scope="row">โรงเรียน</th>
    <td><?=$row['school']?></td>
    </tr>
  <tr>
    <th scope="row">รหัสประจำตัว</th>
    <td><?=$row['sorted_id']?></td>
    </tr>
  <tr>
    <th scope="row">ห้องสอบ</th>
    <td><?=$row['exam_room']?></td>
    </tr>
</table>
    <p><a href="print.php" title="พิมพ์บัตรประจำตัวผู้แข่งขัน" target="_blank">พิมพ์บัตรประจำตัวผู้แข่งขัน</a> หรือ <a href="print.php?i=<?=$i?>" title="capture หน้าจอ" target="_blank">capture หน้าจอ</a></p>
<?
	}
} catch(Exception $e){
?> 
  <p class="redpink"><?=errMsg($e)?></p>
<? } ?>
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
