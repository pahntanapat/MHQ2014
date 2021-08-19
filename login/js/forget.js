$(document).ready(function(e) {
    $.validate({
		onSuccess: function(form) {
			$(form).postAndDisplay($(form).attr("action")+"?ajax="+(Math.random()),
				function(r,msg){
					if(!r) $(form).fadeIn("fast");
					$(form).resultBox(r,msg);
			}).parent().removeRBx();
			return false;
   		}
	});
});