<?php

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>admin</title>
</head>

<body>
<h1>Test Admin</h1><hr>
<h2>ส่วนหลัก</h2>
<h3>คนที่รอยืนยัน Email</h3>
<p>&nbsp;</p>
<h3>User ที่ confirm แล้ว</h3>
<p>&nbsp;</p><hr>
<p>UPDATE team_info<br>
LEFT JOIN student_info ON student_info.team_id=team_info.id<br>
LEFT JOIN quiz_ans ON quiz_ans.team_id=team_info.id<br>
SET team_info.is_pass=-2, team_info.is_pay=1, student_info.is_pass=-2, student_info.is_upload=-2, quiz_ans.state=-2<br>
WHERE team_info.id=0;</p>
</body>
</html>