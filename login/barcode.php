<?php
require_once 'bcg/BCGFontFile.php';
require('bcg/BCGColor.php');
require('bcg/BCGDrawing.php');
require('bcg/BCGcodabar.barcode.php');

switch($_GET['order']%4){
	case 0:
		$o='a'; break;
	case 1:
		$o='b'; break;
	case 2:
		$o='c'; break;
	case 3:
		$o='d'; break;
	default:
		$o='a';
}

$font=new BCGFontFile('bcg/Arial.ttf',18);
$code=new BCGcodabar();
$code->setScale(2);
$code->setThickness(35);
$code->setFont($font);
$code->parse($o.$_GET['sorted_id'].$o);

$draw=new BCGDrawing('',$code->getBackgroundColor());
$draw->setBarcode($code);
$draw->draw();
header("Content-Type: image/png");
$draw->finish(BCGDrawing::IMG_FORMAT_PNG);
?>