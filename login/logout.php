<?php
session_start();
unset($_SESSION);
session_destroy();
header("Location: login.php");
exit("You have just logged out.");
?>