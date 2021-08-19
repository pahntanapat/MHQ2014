<?php
session_start();
require_once 'config.inc.php';
require_once "check_session.php";
$sess=checkSession::mustLogIn();

$db=newPDO();
$_SESSION=$sess->updateData($db,$_SESSION);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>พิมพ์บัตรประจำตัวผู้แข่งขัน: การแข่งขันตอบปัญหาวิทยาศาสตร์การแพทย์</title>
<link rel="stylesheet" href="../mahidol.css"/>
<link rel="stylesheet" href="css/mahidol_quiz.css" />
</head>

<body>
<?php
if(!isset($_GET['i'])):
try{
?>
<div class="a4">
  <h1>การแข่งขันตอบปัญหาวิทยาศาสตร์สุขภาพ</h1>
  <h2>Mahidol Quiz</h2><br>
<?
$sql='SELECT sorted_id, t_firstname, t_lastname, t_phone FROM team_info WHERE id=:id LIMIT 1;';
$stm=$db->prepare($sql);
$stm->execute(array(':id'=>$sess->ID));
$row=$stm->fetch(PDO::FETCH_ASSOC);
$i=0;
?>  
  <h3>รายละเอียดทีม</h3>
   <table width="100%" border="0" cellspacing="2">
    <tr>
      <th width="230" rowspan="5" align="center" valign="middle"><img src="barcode.php?order=<?=$i?>&sorted_id=<?=$row['sorted_id']?>" alt="รหัสประจำทีม"></th>
      <th width="200" scope="row">ชื่อทีม</th>
      <td><?=$sess->teamName()?></td>
    </tr>
    <tr>
      <th width="200" scope="row">ประเภท</th>
      <td>ทีม<?=($sess->type)?'อิสระ':'โรงเรียน'?></td>
      </tr>
    <tr>
      <th width="200" scope="row">ครูที่ปรึกษา</th>
      <td><?=$row['t_firstname'].' '.$row['t_lastname']?></td>
      </tr>
    <tr>
      <th width="200" scope="row">โทร</th>
      <td><?=$row['t_phone']?></td>
      </tr>
    <tr>
      <th width="200" scope="row">รหัสประจำทีม</th>
      <td><?=$row['sorted_id']?></td>
    </tr>
    </table>

<?php
$sql='SELECT title, firstname, lastname, phone, school, sorted_id, exam_room FROM student_info WHERE team_id=:id';
$stm=$db->prepare($sql);
$stm->execute(array(':id'=>$sess->ID));
while($row=$stm->fetch(PDO::FETCH_ASSOC)){
?>
<hr>
<h3>ผู้แข่งขันคนที่ 
  <?=++$i?>
</h3>
<table width="100%" border="0" cellspacing="2">
  <tr>
    <td width="230" rowspan="5" align="center" valign="middle"><img src="barcode.php?order=<?=$i?>&amp;sorted_id=<?=$row['sorted_id']?>" alt="รหัสประจำตัว"></td>
    <th width="200" scope="row">ชื่อ - นามสกุล</th>
    <td><?=$row['title']?> <?=$row['firstname']?> <?=$row['lastname']?></td>
  </tr>
  <tr>
    <th scope="row">โทร</th>
    <td><?=$row['phone']?></td>
  </tr>
  <tr>
    <th width="200" scope="row">โรงเรียน</th>
    <td><?=$row['school']?></td>
    </tr>
  <tr>
    <th width="200" scope="row">รหัสประจำตัว</th>
    <td><?=$row['sorted_id']?></td>
    </tr>
  <tr>
    <th width="200" scope="row">ห้องสอบ</th>
    <td><?=$row['exam_room']?></td>
    </tr>
</table>
<div class="right">(<?=$sess->teamName()?> | ทีม<?=($sess->type)?'อิสระ':'โรงเรียน'?>)</div>
<?php
}
}catch(Exception $e){
	echo errMsg($e,$sql);
}
?>
</div>
<?php
else: ?><table border="0"><?php
try{
	if($_GET['i']==0)
		$sql='SELECT sorted_id, t_firstname, t_lastname, t_phone FROM team_info WHERE id=:id LIMIT 1;';
	else
		$sql='SELECT title, firstname, lastname, phone, school, sorted_id, exam_room FROM student_info WHERE team_id=:id LIMIT '.($_GET['i']-1).', 1;';
	$stm=$db->prepare($sql);
	$stm->execute(array(':id'=>$sess->ID));
	
$stm=$db->prepare($sql);
$stm->execute(array(':id'=>$sess->ID));
$row=$stm->fetch(PDO::FETCH_ASSOC);
if($_GET['i']==0):
?>
  <tr>
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
<?php
else:
?>
  <tr>
    <th scope="row">ชื่อ - นามสกุล</th>
    <td><?=$row['title']?>
      <?=$row['firstname']?>
      <?=$row['lastname']?></td>
  </tr>
  <tr>
    <th scope="row">โทร</th>
    <td><?=$row['phone']?></td>
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
<?php
endif;
?>
<tr><td colspan="2" class="center">
<img src="barcode.php?order=<?=$_GET['i']?>&amp;sorted_id=<?=$row['sorted_id']?>" alt="barcode"></td>
</tr>
</table>
<?php
}catch(Exception $e){
	echo errMsg($e,$sql);
}
endif;
?>
</body>
</html>