/*$(window).scroll(function(){
			var a=$(window).scrollTop();
			if(a>31){
			$(".nb-d ").css({"margin-top":"64px"});
		}else{
			$(".nb-d ").css({"margin-top":"0px"});
		}
		});*/
		$(".nav-ex").parent().hover(function(){
			$(this).find('.nav-ex').stop().slideDown("fast");  
		},function(){
			$(this).find('.nav-ex').stop().slideUp("fast");
		});
		$(".nav-ex").parent().hover(function(){
			if($(window).width()<1183){
				$(".nav-ex").css("display","none");
			}
		});
		$(window).resize(function(event) {
			$(".nav-ex1").width(document.body.clientWidth);
		});

		$(".lang_icon").mouseenter(function(){
			w = $(window).width();

			if(w >= 760){
				$('.lang_list').css('display','inline-block').show(); 
			}
			 
		});
		$('.lang_list').mouseleave(function(){
			$(this).hide();
		});

		if($(window).width() > 1200){
			$('.product_show').hover(function(){
			$('.drop_list').show();

		},function(){
			$('.drop_list').hide();
		})
	
		$('.drop_list').mouseenter(function(){
			
			$(this).show();
		});
		
		$('.drop_list').mouseover(function(){
			$('.drop_list').show();
		});
		$('.drop_list').mouseleave(function(){
			$(this).hide();
		
		});
		}

		$(window).scroll(function(){
			if($(window).scrollTop() >= 70){
				$('.drop_list').css('top','59px');
			}else{
				$('.drop_list').css('top','130px');
			}
		});

		if($(window).width()<760){
			$(".fixed").removeClass("fixed");
		}