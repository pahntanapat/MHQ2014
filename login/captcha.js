$(document).ready(function(e) {
    $("#reload_img").click(function(e){
		$("#siimage").attr("src","securimage/securimage_show.php?sid="+(Math.random()));
		$("#captcha").val("");
	});
});