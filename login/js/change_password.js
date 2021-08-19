$(document).ready(function(e) {
    $.validate({
		modules : 'security',
		onModulesLoaded : function() {
    		$('#new_pw_confirmation').displayPasswordStrength();
			$("#old_pw").displayPasswordStrength();
    	},
		onSuccess: function(form) {
			$(form).postAndDisplay(form.attr("action")+"?ajax="+(Math.random()),
				function(r,msg){
					$(form).fadeIn("fast").resultBox(r,msg);
			}).parent().removeRBx();
			return false;
   		}
	});
});