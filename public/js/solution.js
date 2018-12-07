window.onload=function(){
		var w=$(".img-responsive").width();
		$(".solution_flo").width(w);
		//获取padding-left的值
		var pl=($(".solution_div").width()-$(".img-responsive").width())*0.5;
		$(".img-responsive").css('padding-left', pl);
		$(".solution_flo").css('margin-left',pl);
		//获取p的line-height的值
		var p_lh=$(".solution_flo").height();
		$(".solution_flo p").css('line-height',p_lh+"px");
		//屏幕小于450px的时候图片xl的数值被改为12，重新获取
		var obj=$(window).width();
		if(obj<450){
			$(".solution_div").removeClass('xl6').addClass('xl12');
			var w=$(".img-responsive").width();
			$(".solution_flo").width(w);
			$(".img-responsive").css('margin', '0 auto');
			var pl=($(".solution_div").width()-$(".img-responsive").width())*0.5;
			$(".solution_flo").css('margin-left',pl);
			var p_lh=$(".solution_flo").height();
			$(".solution_flo p").css('line-height',p_lh+"px");
		}
}