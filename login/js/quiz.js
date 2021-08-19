$(document).ready(function(e) {
	if($("input:checkbox").length>0){
		$("input:checkbox").click(function(e) {
			if($("input:checked").length==$("input:checkbox").length) $("#start").removeAttr("disabled");
			else $("#start").attr("disabled","disabled");
        });
	}
    if($("#used_time").length>0){
		var startTime,updater,showTime,pg=[null,null,null];
		var endTime=120000,countDown=10000;
		$("#remain").css("color","#F00").css("font-style","italic");
		showTime=setInterval(function(){
			var timer=startTime-((new Date()).getTime());
			$("#used_time").val(endTime-timer);
			if(timer<0){
				$("#quizForm").submit();
			}
			var display="";
			if(timer>endTime){
				timer-=endTime;
				timer/=1000;
				display="... "+timer.toFixed(3);
			}else{
				if(endTime-timer<34){
					$("#remain").removeAttr("style").prev().text("เหลือเวลาอีก");
					$("#answer").removeAttr("readonly").focus();
					$("#send").removeAttr("disabled");
					updater=setInterval(function(){
						$("#connection").html("กำลังติดต่อ server...");
						$("#connection").load("quiz.php?act="+(timer)+"&ajax="+(Math.random()),$("#quizForm").serializeArray());
					},endTime/10);
					pg[2]=setTimeout(function(){
						$.reloadProgressMenu();
					}, endTime/5);
				}
				display=Math.floor(timer/60000)+":";
				timer=Math.abs((timer%60000)/1000);
				display+=timer.toFixed(3);
			}
			$("#remain").html(display);
		},33);
		startTime=(new Date()).getTime()+countDown+endTime;
		pg[1]=setTimeout(function(){
			$("#remain").css("color","#F00");
		}, countDown+endTime-10000);
		pg[0]=setTimeout(function(){
			$("#remain").css("color","#E87600");
		}, countDown+endTime-30000);
		$("#quizForm").submit(function(e) {
			clearInterval(updater);
			clearInterval(showTime);
			clearTimeout(pg[0]);
			clearTimeout(pg[1]);
			clearTimeout(pg[2]);
            return true;
        });
	}
});
/*
<!DOCTYPE html>
<html>
<body>

<button onclick="startCount()">Start count!</button><br>
<input type="text" id="txt"> - 
<input type="text" id="time"> = 
<input type="text" id="diff"> 
(<input type="text" id="pc">) 
<button onclick="stopCount()">Stop count!</button>

<p>
Click on the "Start count!" button above to start the timer. The input field will count forever, starting at 0. Click on the "Stop count!" button to stop the counting. Click on the "Start count!" button to start the timer again.
</p>

<script>
var c = 0,intv=10;
var t;
var timer_is_on = 0;
var startTime, now, myVar;

function timedCount()
{
t = setTimeout(function(){timedCount()},intv);
document.getElementById("txt").value=c;
document.getElementById("diff").value=c-(now-startTime);
document.getElementById("pc").value=(c-now+startTime)/(now-startTime);
c += intv;
}

function startCount()
{
if (!timer_is_on)
  {
  timer_is_on=1;
  var timer=new Date();
  startTime=timer.getTime();
  myVar = setInterval(function(){myTimer()},intv);
  timedCount();
  }
}

function myTimer()
{
var timer=new Date();
now=timer.getTime();
document.getElementById("time").value=(now-startTime);
document.getElementById("diff").value=c-(now-startTime);
document.getElementById("pc").value=(c-now+startTime)/(now-startTime);
}
function stopCount()
{
clearInterval(myVar);
clearTimeout(t);
timer_is_on=0;
}
</script>

</body>
</html>
*/