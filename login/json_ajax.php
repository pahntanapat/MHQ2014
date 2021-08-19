<?php
class jsonAjax{
	private $arrayAction;
	public $result,$message;
	const ALERT="alert", REDIRECT="redirect", EVALUTE="eval", FOCUS="focus",
		SET_TEXT="setText", SET_HTML="setHTML", SET_VAL="setVal",
		RELOAD_CAPTCHA="reloadCAPTCHA", SCROLL_TO="scrollTo";
	
	public function __construct($json=NULL){
		$this->arrayAction=array();
		$this->result=NULL;
		$this->message='';
		if($json!=NULL)
			self::fromJSON($json);
	}
	public function __destruct(){
		unset($this->arrayAction);
	}
	public function getAction($id=NULL){
		if($id==NULL)
			return $this->arrayAction;
		elseif($id<count($this->arrayAction))
			return $this->arrayAction[$id];
		else return NULL;
	}
	public function setAction($id,$act,$how=NULL){
		$arr=array('act'=>$act);
		switch($act){
			case self::SET_HTML:
			case self::SET_TEXT:
			case self::SET_VAL:
				$arr['selector']=self::getOption($how,'selector');
			case self::ALERT:
				$arr['message']=self::getOption($how,'message');break;
			case self::REDIRECT:
				$arr['url']=self::getOption($how,'url');break;
			case self::EVALUTE:
				$arr['script']=self::getOption($how,'script');break;
			case self::RELOAD_CAPTCHA: break;
			case self::SCROLL_TO:
			case self::FOCUS:
				$arr['selector']=self::getOption($how,'selector');break;
		}
		$this->arrayAction[$id]=$arr;
		return count($this->arrayAction);
	}
	public function addAction($act,$how=NULL){
		return $this->setAction(count($this->arrayAction),$act,$how);
	}
	public function addHtmlTextVal($action=jsonAjax::SET_HTML,$selector="body",$msg=""){
		return $this->addAction($action,array('selector'=>$selector,'message'=>$msg));
	}
	public function removeAction($id=-1){
		if($id<0){
			$this->arrayAction=array();
			return 0;
		}
		unset($this->arrayAction[$id]);
		$this->arrayAction=array_values($this->arrayAction);
		return count($this->arrayAction);
	}
	public function setResult(resultCon $result_con){
		$this->result=$result_con->result;
		$this->message=$result_con->message;
	}
	public function toJSON($option=0,$depth=512){
		$r=array();
		$r['result']=$this->result;
		$r['message']=$this->message;
		$r['action']=$this->arrayAction;
		return json_encode($r,$option,$depth);
	}
	public function fromJSON($json,$assoc=false,$depth=512,$option=0){
		$r=json_decode($json,$assoc,$depth,$option);
		$this->arrayAction=$r['action'];
		$this->message=$r['message'];
		$this->result=$r['result'];
	}
	public function __toString(){
		return $this->toJSON();
	}
	public static function getOption($arr,$key=0){
		if(is_array($arr)){
			if(isset($arr[$key])) return $arr[$key];
			else return $arr[0];
		}elseif(is_object($arr)){
			if(isset($arr->{$key})) return $arr->{$key};
			else return $arr->__toString();
		}else return $arr;
	}
}
?>