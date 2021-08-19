$(document).ready(function(e) {
	function validation(){
	$("#tabs").tabs();
 	   $.validate({
			onSuccess: function(form) {
				$(form).postAndDisplay($(form).attr("action")+"?ajax="+(Math.random()),
					function(r,msg){
						if(!r) $(form).fadeIn("fast");
						$("#tabs").fadeIn("fast").parent().resultBox(r,msg);
						$.reloadProgressMenu();
						validation();
				});
				$("#tabs").fadeOut("fast").parent().removeRBx();
				return false;
	   		}
		});
	}
	validation();
});