$(function(){
	if($(window).width()<1200 && $(window).width()>=1000){
		$(".service_pro_pro").css('padding-left','11px');
	}
	if($(window).width()<1000 && $(window).width()>=760){
		$(".service_pro_p2").css('line-height','11px');
	}
	if($(window).width()<760){
		$(".service_pro_pro").width($(".service_o").width());
		$(".service_pro_pro").css('margin','0 auto');
		var w=($(".pic-list").width()-$(".service_o").width())*0.5;
		$(".service_pro_pro").css('position','absolute');
		$(".service_pro_pro").css('left',w+'px');

	}
})