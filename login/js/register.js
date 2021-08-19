$(document).ready(function(e) {
    $.validate({
    	modules : 'security',
		onModulesLoaded : function() {
    		$('#password_confirmation').displayPasswordStrength();
    	},
		onSuccess: function(form) {
			$(form).postAndDisplay("register.php?ajax="+(Math.random()),
				function(r,msg){
					if(!r) $(form).fadeIn("fast");
					$(form).resultBox(r,msg);
			}).parent().removeRBx();
			return false;
   		}
  });
});