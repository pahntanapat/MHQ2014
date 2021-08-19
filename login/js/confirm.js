$(document).ready(function(e) {
    function checkList(){
		if($("input:checked").length==$("input:checkbox").length){
			$("#sent").removeAttr("disabled");
			return true;
		}else{
			$("#sent").attr("disabled","disabled");
			return false;
		}
	}
	$("input:checkbox").click(function(e) {
        checkList();
    });
	$("form").submit(function(e) {
        return checkList();
    });
	checkList();
});