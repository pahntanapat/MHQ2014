(function($){
	$.fn.processJSON=function(data,callback){
		for(var k in data.action){
			var json=data.action[k];
			switch(json.act){
				case "alert":
					alert(json.message);continue;
				case "redirect":
					window.location=json.url;continue;
				case "eval":
					$.globalEval(json.script);continue;
				case "setText":
					$(json.selector).text(json.message);continue;
				case "setHTML":
					$(json.selector).html(json.message);continue;
				case "setVal":
					$(json.selector).val(json.message);continue;
				case "reloadCAPTCHA":
					 $("#reload_img").click();continue;
				case "scrollTo":
					$("html, body").animate({scrollTop:$(json.selector).offset().top},"fast");continue;
				case "focus":
					$(json.selector).focus();continue;
				/*
				case "":
					continue;
				*/
				default:
			}
		}
		return callback(data.result,data.message);
	};
    $.fn.postAndDisplay=function(url,callback){
		var t=this;
		$(t).fadeOut("fast").before("<div><div id=\"loadingBar\"></div></div>").prev().load("loading_bar.html"); //hide form and add loading bar
		$.post(url,t.serialize(),function(data){
			$(t).parent().find("#loadingBar").fadeOut("fast").parent().remove();
			return $(t).processJSON(data,callback);
		},"json");
		return t;
	};
	 $.fn.resultBox=function(result,msg){
			$(this).after("<div></div>").next().load("result_box.php?ajax="+(Math.random()),{"result":result,"message":msg});
		 return this;
	 };
	 $.fn.removeRBx=function(){
		 $(this).parent().find(".result").fadeOut("fast").parent().remove();
		 return this;
	 };
	 $.progress=function(pg){
		 if(pg<0) pg=$("#progressbar").html();
		 else pg=pg+'%';
		 $("#progressbar").width(pg).css("color","#90F").html("&nbsp;");
	 };
	 $.fn.uploadFile=function(re){
		 var form=this;
		$(form).ajaxSubmit({
			clearForm:true,
			dataType:'json',
			url:$(form).attr("action")+"?ajax="+(Math.random()),
			beforeSerialize:function(){
				$("#pgb").progressbar({value:false});
			},
			uploadProgress:function(e,val,maxV){
				$("#pgb").progressbar({value:val,"max":maxV});
			},
			success: function(data){
				return $(form).remove("#pgb").processJSON(data,function(r,msg){
					$(form).resultBox(r,msg);
					$(form).find("#pgb").remove();
					$.reloadProgressMenu();
					return true;
				});
			}
		}).find("fieldset").append("<div id=\"pgb\"></div>");
		$(form).parent().removeRBx();
		return re;
	};
	$.reloadProgressMenu=function(){
		$.post("index.php?ajax="+(Math.random()),{"load":"progressMenu"},function(data){
			$.progress(data.progress);
			var menu=$(".countMenu").find("div");
			for(var i in data.menu)
				$(menu[i]).removeClass().addClass(data.menu[i]);
		},"json");
	}
}(jQuery));
$(document).ready(function(e) {
	$.progress(-1);
	$(".team_message_area").accordion({collapsible: true});
	$("#reloadMsg").click(function(e) {
        $("#reloadArea").load("./?ajax="+(Math.random()),$("#reloadMsg").data(),function(){$(".team_message_area").accordion({collapsible: true});});
		$.reloadProgressMenu();
    });
});