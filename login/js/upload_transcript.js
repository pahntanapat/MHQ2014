$(document).ready(function(e) {
	$("p[id|='command']").buttonset();
	$("#tabs").tabs();
	$.validate({
		modules : 'file',
		onSuccess:function(form){return $(form).uploadFile(false);}
	});
});