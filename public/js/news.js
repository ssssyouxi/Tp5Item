$(function(){
		var obj=$(window).width();
			if(obj < 1000){
			$(".news_title").removeClass("float-right");
			$(".news_img").addClass("img-responsive");
			$("#previous1").html("Previous");
			$("#next1").html("Next")
		}else{
			$(".news_title").addClass('float-right');
			$(".news_img").removeClass("img-responsive");
			$("#previous1").html("Previous: Kefid's tailings processing system is ...");
			$("#next1").html("Next: Kefid's tailings processing system is ...")
		};
	$(window).resize(function(){
		var obj=$(window).width();
		if(obj < 1000){
			$(".news_title").removeClass("float-right");
			$(".news_img").addClass("img-responsive");
			$("#previous1").html("Previous");
			$("#next1").html("Next");
		}else{
			$(".news_title").addClass('float-right');
			$(".news_img").removeClass("img-responsive");
			$("#previous1").html("Previous: Kefid's tailings processing system is ...");
			$("#next1").html("Next: Kefid's tailings processing system is ...")
		};
	})
})