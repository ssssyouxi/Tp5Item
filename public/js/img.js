$(document).ready(function(){
var isNotEmpty=function(strVal){if(strVal==''||strVal==null||strVal==undefined){return false}else{return true}};
var res_img = $("img.res");
var num = res_img.length;
for(i = 0 ; i< num ; i++){
	var img_src = isNotEmpty(res_img.eq(i).attr("data-xb")) ? res_img.eq(i).attr("data-xb") : isNotEmpty(res_img.eq(i).attr("data-xm")) ? res_img.eq(i).attr("data-xm") : isNotEmpty(res_img.eq(i).attr("data-xs")) ? res_img.eq(i).attr("data-xs") : isNotEmpty(res_img.eq(i).attr("data-xm")) ? res_img.eq(i).attr("data-xm") : "";
	var w_width = $(window).width();
if (w_width < 1200 && w_width > 999 && isNotEmpty(res_img.attr("data-xm"))) {
    img_src = res_img.eq(i).attr("data-xm")
}
if (w_width < 1000 && w_width > 759 && isNotEmpty(res_img.attr("data-xs"))) {
    img_src = res_img.eq(i).attr("data-xs")
}
if (w_width < 760 && isNotEmpty(res_img.attr("data-xl"))) {
    img_src = res_img.eq(i).attr("data-xl")
}
if (isNotEmpty(img_src)) {
    res_img.eq(i).attr('src', img_src)
}
}

})
