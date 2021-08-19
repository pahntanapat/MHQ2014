$(document).ready(function(e) {
	$.validate({
		onSuccess: function(form) {
			$(form).fadeOut("fast",function(){
				$(form).postAndDisplay("login.php?ajax="+(Math.random()),
					function(r,msg){
					if(r) return;
					$(form).fadeIn("fast");
					$(form).resultBox(r,msg);
				});
			}).parent().removeRBx();
			return false;
   	}
  });
});