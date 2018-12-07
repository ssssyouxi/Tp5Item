$(function  () {
	// 		var obj=$(window).width();
	// 		if(obj < 983){
	// 		$(".contact_fl").addClass("clearfix");
	// 	}else{
	// 		$(".contact_fl").removeClass('clearfix');
	// 	};
	// $(window).resize(function(){
	// 	var obj=$(window).width();
	// 	if(obj < 983){
	// 		$(".contact_fl").addClass("clearfix");
	// 	}else{
	// 		$(".contact_fl").removeClass('clearfix');
	// 	};
	// })
	
			

	$("#contact_che").change(function() {
		if ($("#contact_inp").attr("disabled")) {
			$("#contact_inp").removeAttr('disabled');
		}else{
			$("#contact_inp").attr("disabled",true);
		}
	});
})
function test(){
		document.msgForm.reset(); 
	}


function check(){
	if($(".form-group input[name='userName']").val()==''){
	alert("Please input your name!");
	return false;
}else{
	if($(".form-group input[name='userEMail']").val()==''){
		alert("Please input your E-Mail!");
		return false;
	}else{
		if($("#product").val()==''){
				alert("Please select your product!");
				return false;	
		}else{
			if($('#mType').val() == ''){
					alert("Please select your material(s)!");
					return false;
			}else{

					var other  = $('#f_other').val()? true :false;
					var radio=$("input[name='mCap']");
				var flag=0;
				for(var i=0;i<radio.length;i++){
					if(radio[i].checked ||  other){
						flag+=1;
					}
				}
				if(flag==0){

				alert("Please select your capacity!");
				return false;
			}else{

		// 		if($("#textarea").val()==""){
		// 	alert("Please input your message!");
		// 	return false;
		// }else{
		// 		return true;
		// 	}
				return true
				}
			}
			}
		}
	}

}