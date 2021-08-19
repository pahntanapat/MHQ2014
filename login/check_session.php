<?php
require_once 'main_function.php';
require_once 'json_ajax.php';
class CheckSession{
	const SQL=<<<SQL
SELECT
team_info.id AS ID,	team_info.team_name AS teamName,		team_info.type AS type,
team_info.is_pass AS sTeamInfo,		team_info.is_pay AS sTeamPay,
quiz_ans.state AS sQuiz,
student_info.is_upload AS sStdDoc,		student_info.is_pass AS sStdInfo,		LENGTH(student_info.sorted_id)>0 AS sPrint
FROM team_info
LEFT JOIN quiz_ans ON quiz_ans.team_id=team_info.id
LEFT JOIN student_info ON student_info.team_id=team_info.id
SQL;
	
	const
		PAGE_TEAM_INFO=10, 
			SECT_INFO_STD_1=11,
			SECT_INFO_STD_2=12,
			SECT_INFO_STD_3=13,
			SECT_INFO_TEAM=14,
		PAGE_UPLOAD_TSP=20,
			SECT_TSP_STD_1=21,
			SECT_TSP_STD_2=22,
			SECT_TSP_STD_3=23,
		PAGE_QUIZ=30,
		PAGE_CONFIRM=40,
		PAGE_PAY=50,
		PAGE_RECEIVE_ID=60;
	
	const STATE_NOT_PASS=-3, STATE_PASS=-2, STATE_WAIT=-1, STATE_LOCKED=0,
		STATE_NOT_FINISHED=1, STATE_EDITTABLE=2, STATE_MUST_CHANGE=3;
	
	public $ID,$type,$sTeamInfo=1,$sTeamPay=0,$sStdDoc=array(0,0,0),$sStdInfo=array(0,0,0),$sQuiz=0,$sPrint=array(0,0,0);
	protected $teamName='',$ip;
	
	public static function isLogIn(){
		if(session_id()=='') return NULL;
		$s=new CheckSession($_SESSION);
		return ($s->isIP($_SERVER['REMOTE_ADDR']))? $s:false;
	}
	public static function mustLogIn(){ //In page that must log in
		$s=self::isLogIn();
		if($s!==false) return $s;
		unset($_SESSION);
		if(isset($_GET['ajax'])){
			header('Content-type: application/json');
			$res=new jsonAjax();
			$res->addAction(jsonAjax::REDIRECT,"login.php");
			exit($res->__toString());
		}else{
			header("Location: login.php");
			exit("You do not log in or your session is expired.\nWe are redirecting to log in page.");
		}
	}
	
	public static function whenLogIn(){ //In register, log in page
		if(self::isLogIn()===false) return;
		if(isset($_GET['ajax'])){
			header('Content-type: application/json');
			$res=new jsonAjax();
			$res->addAction(jsonAjax::REDIRECT,"./");
			exit($res->__toString());
		}else{
			header("Location: ./");
			exit("You have logged in.\nWe are redirecting to main page.");
		}
	}
	
	public function __construct($SESSION=NULL){
		if($SESSION==NULL) return;
		foreach($this as $k=>$v) $this->$k=isset($SESSION[$k])?$SESSION[$k]:$v;
	}
	public function toSession($re=array()){
		foreach($this as $k=>$v) $re[$k]=$v;
		$re['createTime']=time();
		return $re;
	}
	public function isIP($ip){
		if(isset($_SESSION['creatTime'])) return false;
		return ($ip==$_SERVER['REMOTE_ADDR'] && $ip==$this->ip && $_SESSION['createTime']>1440);
	}
	public function newUser($id,$type){
		$this->ID=$id;
		$this->type=$type;
		$this->ip=$_SERVER['REMOTE_ADDR'];
	}
	public function fromDB(PDOStatement $stm){
		$i=0;
		while($row=$stm->fetch(PDO::FETCH_ASSOC)){
			foreach($this as $k=>$v){
				if($k=='sStdDoc' || $k=='sStdInfo' || $k=='sPrint')
					$this->{$k}[$i]=($row[$k]=='')? self::STATE_LOCKED:intval($row[$k]);
				elseif($k!='ip')	$this->$k=($row[$k]=='')? $v:$row[$k];
			}
			$i++;
		}
		$this->ip=$_SERVER['REMOTE_ADDR'];
		unset($row,$i);
		return $stm;
	}
	
	public function updateData(PDO $db=NULL,$session=array(),$freq=600){ //600 s = 10 min
		if($session['createTime']+$freq>=time()) return $session;
		session_regenerate_id(true);
		if($db==NULL) $db=newPDO();
		$stm=$db->prepare(self::SQL.' WHERE team_info.id=:id');
		$stm->execute(array(':id'=>$this->ID));
		self::fromDB($stm);
		unset($stm,$db,$freq);
		return self::toSession($session);
	}
	
	
	public function teamName(){
		return ($this->teamName=='')? '<<ยังไม่มีชื่อทีม>>':$this->teamName;
	}
	/**
	*tabsAdd()
	*for generate tabs index in team_info.php
	*/
	public function tabsArrangeForTeamInfo(){
		if($this->sTeamInfo==1) return -1;
		else return array_search(0,$this->sStdInfo);
	}
	/**
	*generate class for side menu
	*and return state of each page ($item)
	*/
	public function menuClass($item,$toCSS=array('wrong','correct','wait','lock','q','edit','err')){
		$priority=array( #from min to max
			self::STATE_EDITTABLE, #  (edittable)  edit = lowest
			self::STATE_NOT_FINISHED, # (not complete) q
			self::STATE_PASS, # (pass) correct
			self::STATE_MUST_CHANGE, # (error) err
			self::STATE_WAIT, # (waiting) wait
			self::STATE_NOT_PASS, # (not pass) wrong
			self::STATE_LOCKED # (lock) lock = highest
		);
		switch($item){
			case self::PAGE_TEAM_INFO:
				$temp=array();
				foreach($this->sStdInfo as $v)
					$temp[]=array_search($v,$priority);
				$temp[]=array_search($this->sTeamInfo,$priority);
				$css=$priority[max($temp)];
				unset($temp,$v);
				if($css==self::STATE_LOCKED) // PAGE_TEAM_INFO is unlockable.
					$css=self::STATE_NOT_FINISHED;
				break;
			case self::PAGE_UPLOAD_TSP:
				$temp=array();
				foreach($this->sStdDoc as $v)
					$temp[]=array_search($v,$priority);
				$css=$priority[max($temp)];
				unset($temp,$v);
				break;
			case self::PAGE_QUIZ:
				if($this->type)
					$css=(
					$this->sQuiz==self::STATE_LOCKED &&
					count(array_diff_assoc(
						$this->sStdDoc,
						array(self::STATE_EDITTABLE,self::STATE_EDITTABLE,self::STATE_EDITTABLE)
					))==0)? self::STATE_NOT_FINISHED:$this->sQuiz;
				else
					$css=self::STATE_PASS;
				break;
			case self::PAGE_CONFIRM:
				$temp=array();
				foreach($this->sStdDoc as $v) $temp[]=array_search($v,$priority);
				foreach($this->sStdInfo as $v) $temp[]=array_search($v,$priority);
				$temp[]=array_search($this->sTeamInfo,$priority);
				$css=$priority[max($temp)];
				unset($temp,$v);
				if($css==self::STATE_EDITTABLE) $css=self::STATE_NOT_FINISHED; //PAGE_RECEIVE_ID is one time edittable
				break;
			case self::PAGE_PAY:
				$css=$this->sTeamPay;
				break;
			case self::PAGE_RECEIVE_ID:
				// find if each user is received sorted_id
				$css=(array_search(self::STATE_LOCKED, $this->sPrint)===false)? self::STATE_PASS:self::STATE_LOCKED;
				if($this->sTeamPay==self::STATE_PASS && $css==self::STATE_LOCKED)
					$css=self::STATE_WAIT; // if team paid registry fee but they are not received sorted_id
				break;
		}
		return (count($toCSS)>=7)? $toCSS[$css+3]:$css;
	}
	/*
	*progression for progressbar
	*/
	public function progression(){
		$s=array(6,8,5,-1,0,2,3);
		$sum=$s[$this->sTeamInfo+3];
		foreach($this->sStdDoc as $v) $sum+=$s[$v+3];
		foreach($this->sStdInfo as $v) $sum+=$s[$v+3];
		for($i=self::PAGE_QUIZ;$i<=self::PAGE_RECEIVE_ID;$i+=10)
			$sum+=self::menuClass($i,$s);
		return 12+$sum;
	}
	/**
	*show state description for user
	*/
	public function showState($pageIndex,$preset=false){ //Same to show_page in table team_message
		if($preset)
			$state=$preset;
		else
			switch($pageIndex){
				case self::PAGE_CONFIRM:
				case self::PAGE_PAY:
				case self::PAGE_RECEIVE_ID:
				case self::PAGE_TEAM_INFO:
				case self::PAGE_UPLOAD_TSP:
					$state=self::menuClass($pageIndex,NULL);
					break;
				case self::SECT_INFO_TEAM:
					$state=$this->sTeamInfo;
					break;
				case self::SECT_INFO_STD_1:
				case self::SECT_INFO_STD_2:
				case self::SECT_INFO_STD_3:
					$state=$this->sStdInfo[($pageIndex%10)-1];
					break;
				case self::SECT_TSP_STD_1:
				case self::SECT_TSP_STD_2:
				case self::SECT_TSP_STD_3:
					$state=$this->sStdDoc[($pageIndex%10)-1];
				default:
					return $preset;
			}

		switch($state){
			case self::STATE_NOT_PASS: return <<<HTML
<p class="redpink"><img src="image/wrong.png" alt="ไม่ผ่าน"> - ข้อมูลส่วนนี้ไม่ผ่าน และท่านไม่สามารถแก้ไขข้อมูลได้</p>
HTML;
			case self::STATE_PASS: return <<<HTML
<p class="green"><img src="image/correct.png" alt="ผ่าน"> - ข้อมูลส่วนนี้ผ่าน หากต้องการแก้ไขกรุณาติดต่อกรรมการการแข่งขัน</p>
HTML;
			case self::STATE_WAIT: return <<<HTML
<p class="orange"><img src="image/refresh.png" alt="รอการตรวจสอบ"> - กรรมการกำลังตรวจสอบข้อมูลนี้</p>
HTML;
			case self::STATE_LOCKED: return <<<HTML
<p class="grey"><img src="image/lock.png" alt="lock"> - ท่านไม่สามารถแก้ไขข้อมูลส่วนนี้ได้</p>
HTML;
			case self::STATE_NOT_FINISHED: return <<<HTML
<p class="lightblue"><img src="image/q.png" alt="กรุณากรอกข้อมูล"> - ท่านยังกรอกข้อมูลส่วนนี้ไม่เสร็จ กรุณากรอกข้อมูล</p>
HTML;
			case self::STATE_EDITTABLE: return <<<HTML
<p class="custard"><img src="image/pencil.png" alt="แก้ไข"> - ท่านสามารถแก้ไขข้อมูลส่วนนี้ได้</p>
HTML;
			case self::STATE_MUST_CHANGE: return <<<HTML
<p class="redpink"><img src="image/error.png" alt="กรุณาแก้ไขด่วน"> - ข้อมูลส่วนนี้ไม่ถูกต้อง กรุณาแก้ไขด่วน</p>
HTML;
		}
	}
	function teamMessage(PDO $db,$section=NULL,$showIfNo=false,$isAjax=false){
		if(!$isAjax)
			$htm=<<<HTML
<div class="ui-widget"><div class="ui-widget-header ui-corner-top team_message xlarge">การแจ้งเตือน<button id="reloadMsg" data-section="$section" data-showIfNo="$showIfNo">Reload</button></div><div class="ui-widget-content ui-corner-bottom team_message" id="reloadArea">
HTML;
		else
			$htm="";

		try{
			$sql='SELECT title,detail,show_page,time FROM team_message WHERE team_id=:id AND show_page';
			if($section==NULL) $sql.='!=0';
			elseif($section%10!=0) $sql.='=:pg';
			else	$sql.=' BETWEEN :pg AND (:pg+9)';
			
			$sql.=' ORDER BY id DESC;';
			$stm=$db->prepare($sql);
			$stm->bindParam(':id',$this->ID,PDO::PARAM_INT);
			if($section!=NULL){
				$stm->bindParam(':pg',$section,PDO::PARAM_INT);
				/*if($section%10==0){
					$sc=$section+9;
					$stm->bindParam(':pge',$sc,PDO::PARAM_INT);
					unset($sc);
				}*/
			}
			$stm->execute();
			if($stm->rowCount()>0){
				$htm.="<div class=\"team_message_area\">\n";
				while($row=$stm->fetch(PDO::FETCH_ASSOC)){
					$htm.=<<<HTML
<h3>$row[title]</h3>
<div>
<p><b>หัวข้อ:</b> $row[title]</p>
<p><b>เวลา:</b> $row[time]</p>
<p><b>รายละเอียด:</b><br>$row[detail]</p>
</div>
HTML;
				}
				$htm.="</div>\n";
			}elseif($showIfNo){
				$htm.=<<<I
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		ท่านยังไม่มีข้อความแจ้งเตือน</p>
	</div>
</div>
I;
			}else return "";
		}catch(Exception $e){
			$htm=<<<HTML
<div class="ui-widget">
	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<strong>Error:</strong> ไม่สามารถแสดงข้อความแจ้งเตือนได้เนื่องจาก<br>$e<br><br>{$e->getMessage()}<br>SQL = $sql</p>
	</div>
</div>
HTML;
		}
		if(!$isAjax) $htm.="</div></div>";
		return $htm;
	}
}
?>