<?php
require_once 'config.inc.php';
function imgDir($team_id){
	global $_ROOT;
	$dir='/upload/';
	$dir.=floor($team_id/100).'/';
	$dir.=($team_id%100).'/';
	if(!is_dir($_ROOT.$dir))
		mkdir($_ROOT.$dir,0777,true);
	return $dir;
}

function imgTSP($team_id,$student_id){ //TSP = Transcript
	return imgDir($team_id).$team_id.'_'.$student_id.'.jpg';
}

function imgPay($team_id,$team_name,$type){ // For payment section
	return imgDir($team_id).md5('Image of #'.$team_id.' @'.$type.' = '.$team_name.' EXT: JPEG;').'.jpg';
}

class UploadImageException extends Exception{
		const TYPE_UPLOAD_ERROR=1, TYPE_IMG_ERROR=2,
			CODE_UNSUPPORT_EXT=1, CODE_WRONG_FORMAT=2, CODE_UNWRITTABLE=3;
	public function __construct($message='',$code=0,$type=0){
		if($type==self::TYPE_IMG_ERROR){
			$message="ไม่สามารถประมวลผลภาพ ".$message." ได้";
			switch($code){
				case self::CODE_UNSUPPORT_EXT:
					$message.="เนื่องจาก ไม่รองรับไฟล์ประเภทนี้\nกรุณาอัพโหลดไฟล์ JPEG (*.jpg, *.jpeg), PNG (*.png) หรือ GIF (*.gif) เท่านั้น";
					break;
				case self::CODE_WRONG_FORMAT:
					$message.="เนื่องจาก รูปแบบไฟล์ไม่ถูกต้อง อาจเกิดจากไฟล์ภาพเสียหาย";
					break;
				case self::CODE_UNWRITTABLE:
					$message.="เนื่องจากไม่สามารถบันทึกไฟล์ลงบน hosting server ได้ กรุณาติดต่อผู้ดูแลระบบ";
					break;
				default:
			}
		}elseif($type==self::TYPE_UPLOAD_ERROR){
			$message.="ไม่สามารถอัพโหลดไฟล์ได้ ";
			switch($code){
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$message.="เนื่องจากไฟล์ขนาดใหญ่เกินไป\nกรุณาเปลี่ยนประเภทของไฟล์ หรือ ลดความละเอียดของรูปลง";
					break;
				case UPLOAD_ERR_PARTIAL:
					$message.="เนื่องจากไฟล์ที่อัพโหลดไม่สมบูรณ์\nกรุณาตรวจสอบการเชื่อมต่อ internet แล้วลองอัพโหลดอีกครั้ง"
					;break;
				case UPLOAD_ERR_NO_FILE:
					$message.="เนื่องจากระบบไม่พบไฟล์ที่อัพโหลดมาด้วย\nกรุณาตรวจสอบ web browser และ การเชื่อมต่อ internet  แล้วลองอัพโหลดอีกครั้ง";
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
				case UPLOAD_ERR_CANT_WRITE:
				case UPLOAD_ERR_EXTENSION:
					$message.="เนื่องจากระบบเกิดความผิดพลาด (รหัส $code) กรุณาติดต่อผู้ดูแลระบบ";
			}
		}
		parent::__construct($message,$code);
	}
}
?>