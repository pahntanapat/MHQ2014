<?php
class resultCon{
	public $result,$message;
	public function __construct($re=NULL){
		$this->result=$re;
	}
	public function addIfisStr($msg){
		if(is_string($msg)) $this->message.=$msg;
	}
	public function __toString(){
		$CSS=$this->result==true ? ' neutral':($this->result===false ? ' warning':''); //CSS Class in mahidol.css
		$htm=<<<HTML
<div class="result$CSS">{$this->message}</div>
HTML;
		return $htm;
	}
	public function getIfNotNull(){
		if(strlen($this->message)>0) return "<div>{$this->__toString()}</div>";
	}
}

if(isset($_REQUEST['ajax'],$_REQUEST['result'])){ //for ajax request
	$result=new resultCon(filter_var($_REQUEST['result'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
	if(isset($_REQUEST['message']))
		$result->message=$_REQUEST['message'];
	echo $result;
	unset($result);
}
?>